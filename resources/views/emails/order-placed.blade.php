<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"></head>
<body style="margin:0;padding:0;background:#f1f5f9;font-family:Arial,Helvetica,sans-serif;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f1f5f9;padding:24px 0;">
        <tr><td align="center">
            <table role="presentation" width="560" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:10px;overflow:hidden;">
                <tr>
                    <td style="background:#0d9488;padding:20px 32px;">
                        <h1 style="margin:0;color:#ffffff;font-size:20px;">{{ config('app.name') }}</h1>
                    </td>
                </tr>
                <tr>
                    <td style="padding:32px;">
                        <h2 style="margin:0 0 12px;color:#1e293b;font-size:18px;">We received your order</h2>
                        <p style="margin:0 0 20px;color:#475569;font-size:14px;line-height:22px;">
                            Hi {{ $order->customer_name }}, your order
                            <strong>{{ $order->order_no }}</strong> for
                            <strong>{{ $order->product_name }}</strong>
                            ({{ $order->currency }} {{ number_format((float) $order->amount, 2) }})
                            is waiting for payment.
                        </p>

                        <table role="presentation" cellpadding="0" cellspacing="0" style="margin:0 auto 24px;">
                            <tr><td style="background:#0d9488;border-radius:8px;">
                                <a href="{{ route('account.orders.show', $order) }}" style="display:inline-block;padding:12px 32px;color:#ffffff;font-size:14px;font-weight:bold;text-decoration:none;">Pay Online Now</a>
                            </td></tr>
                        </table>

                        @if($setting->payment_instructions)
                            <p style="margin:0 0 8px;color:#1e293b;font-size:14px;font-weight:bold;">Prefer to pay manually?</p>
                            <div style="background:#f8fafc;border-radius:8px;padding:16px 20px;color:#475569;font-size:13px;line-height:21px;white-space:pre-line;">{{ $setting->payment_instructions }}</div>
                            <p style="margin:12px 0 0;color:#94a3b8;font-size:12px;">Mention your order number {{ $order->order_no }} with your payment.</p>
                        @endif

                        <p style="margin:24px 0 0;color:#475569;font-size:14px;line-height:22px;">
                            As soon as your payment is confirmed, your license key and download access
                            arrive automatically by email.
                        </p>
                    </td>
                </tr>
                <tr>
                    <td style="padding:16px 32px;background:#f8fafc;border-top:1px solid #e2e8f0;">
                        <p style="margin:0;color:#94a3b8;font-size:12px;">&copy; {{ date('Y') }} {{ config('app.name') }} · Powered by Winner Devs</p>
                    </td>
                </tr>
            </table>
        </td></tr>
    </table>
</body>
</html>
