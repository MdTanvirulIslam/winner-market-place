<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use App\Models\Order;
use Illuminate\Support\Collection;
use Illuminate\View\View;

// Deeper sales numbers than the dashboard: lifetime KPIs, product
// performance, and coupon effectiveness. All figures count paid +
// delivered orders (money actually received).
class AnalyticsController extends Controller
{
    private const REVENUE_STATUSES = ['paid', 'delivered'];

    public function index(): View
    {
        $revenueTotal = (float) Order::whereIn('status', self::REVENUE_STATUSES)->sum('amount');
        $orderCount = Order::whereIn('status', self::REVENUE_STATUSES)->count();

        return view('admin.analytics', [
            'revenueTotal' => $revenueTotal,
            'orderCount' => $orderCount,
            'averageOrder' => $orderCount > 0 ? $revenueTotal / $orderCount : 0,
            'refundCount' => Order::where('status', 'refunded')->count(),
            'refundedAmount' => (float) Order::where('status', 'refunded')->sum('amount'),
            'discountTotal' => (float) Order::whereIn('status', self::REVENUE_STATUSES)->sum('discount_amount'),
            'chartData' => [
                'weekly' => $this->dailyRevenue(),
                'monthly' => $this->monthlyRevenue(),
            ],
            'productPerformance' => $this->productPerformance(),
            'couponPerformance' => $this->couponPerformance(),
        ]);
    }

    private function revenueBetween($from, $to): float
    {
        return (float) Order::whereIn('status', self::REVENUE_STATUSES)
            ->whereBetween('paid_at', [$from, $to])
            ->sum('amount');
    }

    private function dailyRevenue(): array
    {
        $labels = [];
        $values = [];
        $display = [];

        foreach (range(29, 0) as $daysAgo) {
            $day = now()->subDays($daysAgo);
            $revenue = $this->revenueBetween($day->copy()->startOfDay(), $day->copy()->endOfDay());

            $labels[] = $day->format('d');
            $values[] = $revenue;
            $display[] = $day->format('D d M') . ': ' . format_price($revenue);
        }

        return compact('labels', 'values', 'display');
    }

    private function monthlyRevenue(): array
    {
        $labels = [];
        $values = [];
        $display = [];

        foreach (range(11, 0) as $monthsAgo) {
            $month = now()->subMonthsNoOverflow($monthsAgo);
            $revenue = $this->revenueBetween($month->copy()->startOfMonth(), $month->copy()->endOfMonth());

            $labels[] = $month->format('M');
            $values[] = $revenue;
            $display[] = $month->format('M Y') . ': ' . format_price($revenue);
        }

        return compact('labels', 'values', 'display');
    }

    private function productPerformance(): Collection
    {
        $rows = Order::whereIn('status', self::REVENUE_STATUSES)
            ->selectRaw('product_name, product_slug, COUNT(*) as sales_count, SUM(amount) as revenue, SUM(discount_amount) as discounts')
            ->groupBy('product_name', 'product_slug')
            ->orderByDesc('revenue')
            ->get();

        $total = (float) $rows->sum('revenue') ?: 1.0;

        return $rows->map(function ($row) use ($total) {
            $row->share = round(((float) $row->revenue / $total) * 100, 1);

            return $row;
        });
    }

    private function couponPerformance(): Collection
    {
        $usage = Order::whereIn('status', self::REVENUE_STATUSES)
            ->whereNotNull('coupon_code')
            ->selectRaw('coupon_code, COUNT(*) as order_count, SUM(discount_amount) as discount_total, SUM(amount) as revenue')
            ->groupBy('coupon_code')
            ->orderByDesc('order_count')
            ->get()
            ->keyBy('coupon_code');

        // Include coupons that exist but were never used on a completed order.
        return Coupon::orderBy('code')->get()->map(function (Coupon $coupon) use ($usage) {
            $row = $usage->get($coupon->code);

            return (object) [
                'code' => $coupon->code,
                'redeemable' => $coupon->isRedeemable(),
                'order_count' => (int) ($row->order_count ?? 0),
                'discount_total' => (float) ($row->discount_total ?? 0),
                'revenue' => (float) ($row->revenue ?? 0),
            ];
        });
    }
}
