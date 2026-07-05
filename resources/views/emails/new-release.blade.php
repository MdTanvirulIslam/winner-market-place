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
                        <h2 style="margin:0 0 12px;color:#1e293b;font-size:18px;">A new version is ready 🚀</h2>
                        <p style="margin:0 0 20px;color:#475569;font-size:14px;line-height:22px;">
                            <strong>{{ $release->product->name }} v{{ $release->version }}</strong> has just been released.
                            As a customer, you can download it right away — free of charge, like every update.
                        </p>

                        @if($release->notes)
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f8fafc;border-radius:8px;margin-bottom:20px;">
                                <tr><td style="padding:16px 20px;">
                                    <p style="margin:0 0 6px;color:#64748b;font-size:12px;">WHAT'S NEW</p>
                                    <div style="color:#1e293b;font-size:14px;line-height:22px;">{!! $release->notesHtml() !!}</div>
                                </td></tr>
                            </table>
                        @endif

                        <table role="presentation" cellpadding="0" cellspacing="0" style="margin:0 auto;">
                            <tr><td style="background:#0d9488;border-radius:8px;">
                                <a href="{{ route('account.downloads') }}" style="display:inline-block;padding:12px 32px;color:#ffffff;font-size:14px;font-weight:bold;text-decoration:none;">Download the Update</a>
                            </td></tr>
                        </table>
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
