<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AccountController extends Controller
{
    public function orders(Request $request): View
    {
        $orders = Order::where('user_id', $request->user()->id)
            ->latest()
            ->paginate(10);

        return view('account.orders', compact('orders'));
    }

    public function order(Request $request, Order $order): View
    {
        abort_unless($order->user_id === $request->user()->id, 404);

        $order->load(['product.releases']);

        return view('account.order', [
            'order' => $order,
            'setting' => Setting::current(),
        ]);
    }

    public function downloads(Request $request): View
    {
        $orders = Order::where('user_id', $request->user()->id)
            ->where('status', 'delivered')
            ->with(['product.releases', 'product.images'])
            ->latest('delivered_at')
            ->get();

        return view('account.downloads', compact('orders'));
    }
}
