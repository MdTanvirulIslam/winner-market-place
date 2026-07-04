<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\Http;
use Throwable;

// Server-to-server client for the License Manager's provisioning API
// (POST /api/licenses — documented in API.md of winner-license-application).
// The token comes from config only and must never be logged or rendered.
class LicenseManager
{
    /**
     * Create a license for the order and store the result on it.
     * Idempotent: an order that already has a license key is left untouched,
     * so retries never create duplicate licenses.
     */
    public function provision(Order $order): bool
    {
        if ($order->license_key) {
            $order->update(['provisioning_status' => 'provisioned', 'provisioning_error' => null]);

            return true;
        }

        $baseUrl = config('marketplace.license_manager.url');
        $token = config('marketplace.license_manager.token');

        if (blank($baseUrl) || blank($token)) {
            return $this->fail($order, 'License Manager URL or API token is not configured.');
        }

        try {
            $response = Http::withToken($token)
                ->acceptJson()
                ->timeout(20)
                ->post(rtrim($baseUrl, '/') . '/api/licenses', [
                    'product' => $order->product_slug,
                    'customer_name' => $order->customer_name,
                    'customer_email' => $order->customer_email,
                    // The License Manager sends the credentials email itself,
                    // so licensing emails look identical for every sales channel.
                    'send_email' => true,
                ]);
        } catch (Throwable $e) {
            report($e);

            return $this->fail($order, 'Connection failed: ' . substr($e->getMessage(), 0, 200));
        }

        if ($response->status() !== 201 || ! $response->json('created')) {
            return $this->fail($order, sprintf(
                'HTTP %d: %s',
                $response->status(),
                substr($response->json('message') ?? $response->body(), 0, 200)
            ));
        }

        $order->update([
            'license_key' => $response->json('license.key'),
            'delivery_url' => $response->json('delivery_url'),
            'provisioning_status' => 'provisioned',
            'provisioning_error' => null,
        ]);

        return true;
    }

    private function fail(Order $order, string $message): bool
    {
        $order->update([
            'provisioning_status' => 'failed',
            'provisioning_error' => substr($message, 0, 500),
        ]);

        return false;
    }
}
