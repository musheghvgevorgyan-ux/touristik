<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body style="margin:0;padding:0;background:#f4f5f7;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;">
<div style="max-width:600px;margin:0 auto;padding:20px;">
    <div style="background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 2px 12px rgba(0,0,0,0.08);">
        <div style="background:linear-gradient(135deg,#FF6B35,#f7a072);padding:30px;text-align:center;">
            <h1 style="color:#fff;margin:0;font-size:22px;">&#128222; Callback Request</h1>
            <p style="color:rgba(255,255,255,0.9);margin:8px 0 0;font-size:14px;">A customer wants you to call them</p>
        </div>
        <div style="padding:30px;">
            <div style="text-align:center;margin-bottom:20px;">
                <div style="display:inline-block;background:#FFF3ED;padding:15px 25px;border-radius:10px;">
                    <p style="margin:0 0 5px;color:#6c757d;font-size:13px;">Customer Name</p>
                    <p style="margin:0;color:#1a1a2e;font-size:18px;font-weight:700;">{{ $callbackName }}</p>
                </div>
            </div>
            <div style="text-align:center;margin-bottom:25px;">
                <a href="tel:{{ $callbackPhone }}" style="display:inline-block;padding:15px 40px;background:#28a745;color:#fff;text-decoration:none;border-radius:10px;font-weight:700;font-size:20px;letter-spacing:1px;">{{ $callbackPhone }}</a>
            </div>
            @if(!empty($callbackNote))
            <div style="padding:15px;background:#f8f9fa;border-radius:8px;">
                <p style="margin:0 0 5px;color:#6c757d;font-size:13px;">Note:</p>
                <p style="margin:0;color:#333;font-size:14px;">{{ $callbackNote }}</p>
            </div>
            @endif
            <p style="text-align:center;color:#999;font-size:13px;margin-top:20px;">Received at {{ now()->timezone('Asia/Yerevan')->format('H:i, M d Y') }}</p>
        </div>
        <div style="background:#f8f9fa;padding:15px;text-align:center;border-top:1px solid #eee;">
            <p style="margin:0;color:#999;font-size:12px;">&copy; {{ date('Y') }} Touristik Travel Club</p>
        </div>
    </div>
</div>
</body>
</html>
