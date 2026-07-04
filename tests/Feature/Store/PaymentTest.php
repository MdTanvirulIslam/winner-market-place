<?php

namespace Tests\Feature\Store;

use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class PaymentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Mail::fake();

        // Self-invented test values — never real credentials.
        config([
            'marketplace.sslcommerz.store_id' => 'teststore001',
            'marketplace.sslcommerz.store_password' => 'teststore001@ssl',
            'marketplace.sslcommerz.sandbox' => true,
            'marketplace.license_manager.url' => 'https://license-manager.test',
            'marketplace.license_manager.token' => 'test-token-for-phpunit-only',
        ]);
    }

    private function pendingOrder(): Order
    {
        return Order::factory()->create(['customer_phone' => '01712345678']);
    }

    private function fakeGateway(array $overrides = []): void
    {
        Http::fake(array_merge([
            'sandbox.sslcommerz.com/gwprocess/v4/api.php' => Http::response([
                'status' => 'SUCCESS',
                'sessionkey' => 'TESTSESSION123',
                'GatewayPageURL' => 'https://sandbox.sslcommerz.com/EasyCheckOut/testsession123',
            ]),
            'license-manager.test/api/licenses' => Http::response([
                'created' => true,
                'license' => ['key' => 'WD-TESTKEY-999'],
                'delivery_url' => 'https://license-manager.test/claim/xyz',
                'email' => 'sent',
            ], 201),
        ], $overrides));
    }

    private function validatorResponse(Order $order, array $overrides = []): array
    {
        return array_merge([
            'status' => 'VALID',
            'tran_id' => $order->sslcz_tran_id,
            'val_id' => 'VAL123456789',
            'amount' => number_format((float) $order->amount, 2, '.', ''),
            'currency_type' => $order->currency,
            'currency_amount' => number_format((float) $order->amount, 2, '.', ''),
        ], $overrides);
    }

    public function test_start_redirects_to_the_gateway_and_stores_tran_id(): void
    {
        $this->fakeGateway();
        $order = $this->pendingOrder();

        $response = $this->actingAs($order->user)->post(route('payment.start', $order));

        $response->assertRedirect('https://sandbox.sslcommerz.com/EasyCheckOut/testsession123');

        $order->refresh();
        $this->assertNotNull($order->sslcz_tran_id);
        $this->assertStringStartsWith($order->order_no . '-', $order->sslcz_tran_id);

        Http::assertSent(function ($request) use ($order) {
            return str_contains($request->url(), 'gwprocess/v4/api.php')
                && $request['tran_id'] === $order->fresh()->sslcz_tran_id
                && $request['total_amount'] === number_format((float) $order->amount, 2, '.', '')
                && $request['product_profile'] === 'non-physical-goods';
        });
    }

    public function test_start_requires_configuration(): void
    {
        config(['marketplace.sslcommerz.store_id' => null]);
        $order = $this->pendingOrder();

        $this->actingAs($order->user)->post(route('payment.start', $order))
            ->assertRedirect(route('account.orders.show', $order))
            ->assertSessionHas('error');

        $this->assertNull($order->fresh()->sslcz_tran_id);
    }

    public function test_only_the_owner_can_start_payment(): void
    {
        $this->fakeGateway();
        $order = $this->pendingOrder();
        $stranger = User::factory()->create(['role' => 'customer']);

        $this->actingAs($stranger)->post(route('payment.start', $order))->assertNotFound();
    }

    public function test_validated_success_callback_delivers_the_order(): void
    {
        $order = $this->pendingOrder();
        $order->update(['sslcz_tran_id' => $order->order_no . '-ABC123']);

        $this->fakeGateway([
            'sandbox.sslcommerz.com/validator/*' => Http::response($this->validatorResponse($order)),
        ]);

        $response = $this->post(route('payment.success'), [
            'tran_id' => $order->sslcz_tran_id,
            'val_id' => 'VAL123456789',
            'status' => 'VALID',
        ]);

        $order->refresh();
        $this->assertSame('delivered', $order->status);
        $this->assertSame('sslcommerz', $order->payment_method);
        $this->assertSame('VAL123456789', $order->sslcz_val_id);
        $this->assertSame('WD-TESTKEY-999', $order->license_key);

        $response->assertRedirect(route('account.orders.show', $order));
    }

    public function test_redirect_alone_is_never_trusted(): void
    {
        $order = $this->pendingOrder();
        $order->update(['sslcz_tran_id' => $order->order_no . '-ABC123']);

        // Validator says the payment is NOT valid, whatever the POST claims.
        $this->fakeGateway([
            'sandbox.sslcommerz.com/validator/*' => Http::response(['status' => 'INVALID_TRANSACTION']),
        ]);

        $this->post(route('payment.success'), [
            'tran_id' => $order->sslcz_tran_id,
            'val_id' => 'VAL-FORGED',
            'status' => 'VALID',
        ]);

        $this->assertSame('pending', $order->fresh()->status);
    }

    public function test_amount_tampering_is_rejected(): void
    {
        $order = $this->pendingOrder(); // amount 10000
        $order->update(['sslcz_tran_id' => $order->order_no . '-ABC123']);

        $this->fakeGateway([
            'sandbox.sslcommerz.com/validator/*' => Http::response($this->validatorResponse($order, [
                'amount' => '10.00',
                'currency_amount' => '10.00',
            ])),
        ]);

        $this->post(route('payment.success'), [
            'tran_id' => $order->sslcz_tran_id,
            'val_id' => 'VAL123456789',
        ]);

        $this->assertSame('pending', $order->fresh()->status);
    }

    public function test_ipn_is_idempotent_with_the_success_redirect(): void
    {
        $order = $this->pendingOrder();
        $order->update(['sslcz_tran_id' => $order->order_no . '-ABC123']);

        $this->fakeGateway([
            'sandbox.sslcommerz.com/validator/*' => Http::response($this->validatorResponse($order)),
        ]);

        $payload = ['tran_id' => $order->sslcz_tran_id, 'val_id' => 'VAL123456789'];

        $this->post(route('payment.success'), $payload);
        $this->post(route('payment.ipn'), $payload)->assertOk();

        $order->refresh();
        $this->assertSame('delivered', $order->status);

        // Exactly one license was provisioned despite two callbacks.
        Http::assertSentCount(2); // 1 validator call + 1 license call
    }

    public function test_fail_and_cancel_keep_the_order_pending(): void
    {
        $order = $this->pendingOrder();
        $order->update(['sslcz_tran_id' => $order->order_no . '-ABC123']);

        $this->post(route('payment.fail'), ['tran_id' => $order->sslcz_tran_id])
            ->assertRedirect(route('account.orders.show', $order));
        $this->assertSame('pending', $order->fresh()->status);

        $this->post(route('payment.cancel'), ['tran_id' => $order->sslcz_tran_id])
            ->assertRedirect(route('account.orders.show', $order));
        $this->assertSame('pending', $order->fresh()->status);
    }

    public function test_unknown_transactions_are_handled_gracefully(): void
    {
        $this->fakeGateway();

        $this->post(route('payment.success'), ['tran_id' => 'WM-0000-FAKE', 'val_id' => 'VAL1'])
            ->assertRedirect(route('home'));
    }
}
