<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"></head>
<body style="margin:0;padding:24px;background:#f1f5f9;font-family:Arial,Helvetica,sans-serif;color:#1e293b;">
    <h2 style="margin:0 0 16px;font-size:18px;">New contact message</h2>
    <p style="margin:0 0 4px;font-size:14px;"><strong>From:</strong> {{ $senderName }} ({{ $senderEmail }})</p>
    <p style="margin:0 0 16px;font-size:14px;"><strong>Subject:</strong> {{ $messageSubject }}</p>
    <div style="background:#ffffff;border-radius:8px;padding:16px;font-size:14px;line-height:22px;white-space:pre-line;">{{ $messageBody }}</div>
    <p style="margin:16px 0 0;font-size:12px;color:#94a3b8;">Sent from the {{ config('app.name') }} contact form. Reply directly to answer.</p>
</body>
</html>
