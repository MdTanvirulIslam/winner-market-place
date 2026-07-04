<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    public function show(Product $product): View
    {
        abort_unless($product->isPublished(), 404);

        return view('store.checkout', [
            'product' => $product->load('images'),
            'setting' => Setting::current(),
        ]);
    }

    public function store(Request $request, Product $product): RedirectResponse
    {
        abort_unless($product->isPublished(), 404);

        $user = $request->user();

        // Reuse an open pending order for the same product instead of
        // piling up duplicates when the customer revisits checkout.
        $existing = Order::where('user_id', $user->id)
            ->where('product_id', $product->id)
            ->where('status', 'pending')
            ->first();

        if ($existing) {
            return redirect()->route('account.orders.show', $existing)
                ->with('info', 'You already have an open order for this product.');
        }

        $order = Order::place([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'product_name' => $product->name,
            'product_slug' => $product->slug,
            'customer_name' => $user->name,
            'customer_email' => $user->email,
            'amount' => $product->effectivePrice(),
            'currency' => Setting::current()->currency,
            'status' => 'pending',
            'payment_method' => 'manual',
        ]);

        return redirect()->route('account.orders.show', $order)
            ->with('success', 'Order placed! Follow the payment instructions to complete your purchase.');
    }
}
