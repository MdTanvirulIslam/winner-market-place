<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Mail\OrderPlacedMail;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\Product;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;
use Throwable;

class CheckoutController extends Controller
{
    public function show(Request $request, Product $product): View
    {
        abort_unless($product->isPublished(), 404);

        return view('store.checkout', [
            'product' => $product->load('images'),
            'setting' => Setting::current(),
            'coupon' => $this->sessionCoupon($request),
        ]);
    }

    public function store(Request $request, Product $product): RedirectResponse
    {
        abort_unless($product->isPublished(), 404);

        $data = $request->validate([
            'customer_phone' => 'required|string|max:30|regex:/^[0-9+\-\s]{6,}$/',
        ]);

        $user = $request->user();

        // Reuse an open pending order for the same product instead of
        // piling up duplicates when the customer revisits checkout.
        $existing = Order::where('user_id', $user->id)
            ->where('product_id', $product->id)
            ->where('status', 'pending')
            ->first();

        if ($existing) {
            $existing->update(['customer_phone' => $data['customer_phone']]);

            return redirect()->route('account.orders.show', $existing)
                ->with('info', 'You already have an open order for this product.');
        }

        $price = $product->effectivePrice();
        $coupon = $this->sessionCoupon($request);
        $discount = $coupon?->discountFor($price) ?? 0.0;

        $order = Order::place([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'product_name' => $product->name,
            'product_slug' => $product->slug,
            'customer_name' => $user->name,
            'customer_email' => $user->email,
            'customer_phone' => $data['customer_phone'],
            'amount' => round($price - $discount, 2),
            'coupon_code' => $coupon?->code,
            'discount_amount' => $discount,
            'currency' => Setting::current()->currency,
            'status' => 'pending',
            'payment_method' => 'manual',
        ]);

        if ($coupon) {
            $coupon->increment('used_count');
            $request->session()->forget(CouponController::SESSION_KEY);
        }

        try {
            Mail::to($order->customer_email)->send(new OrderPlacedMail($order));
        } catch (Throwable $e) {
            report($e); // the order page shows the same instructions anyway
        }

        return redirect()->route('account.orders.show', $order)
            ->with('success', 'Order placed! Follow the payment instructions to complete your purchase.');
    }

    /**
     * The coupon currently applied in this session, if it is still
     * redeemable — silently dropped otherwise.
     */
    private function sessionCoupon(Request $request): ?Coupon
    {
        $coupon = Coupon::findByCode($request->session()->get(CouponController::SESSION_KEY));

        if ($coupon && $coupon->isRedeemable()) {
            return $coupon;
        }

        $request->session()->forget(CouponController::SESSION_KEY);

        return null;
    }
}
