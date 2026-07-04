<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class DashboardController extends Controller
{
    private const REVENUE_STATUSES = ['paid', 'delivered'];

    public function index(): View
    {
        $now = now();

        $revenueThisMonth = $this->revenueBetween($now->copy()->startOfMonth(), $now);
        $revenueLastMonth = $this->revenueBetween(
            $now->copy()->subMonthNoOverflow()->startOfMonth(),
            $now->copy()->subMonthNoOverflow()->endOfMonth()
        );

        $ordersThisMonth = $this->ordersBetween($now->copy()->startOfMonth(), $now);
        $ordersLastMonth = $this->ordersBetween(
            $now->copy()->subMonthNoOverflow()->startOfMonth(),
            $now->copy()->subMonthNoOverflow()->endOfMonth()
        );

        return view('admin.dashboard', [
            'customerCount' => User::where('role', 'customer')->count(),
            'productCount' => Product::count(),
            'publishedCount' => Product::published()->count(),
            'orderCount' => Order::whereIn('status', self::REVENUE_STATUSES)->count(),
            'pendingCount' => Order::where('status', 'pending')->count(),
            'failedProvisioningCount' => Order::where('provisioning_status', 'failed')->count(),
            'revenueThisMonth' => $revenueThisMonth,
            'revenueTrend' => $this->trend($revenueThisMonth, $revenueLastMonth),
            'ordersThisMonth' => $ordersThisMonth,
            'ordersTrend' => $this->trend($ordersThisMonth, $ordersLastMonth),
            'chartData' => [
                'weekly' => $this->weeklyRevenue(),
                'monthly' => $this->monthlyRevenue(),
            ],
            'bestSellers' => $this->bestSellers(),
            'recentOrders' => Order::latest()->take(6)->get(),
        ]);
    }

    private function revenueBetween($from, $to): float
    {
        return (float) Order::whereIn('status', self::REVENUE_STATUSES)
            ->whereBetween('paid_at', [$from, $to])
            ->sum('amount');
    }

    private function ordersBetween($from, $to): int
    {
        return Order::whereIn('status', self::REVENUE_STATUSES)
            ->whereBetween('paid_at', [$from, $to])
            ->count();
    }

    /**
     * Percent change vs the previous period, or null when there is no
     * previous data to compare against.
     */
    private function trend(float|int $current, float|int $previous): ?float
    {
        if ($previous <= 0) {
            return null;
        }

        return round((($current - $previous) / $previous) * 100, 1);
    }

    private function weeklyRevenue(): array
    {
        $labels = [];
        $values = [];
        $display = [];

        foreach (range(6, 0) as $daysAgo) {
            $day = now()->subDays($daysAgo);
            $revenue = $this->revenueBetween($day->copy()->startOfDay(), $day->copy()->endOfDay());

            $labels[] = $day->format('D');
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
            $revenue = $this->revenueBetween(
                $month->copy()->startOfMonth(),
                $month->copy()->endOfMonth()
            );

            $labels[] = $month->format('M');
            $values[] = $revenue;
            $display[] = $month->format('M Y') . ': ' . format_price($revenue);
        }

        return compact('labels', 'values', 'display');
    }

    private function bestSellers(): Collection
    {
        $sales = Order::whereIn('status', self::REVENUE_STATUSES)
            ->selectRaw('product_name, product_slug, COUNT(*) as sales_count, SUM(amount) as revenue')
            ->groupBy('product_name', 'product_slug')
            ->orderByDesc('sales_count')
            ->take(5)
            ->get();

        $max = (int) $sales->max('sales_count') ?: 1;

        return $sales->map(function ($row) use ($max) {
            $row->percent = (int) round(($row->sales_count / $max) * 100);

            return $row;
        });
    }
}
