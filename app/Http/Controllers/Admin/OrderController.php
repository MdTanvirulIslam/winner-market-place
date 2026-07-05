<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\WithDataTable;
use App\Http\Controllers\Controller;
use App\Mail\OrderPlacedMail;
use App\Models\Order;
use App\Models\Product;
use App\Models\Setting;
use App\Models\User;
use App\Services\OrderFlow;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Throwable;

class OrderController extends Controller
{
    use WithDataTable;

    public function __construct(private OrderFlow $orderFlow)
    {
    }

    public function index(Request $request): View
    {
        $query = Order::with('product')
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->status))
            ->when($request->boolean('provisioning_failed'), fn ($query) => $query->where('provisioning_status', 'failed'))
            ->when($request->filled('q'), fn ($query) => $query->where(function ($query) use ($request) {
                $query->where('order_no', 'like', '%' . $request->q . '%')
                    ->orWhere('customer_name', 'like', '%' . $request->q . '%')
                    ->orWhere('customer_email', 'like', '%' . $request->q . '%');
            }));

        $orders = $this->dataTable(
            $request,
            $query,
            ['order_no', 'customer_name', 'amount', 'status', 'created_at'],
            fn ($query) => $query->latest()
        );

        return view('admin.orders.index', [
            'orders' => $orders,
            'failedCount' => Order::where('provisioning_status', 'failed')->count(),
        ]);
    }

    public function create(): View
    {
        return view('admin.orders.create', [
            'products' => Product::orderBy('name')->get(),
        ]);
    }

    // Manual sale (WhatsApp / bank transfer): create the order here, then
    // Mark as Paid — from that point the flow is identical to a gateway sale.
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'product_id' => 'required|exists:products,id',
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|string|lowercase|email|max:255',
            'customer_phone' => 'nullable|string|max:30',
            'amount' => 'nullable|numeric|min:0',
        ]);

        $product = Product::findOrFail($data['product_id']);

        // Give the customer an account so their downloads have a home. They
        // can use "Forgot password" to set their own password.
        $user = User::where('email', $data['customer_email'])->first();
        $created = false;

        if (! $user) {
            $user = User::create([
                'name' => $data['customer_name'],
                'email' => $data['customer_email'],
                'password' => Hash::make(Str::random(40)),
                'role' => 'customer',
            ]);
            $created = true;
        }

        $order = Order::place([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'product_name' => $product->name,
            'product_slug' => $product->slug,
            'customer_name' => $data['customer_name'],
            'customer_email' => $data['customer_email'],
            'customer_phone' => $data['customer_phone'] ?? null,
            'amount' => filled($data['amount'] ?? null) ? $data['amount'] : $product->effectivePrice(),
            'currency' => Setting::current()->currency,
            'status' => 'pending',
            'payment_method' => 'manual',
        ]);

        try {
            Mail::to($order->customer_email)->send(new OrderPlacedMail($order));
        } catch (Throwable $e) {
            report($e);
        }

        $message = 'Order ' . $order->order_no . ' created.';
        if ($created) {
            $message .= ' A customer account was created for ' . $user->email . ' — they can set a password via "Forgot password".';
        }

        return redirect()->route('admin.orders.show', $order)->with('success', $message);
    }

    public function show(Order $order): View
    {
        $order->load(['user', 'product', 'downloads.release']);

        return view('admin.orders.show', compact('order'));
    }

    public function markPaid(Order $order): RedirectResponse
    {
        if (! $order->isPending()) {
            return back()->with('error', 'Only pending orders can be marked as paid.');
        }

        $this->orderFlow->markPaid($order);

        return back()->with(
            $order->fresh()->isDelivered() ? 'success' : 'warning',
            $order->fresh()->isDelivered()
                ? 'Payment recorded — license provisioned and delivery email sent.'
                : 'Payment recorded, but license provisioning failed. Fix the problem and press Retry Provisioning.'
        );
    }

    public function retryProvisioning(Order $order): RedirectResponse
    {
        if ($order->status !== 'paid') {
            return back()->with('error', 'Retry is only available for paid orders awaiting delivery.');
        }

        $delivered = $this->orderFlow->attemptDelivery($order);

        return back()->with(
            $delivered ? 'success' : 'error',
            $delivered ? 'License provisioned — order delivered.' : 'Provisioning failed again: ' . $order->fresh()->provisioning_error
        );
    }

    public function refund(Order $order): RedirectResponse
    {
        if (! in_array($order->status, ['paid', 'delivered'], true)) {
            return back()->with('error', 'Only paid or delivered orders can be refunded.');
        }

        $this->orderFlow->markRefunded($order);

        return back()->with('warning', 'Order marked refunded — downloads are blocked. Remember to suspend the license in the License Manager.');
    }

    public function cancel(Order $order): RedirectResponse
    {
        if (! $order->isPending()) {
            return back()->with('error', 'Only pending orders can be cancelled.');
        }

        $this->orderFlow->cancel($order);

        return back()->with('success', 'Order cancelled.');
    }
}
