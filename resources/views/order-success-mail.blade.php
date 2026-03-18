<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Xác nhận đơn hàng</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            background-color: #f8f9fa;
        }
        .container {
            background-color: #ffffff;
            margin: 20px;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #007bff;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .header h1 {
            color: #007bff;
            margin: 0;
            font-size: 24px;
        }
        .success-icon {
            font-size: 48px;
            color: #28a745;
            margin-bottom: 10px;
        }
        .order-info {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }
        .order-info h3 {
            margin-top: 0;
            color: #007bff;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
        }
        .info-label {
            font-weight: bold;
            color: #666;
        }
        .info-value {
            color: #333;
        }
        .total-amount {
            font-size: 18px;
            font-weight: bold;
            color: #28a745;
            text-align: center;
            margin: 20px 0;
            padding: 15px;
            background-color: #d4edda;
            border-radius: 5px;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #dee2e6;
            color: #666;
            font-size: 14px;
        }
        .contact-info {
            background-color: #e9ecef;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .status-badge {
            display: inline-block;
            padding: 5px 15px;
            background-color: #28a745;
            color: white;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="success-icon">✅</div>
            <h1>ĐẶT HÀNG THÀNH CÔNG!</h1>
            <p>Cảm ơn bạn đã tin tưởng và mua sắm tại cửa hàng của chúng tôi</p>
        </div>
<div class="order-info">
    <h3>🛒 Chi tiết sản phẩm</h3>
    <table style="width: 100%; border-collapse: collapse;">
        <thead>
            <tr style="border-bottom: 1px solid #ddd; text-align: left;">
                <th style="padding: 10px;">Sản phẩm</th>
                <th style="padding: 10px;">SL</th>
                <th style="padding: 10px;">Giá</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->details as $item)
            <tr style="border-bottom: 1px solid #eee;">
                <td style="padding: 10px;">{{ $item->product_name ?? 'Sản phẩm #'.$item->product_id }}</td>
                <td style="padding: 10px;">{{ $item->qty }}</td>
                <td style="padding: 10px;">{{ number_format($item->price, 0, ',', '.') }}đ</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
        <div class="order-info">
            <h3>📦 Thông tin đơn hàng</h3>
            <div class="info-row">
                <span class="info-label">Mã đơn hàng:</span>
                <span class="info-value">#{{ $order->id }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Ngày đặt:</span>
                <span class="info-value">{{ $order->created_at->format('d/m/Y H:i') }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Phương thức thanh toán:</span>
                <span class="info-value">{{ strtoupper($order->payment_method) }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Trạng thái:</span>
                <span class="status-badge">ĐÃ THANH TOÁN</span>
            </div>
        </div>

        <div class="order-info">
            <h3>👤 Thông tin khách hàng</h3>
            <div class="info-row">
                <span class="info-label">Họ tên:</span>
                <span class="info-value">{{ $order->name }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Email:</span>
                <span class="info-value">{{ $order->email }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Điện thoại:</span>
                <span class="info-value">{{ $order->phone }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Địa chỉ:</span>
                <span class="info-value">{{ $order->address }}</span>
            </div>
        </div>

        <div class="total-amount">
            💰 Tổng tiền: {{ number_format($order->total_amount, 0, ',', '.') }} VND
        </div>

        <div class="contact-info">
            <h3>📞 Thông tin liên hệ</h3>
            <p>Nếu bạn có bất kỳ câu hỏi nào về đơn hàng, vui lòng liên hệ với chúng tôi:</p>
            <p><strong>Email:</strong> support@example.com</p>
            <p><strong>Điện thoại:</strong> 1900-xxxx</p>
            <p><strong>Website:</strong> www.example.com</p>
        </div>

        <div class="footer">
            <p>📧 Email này được gửi tự động từ hệ thống. Vui lòng không trả lời email này.</p>
            <p><strong>Cảm ơn bạn đã lựa chọn chúng tôi! 🛍️</strong></p>
        </div>
    </div>
</body>
</html>
