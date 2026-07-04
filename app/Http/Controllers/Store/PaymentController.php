<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\OrderFlow;
use App\Services\SslCommerz;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function __construct(
        private SslCommerz $sslCommerz,
        private OrderFlow $orderFlow,
    ) {
    }

    /**
     * Customer-initiated: open a hosted SSLCommerz session for a pending
     * order and redirect to the gateway.
     */
    public function start(Request $request, Order $order): RedirectResponse
    {
        abort_unless($order->user_id === $request->user()->id, 404);

        if (! $order->isPending()) {
            return redirect()->route('account.orders.show', $order)
                ->with('info', 'This order is not awaiting payment.');
        }

        if (! $this->sslCommerz->isConfigured()) {
            return redirect()->route('account.orders.show', $order)
                ->with('error', 'Online payment is temporarily unavailable — please use the manual payment instructions.');
        }

        $gatewayUrl = $this->sslCommerz->createSession($order);

        if (! $gatewayUrl) {
            return redirect()->route('account.orders.show', $order)
                ->with('error', 'Could not start the payment session. Please try again or pay manually.');
        }

        return redirect()->away($gatewayUrl);
    }

    /**
     * Browser lands here after paying. The redirect itself proves nothing —
     * the payment counts only after server-side validation. No auth: the
     * cross-site POST arrives without a session cookie (SameSite=Lax).
     */
    public function success(Request $request): RedirectResponse
    {
        $order = $this->processCallback($request);

        if (! $order) {
            return redirect()->route('home')
                ->with('error', 'We could not verify this payment. If money left your account, contact support with your transaction ID.');
        }

        return redirect()->route('account.orders.show', $order)->with(
            $order->isDelivered() ? 'success' : 'info',
            $order->isDelivered()
                ? 'Payment confirmed! Your license is on its way to your inbox and downloads are unlocked.'
                : 'Payment confirmed! Your license is being prepared — it will arrive by email shortly.'
        );
    }

    /**
     * IPN: SSLCommerz's server-to-server notification — the safety net when
     * the customer closes the browser before the success redirect.
     */
    public function ipn(Request $request)
    {
        $this->processCallback($request);

        return response('OK');
    }

    public function fail(Request $request): RedirectResponse
    {
        $order = $this->findOrder($request);

        if ($order && $order->isPending()) {
            // Leave the order pending — the customer can retry or pay
            // manually; nothing was charged.
            return redirect()->route('account.orders.show', $order)
                ->with('error', 'The payment did not go through. You can try again or follow the manual payment instructions.');
        }

        return redirect()->route('home')->with('error', 'The payment did not go through.');
    }

    public function cancel(Request $request): RedirectResponse
    {
        $order = $this->findOrder($request);

        if ($order) {
            return redirect()->route('account.orders.show', $order)
                ->with('info', 'Payment cancelled — your order is still waiting whenever you are ready.');
        }

        return redirect()->route('home');
    }

    /**
     * Shared success/IPN path: identify the order by tran_id, validate the
     * payment server-side, cross-check amount/currency, then run the same
     * paid -> delivered flow as a manual sale. Idempotent — an already-paid
     * order is returned untouched.
     */
    private function processCallback(Request $request): ?Order
    {
        $order = $this->findOrder($request);
        $valId = (string) $request->input('val_id', '');

        if (! $order || $valId === '') {
            return null;
        }

        if (! $order->isPending()) {
            return $order; // already processed (e.g. IPN raced the redirect)
        }

        $validated = $this->sslCommerz->validate($valId);

        if (! $validated || ! $this->sslCommerz->matchesOrder($validated, $order)) {
            report(new \RuntimeException('SSLCommerz validation mismatch for ' . $order->order_no));

            return null;
        }

        $order->update(['sslcz_val_id' => $valId]);

        $this->orderFlow->markPaid($order, 'sslcommerz');

        return $order->fresh();
    }

    private function findOrder(Request $request): ?Order
    {
        $tranId = (string) $request->input('tran_id', '');

        return $tranId === '' ? null : Order::where('sslcz_tran_id', $tranId)->first();
    }
}
