<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

// SSLCommerz hosted-checkout client (v4 gateway process + validation API).
// The store password comes from config only and is never logged or rendered.
// Payment truth comes exclusively from validate() — success redirects and
// IPN posts are treated as hints, never as proof.
class SslCommerz
{
    public function isConfigured(): bool
    {
        return filled(config('marketplace.sslcommerz.store_id'))
            && filled(config('marketplace.sslcommerz.store_password'));
    }

    private function baseUrl(): string
    {
        return config('marketplace.sslcommerz.sandbox')
            ? 'https://sandbox.sslcommerz.com'
            : 'https://securepay.sslcommerz.com';
    }

    /**
     * Open a hosted payment session for the order and return the gateway
     * page URL to redirect the customer to, or null on failure. A fresh
     * tran_id is issued per attempt and stored on the order.
     */
    public function createSession(Order $order): ?string
    {
        $tranId = $order->order_no . '-' . strtoupper(Str::random(6));

        $response = Http::asForm()->timeout(20)->post($this->baseUrl() . '/gwprocess/v4/api.php', [
            'store_id' => config('marketplace.sslcommerz.store_id'),
            'store_passwd' => config('marketplace.sslcommerz.store_password'),
            'total_amount' => number_format((float) $order->amount, 2, '.', ''),
            'currency' => $order->currency,
            'tran_id' => $tranId,
            'success_url' => route('payment.success'),
            'fail_url' => route('payment.fail'),
            'cancel_url' => route('payment.cancel'),
            'ipn_url' => route('payment.ipn'),
            'emi_option' => 0,
            'cus_name' => $order->customer_name,
            'cus_email' => $order->customer_email,
            'cus_add1' => 'N/A',
            'cus_city' => 'N/A',
            'cus_country' => 'Bangladesh',
            'cus_phone' => $order->customer_phone ?: '0000000000',
            'shipping_method' => 'NO',
            'product_name' => $order->product_name,
            'product_category' => 'Software',
            'product_profile' => 'non-physical-goods',
        ]);

        if (! $response->ok() || strtoupper((string) $response->json('status')) !== 'SUCCESS') {
            report(new \RuntimeException(
                'SSLCommerz session failed for ' . $order->order_no . ': '
                . substr((string) ($response->json('failedreason') ?? $response->body()), 0, 200)
            ));

            return null;
        }

        $order->update(['sslcz_tran_id' => $tranId]);

        return $response->json('GatewayPageURL') ?: null;
    }

    /**
     * Server-side validation — the only source of payment truth.
     * Returns the validated payload, or null when the payment is not valid.
     */
    public function validate(string $valId): ?array
    {
        $response = Http::timeout(20)->get($this->baseUrl() . '/validator/api/validationserverAPI.php', [
            'val_id' => $valId,
            'store_id' => config('marketplace.sslcommerz.store_id'),
            'store_passwd' => config('marketplace.sslcommerz.store_password'),
            'format' => 'json',
        ]);

        if (! $response->ok()) {
            return null;
        }

        $data = $response->json();

        if (! in_array(strtoupper((string) ($data['status'] ?? '')), ['VALID', 'VALIDATED'], true)) {
            return null;
        }

        return $data;
    }

    /**
     * Cross-check a validated payload against the order it claims to pay.
     */
    public function matchesOrder(array $validated, Order $order): bool
    {
        $amountOk = abs(((float) ($validated['amount'] ?? 0)) - (float) $order->amount) < 0.01
            || abs(((float) ($validated['currency_amount'] ?? 0)) - (float) $order->amount) < 0.01;

        return ($validated['tran_id'] ?? null) === $order->sslcz_tran_id
            && strtoupper((string) ($validated['currency_type'] ?? $validated['currency'] ?? '')) === strtoupper($order->currency)
            && $amountOk;
    }
}
