<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice {{ $order->order_no }} — {{ config('app.name') }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: Arial, Helvetica, sans-serif; background: #f1f5f9; color: #1e293b; padding: 32px 16px; }
        .sheet { max-width: 720px; margin: 0 auto; background: #fff; border-radius: 10px; padding: 40px; }
        .head { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 32px; }
        .brand { font-size: 22px; font-weight: 800; color: #0d9488; }
        .muted { color: #64748b; font-size: 12px; }
        h1 { font-size: 18px; margin-bottom: 4px; }
        .badge { display: inline-block; padding: 4px 12px; border-radius: 999px; font-size: 11px; font-weight: 700; text-transform: uppercase; }
        .badge.paid { background: rgba(34,197,94,.12); color: #16a34a; }
        .badge.refunded { background: rgba(239,68,68,.12); color: #dc2626; }
        .cols { display: flex; gap: 24px; margin-bottom: 28px; }
        .col { flex: 1; }
        .col h3 { font-size: 11px; text-transform: uppercase; letter-spacing: .1em; color: #64748b; margin-bottom: 6px; }
        .col p { font-size: 13px; line-height: 20px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 24px; }
        th { text-align: left; font-size: 11px; text-transform: uppercase; letter-spacing: .08em; color: #64748b; padding: 10px 12px; border-bottom: 2px solid #e2e8f0; }
        td { padding: 12px; font-size: 13px; border-bottom: 1px solid #e2e8f0; }
        .right { text-align: right; }
        .total-row td { font-size: 15px; font-weight: 800; border-bottom: none; }
        .foot { margin-top: 28px; padding-top: 16px; border-top: 1px solid #e2e8f0; font-size: 12px; color: #94a3b8; }
        .actions { max-width: 720px; margin: 0 auto 16px; display: flex; justify-content: space-between; align-items: center; }
        .btn { display: inline-block; padding: 10px 20px; background: #0d9488; color: #fff; border: none; border-radius: 8px; font-size: 13px; font-weight: 700; cursor: pointer; text-decoration: none; }
        .link { color: #0d9488; font-size: 13px; font-weight: 600; text-decoration: none; }
        @media print {
            body { background: #fff; padding: 0; }
            .sheet { border-radius: 0; padding: 24px; max-width: none; }
            .actions { display: none; }
        }
    </style>
</head>
<body>
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
            <div style="text-align:right;">
                <h1>{{ $order->status === 'refunded' ? 'Refund Receipt' : 'Invoice' }}</h1>
                <div class="muted">{{ $order->order_no }}</div>
                <div style="margin-top:8px;">
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
                    <p style="font-family:monospace;font-weight:700;color:#0d9488;">{{ $order->license_key }}</p>
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
