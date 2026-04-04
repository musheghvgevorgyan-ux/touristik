<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body style="margin:0;padding:0;background:#f4f5f7;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;">
<div style="max-width:600px;margin:0 auto;padding:20px;">
    <div style="background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 2px 12px rgba(0,0,0,0.08);">
        <div style="background:linear-gradient(135deg,#1a1a2e,#16213e);padding:30px;text-align:center;">
            <h1 style="color:#fff;margin:0;font-size:22px;">&#9992; Touristik Travel</h1>
            <p style="color:rgba(255,255,255,0.7);margin:8px 0 0;font-size:14px;">New Contact Message</p>
        </div>
        <div style="padding:30px;">
            <table style="width:100%;border-collapse:collapse;">
                <tr>
                    <td style="padding:8px 0;color:#6c757d;font-size:14px;width:100px;">Name:</td>
                    <td style="padding:8px 0;color:#1a1a2e;font-weight:600;">{{ $contactName }}</td>
                </tr>
                <tr>
                    <td style="padding:8px 0;color:#6c757d;font-size:14px;">Email:</td>
                    <td style="padding:8px 0;"><a href="mailto:{{ $contactEmail }}" style="color:#FF6B35;text-decoration:none;">{{ $contactEmail }}</a></td>
                </tr>
                <tr>
                    <td style="padding:8px 0;color:#6c757d;font-size:14px;">Subject:</td>
                    <td style="padding:8px 0;color:#1a1a2e;">{{ $contactSubject ?? 'N/A' }}</td>
                </tr>
            </table>
            <div style="margin-top:20px;padding:20px;background:#f8f9fa;border-radius:8px;border-left:4px solid #FF6B35;">
                <p style="margin:0 0 8px;color:#6c757d;font-size:13px;text-transform:uppercase;letter-spacing:0.5px;">Message</p>
                <p style="margin:0;color:#333;line-height:1.7;font-size:15px;">{{ $contactMessage }}</p>
            </div>
            <div style="margin-top:25px;text-align:center;">
                <a href="mailto:{{ $contactEmail }}?subject=Re: {{ $contactSubject ?? 'Your inquiry' }}" style="display:inline-block;padding:12px 30px;background:#FF6B35;color:#fff;text-decoration:none;border-radius:8px;font-weight:600;font-size:14px;">Reply to {{ $contactName }}</a>
            </div>
        </div>
        <div style="background:#f8f9fa;padding:15px;text-align:center;border-top:1px solid #eee;">
            <p style="margin:0;color:#999;font-size:12px;">&copy; {{ date('Y') }} Touristik Travel Club &middot; <a href="https://touristik.am" style="color:#FF6B35;text-decoration:none;">touristik.am</a></p>
        </div>
    </div>
</div>
</body>
</html>
