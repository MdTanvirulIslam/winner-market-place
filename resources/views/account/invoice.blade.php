<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice {{ $order->order_no }} — {{ config('app.name') }}</title>
    @vite('resources/css/app.css')
</head>
<body class="invoice-page">
    <div class="actions">
        <a class="link" href="{{ route('account.orders.show', $order) }}">&larr; Back to order</a>
        <button class="btn" onclick="window.print()">Print / Save as PDF</button>
    </div>

    <div class="sheet">
        <div class="head">
            <div>
                <div class="brand">{{ $setting->store_name ?: config('app.name') }}</div>
                <div class="muted">Powered by Winner Devs{{ $setting->support_email ? ' · ' . $setting->support_email : '' }}</div>
            </div>
            <div class="head-meta">
                <h1>{{ $order->status === 'refunded' ? 'Refund Receipt' : 'Invoice' }}</h1>
                <div class="muted">{{ $order->order_no }}</div>
                <div>
                    <span class="badge {{ $order->status === 'refunded' ? 'refunded' : 'paid' }}">{{ $order->status === 'refunded' ? 'Refunded' : 'Paid' }}</span>
                </div>
            </div>
        </div>

        <div class="cols">
            <div class="col">
                <h3>Billed To</h3>
                <p>
                    {{ $order->customer_name }}<br>
                    {{ $order->customer_email }}
                    @if($order->customer_phone)<br>{{ $order->customer_phone }}@endif
                </p>
            </div>
            <div class="col">
                <h3>Details</h3>
                <p>
                    Order date: {{ $order->created_at->format('d M Y') }}<br>
                    @if($order->paid_at)Paid: {{ $order->paid_at->format('d M Y') }}<br>@endif
                    Payment: {{ $order->payment_method === 'sslcommerz' ? 'SSLCommerz (online)' : 'Manual' }}
                    @if($order->sslcz_tran_id)<br>Transaction: {{ $order->sslcz_tran_id }}@endif
                </p>
            </div>
        </div>

        <table>
            <thead>
                <tr><th>Item</th><th class="right">Amount</th></tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <strong>{{ $order->product_name }}</strong><br>
                        <span class="muted">Software license · digital delivery · lifetime updates</span>
                    </td>
                    <td class="right">{{ $order->currency }} {{ number_format((float) $order->amount + (float) $order->discount_amount, 2) }}</td>
                </tr>
                @if((float) $order->discount_amount > 0)
                    <tr>
                        <td class="right">Coupon {{ $order->coupon_code }}</td>
                        <td class="right">−{{ $order->currency }} {{ number_format((float) $order->discount_amount, 2) }}</td>
                    </tr>
                @endif
                <tr class="total-row">
                    <td class="right">Total</td>
                    <td class="right">{{ $order->currency }} {{ number_format((float) $order->amount, 2) }}</td>
                </tr>
            </tbody>
        </table>

        @if($order->license_key)
            <div class="cols">
                <div class="col">
                    <h3>License Key</h3>
                    <p class="license">{{ $order->license_key }}</p>
                </div>
            </div>
        @endif

        <div class="foot">
            This is a computer-generated document for order {{ $order->order_no }} placed on {{ config('app.name') }}.
            @if($order->status === 'refunded') This order was refunded{{ $order->refunded_at ? ' on ' . $order->refunded_at->format('d M Y') : '' }}. @endif
        </div>
    </div>
</body>
</html>
