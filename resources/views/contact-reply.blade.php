<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Phản hồi liên hệ</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto;">
    <h3>Xin chào {{ $contact->name }},</h3>

    <p>Chúng tôi đã nhận được yêu cầu của bạn với nội dung:</p>
    <blockquote style="border-left: 4px solid #ccc; padding-left: 15px; margin: 15px 0; color: #555;">
        {{ $contact->content }}
    </blockquote>

    <hr style="border: 0; border-top: 1px solid #eee; margin: 20px 0;">

    <p><strong>Phản hồi từ bộ phận hỗ trợ:</strong></p>
    <div style="white-space: pre-wrap;">
        {!! nl2br(e($replyContent)) !!}
    </div>

    <br>
    <p>Trân trọng,<br>
    Đội ngũ hỗ trợ Luxury Store<br>
    <a href="{{ config('app.url') }}" style="color: #007bff;">{{ config('app.name') }}</a></p>
</body>
</html>