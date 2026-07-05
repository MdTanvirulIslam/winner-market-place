<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

// Applies/removes a coupon during checkout. The code is kept in the session
// and consumed (re-validated) when the order is placed.
class CouponController extends Controller
{
    public const SESSION_KEY = 'checkout_coupon';

    public function apply(Request $request, Product $product): RedirectResponse
    {
        abort_unless($product->isPublished(), 404);

        $data = $request->validate([
            'code' => 'required|string|max:30',
        ]);

        $coupon = Coupon::findByCode($data['code']);

        if (! $coupon) {
            return back()->withErrors(['code' => 'Unknown coupon code.']);
        }

        if (! $coupon->isRedeemable()) {
            return back()->withErrors(['code' => $coupon->rejectionReason()]);
        }

        $request->session()->put(self::SESSION_KEY, $coupon->code);

        return back()->with('success', "Coupon {$coupon->code} applied.");
    }

    public function remove(Request $request, Product $product): RedirectResponse
    {
        $request->session()->forget(self::SESSION_KEY);

        return back()->with('info', 'Coupon removed.');
    }
}
