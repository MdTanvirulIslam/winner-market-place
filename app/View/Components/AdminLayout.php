<?php

namespace App\View\Components;

use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\View\Component;
use Illuminate\View\View;

class AdminLayout extends Component
{
    public Collection $notifications;

    public int $notificationCount;

    public function __construct(public ?string $title = null)
    {
        $this->notifications = $this->buildFeed();

        // Actionable items only: money waiting to be confirmed or delivered.
        $this->notificationCount = Order::where('status', 'pending')->count()
            + Order::where('provisioning_status', 'failed')->count();
    }

    public function render(): View
    {
        return view('layouts.admin');
    }

    /**
     * A lightweight live feed — no notifications table, just the latest
     * store events, merged and capped.
     */
    private function buildFeed(): Collection
    {
        $pending = Order::where('status', 'pending')->latest()->take(5)->get()
            ->map(fn (Order $order) => (object) [
                'icon' => 'shopping-cart',
                'tint' => 'background:rgba(245,158,11,0.1);color:#d97706;',
                'title' => 'New order ' . $order->order_no . ' — ' . $order->product_name,
                'url' => route('admin.orders.show', $order),
                'time' => $order->created_at,
            ]);

        $failed = Order::where('provisioning_status', 'failed')->latest('paid_at')->take(5)->get()
            ->map(fn (Order $order) => (object) [
                'icon' => 'triangle-alert',
                'tint' => 'background:rgba(239,68,68,0.1);color:#dc2626;',
                'title' => 'Provisioning failed — ' . $order->order_no,
                'url' => route('admin.orders.show', $order),
                'time' => $order->paid_at ?? $order->updated_at,
            ]);

        $customers = User::where('role', 'customer')
            ->where('created_at', '>=', now()->subDays(7))
            ->latest()->take(5)->get()
            ->map(fn (User $user) => (object) [
                'icon' => 'user-plus',
                'tint' => 'background:rgba(34,197,94,0.1);color:#16a34a;',
                'title' => $user->name . ' created an account',
                'url' => route('admin.customers.index', ['q' => $user->email]),
                'time' => $user->created_at,
            ]);

        return $pending->concat($failed)->concat($customers)
            ->sortByDesc('time')
            ->take(8)
            ->values();
    }
}
