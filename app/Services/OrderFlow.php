<?php

namespace App\Services;

use App\Mail\OrderCompletedMail;
use App\Models\Order;
use Illuminate\Support\Facades\Mail;
use Throwable;

// The order lifecycle: pending -> paid -> delivered (or failed/cancelled/
// refunded). Payment and delivery are deliberately separate steps so a
// License Manager outage never leaves money in an ambiguous state — the
// order stays paid with a provisioning-failed flag until Retry succeeds.
class OrderFlow
{
    public function __construct(private LicenseManager $licenseManager)
    {
    }

    /**
     * Mark a pending order as paid and immediately attempt delivery.
     */
    public function markPaid(Order $order, string $paymentMethod = 'manual'): void
    {
        if (! $order->isPending()) {
            return;
        }

        $order->update([
            'status' => 'paid',
            'payment_method' => $paymentMethod,
            'paid_at' => now(),
        ]);

        $this->attemptDelivery($order);
    }

    /**
     * Provision the license and unlock downloads. Safe to call repeatedly —
     * the provisioning call is idempotent and delivered orders are skipped.
     */
    public function attemptDelivery(Order $order): bool
    {
        if ($order->isDelivered()) {
            return true;
        }

        if ($order->status !== 'paid') {
            return false;
        }

        if (! $this->licenseManager->provision($order)) {
            return false;
        }

        $order->update([
            'status' => 'delivered',
            'delivered_at' => now(),
        ]);

        try {
            Mail::to($order->customer_email)->send(new OrderCompletedMail($order));
        } catch (Throwable $e) {
            // Delivery already succeeded; the customer still has the
            // credentials email from the License Manager.
            report($e);
        }

        return true;
    }

    /**
     * Refunds block further downloads. The money itself is refunded outside
     * the app (SSLCommerz panel / by hand), and the license should be
     * suspended in the License Manager afterwards.
     */
    public function markRefunded(Order $order): void
    {
        if (! in_array($order->status, ['paid', 'delivered'], true)) {
            return;
        }

        $order->update([
            'status' => 'refunded',
            'refunded_at' => now(),
        ]);
    }

    public function cancel(Order $order): void
    {
        if ($order->isPending()) {
            $order->update(['status' => 'cancelled']);
        }
    }
}
