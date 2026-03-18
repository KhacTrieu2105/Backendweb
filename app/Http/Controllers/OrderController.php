<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\OrderSuccessMail;

class OrderController extends Controller
{
    // 📌 Danh sách đơn hàng (Có lọc theo User, Phân trang, Tìm kiếm)
    public function index(Request $request)
    {
        $query = Order::query();

        // CHỈNH SỬA QUAN TRỌNG: Lọc theo user_id nếu có gửi lên
        // Điều này giúp tách biệt lịch sử đơn hàng của từng khách hàng
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Tìm kiếm theo thông tin khách hàng
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%')
                  ->orWhere('phone', 'like', '%' . $request->search . '%');
            });
        }

        $limit = $request->input('limit', 10);
        $page  = $request->input('page', 1);

        $total = $query->count();

        // Lấy dữ liệu kèm theo chi tiết sản phẩm (nếu cần hiển thị ở list)
        $orders = $query
            ->orderBy('created_at', 'desc')
            ->offset(($page - 1) * $limit)
            ->limit($limit)
            ->get();

        return response()->json([
            'status' => true,
            'data'   => $orders,
            'total'  => $total
        ]);
    }

    // 📌 Xem chi tiết một đơn hàng (Kèm danh sách sản phẩm)
  public function show($id)
{
    // Nạp cả chi tiết đơn hàng VÀ thông tin sản phẩm của từng chi tiết đó
    $order = Order::with(['details.product'])->find($id);

    if (!$order) {
        return response()->json([
            'status' => false, 
            'message' => 'Đơn hàng không tồn tại'
        ], 404);
    }

    return response()->json([
        'status' => true,
        'data'   => $order
    ]);
}

    // 📌 Cập nhật trạng thái (Dùng cho cả Admin xác nhận và User hủy đơn)
    public function update(Request $request, $id)
    {
        $order = Order::find($id);
        if (!$order) {
            return response()->json(['status' => false, 'message' => 'Đơn hàng không tồn tại'], 404);
        }

        $order->update([
            'status' => $request->status,
            'updated_at' => now()
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'Cập nhật trạng thái thành công'
        ]);
    }

    // 📌 Lưu đơn hàng mới (Checkout)
  public function store(Request $request)
    {
        // 1. Validation: Đảm bảo cart_items đúng cấu trúc
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'customer_name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'required|string|max:20',
            'address' => 'required|string',
            'total_amount' => 'required|numeric|min:1000',
            'payment_method' => 'required|in:cod,vnpay',
            'cart_items' => 'required|array|min:1',
            'cart_items.*.product_id' => 'required|exists:products,id',
            'cart_items.*.quantity' => 'required|integer|min:1',
            'cart_items.*.price' => 'required|numeric',
        ]);

        try {
            // Sử dụng Transaction để đảm bảo tính toàn vẹn dữ liệu
            $order = DB::transaction(function () use ($request) {
                // A. Tạo đơn hàng chính
                $order = Order::create([
                    'user_id'         => $request->user_id,
                    'name'            => $request->customer_name,
                    'email'           => $request->email,
                    'phone'           => $request->phone,
                    'address'         => $request->address,
                    'total_amount'    => $request->total_amount,
                    'payment_method'  => $request->payment_method,
                    'status'          => 1, // 1: Chờ thanh toán/Xác nhận
                    'created_by'      => $request->user_id,
                    'created_at'      => now(),
                ]);

                // B. Lưu chi tiết đơn hàng (OrderDetail)
                foreach ($request->cart_items as $item) {
                    OrderDetail::create([
                        'order_id'   => $order->id,
                        'product_id' => $item['product_id'],
                        'qty'        => $item['quantity'],
                        'price'      => $item['price'],
                        'amount'     => $item['quantity'] * $item['price'],
                        'discount'   => 0, // Cập nhật để tránh lỗi "Field discount doesn't have a default value"
                    ]);
                }

                return $order;
            });

            // 2. Xử lý theo phương thức thanh toán
            if ($request->payment_method === 'vnpay') {
                $vnpay_url = $this->createVNPAYUrl($order);

                if ($vnpay_url) {
                    return response()->json([
                        'status' => true,
                        'message' => 'Đơn hàng đã tạo thành công. Đang chuyển hướng...',
                        'vnpay_url' => $vnpay_url,
                        'order_id' => $order->id,
                        'redirect_to_vnpay' => true
                    ]);
                }
            }

            // 3. Nếu là COD: Gửi email xác nhận và trả về thành công
            $this->sendOrderSuccessEmail($order);

            return response()->json([
                'status' => true,
                'message' => 'Đặt hàng thành công! Vui lòng kiểm tra email xác nhận.',
                'order' => $order,
                'order_id' => $order->id
            ]);

        } catch (\Exception $e) {
            Log::error('Lỗi Checkout: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Có lỗi xảy ra trong quá trình đặt hàng: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Helper gửi email thành công
     */
    public function sendOrderSuccessEmail(Order $order)
    {
        try {
            // Nạp thêm details và product để hiển thị trong email
            $order->load(['details.product']);
            Mail::to($order->email)->send(new OrderSuccessMail($order));
            Log::info('Order success email sent', ['order_id' => $order->id]);
        } catch (\Exception $e) {
            Log::error('Failed to send order success email: ' . $e->getMessage());
        }
    }

private function createVNPAYUrl($order)
{
    // Lấy đúng tên biến đã khai báo trong file .env
    $vnp_TmnCode = env('VNP_TMN_CODE'); 
    $vnp_HashSecret = env('VNP_HASH_SECRET');
    $vnp_Url = env('VNP_URL');
    $vnp_Returnurl = env('VNP_RETURNURL'); // Lưu ý đúng tên biến RETURNURL

    $vnp_TxnRef = $order->id; 
    $vnp_OrderInfo = "Thanh toan hoa don #" . $order->id;
    $vnp_OrderType = 'billpayment';
    $vnp_Amount = intval($order->total_amount * 100); // Ép kiểu số nguyên để tránh lỗi định dạng
    $vnp_Locale = 'vn';
    $vnp_IpAddr = request()->ip();

    $inputData = array(
        "vnp_Version" => "2.1.0",
        "vnp_TmnCode" => $vnp_TmnCode,
        "vnp_Amount" => $vnp_Amount,
        "vnp_Command" => "pay",
        "vnp_CreateDate" => date('YmdHis'),
        "vnp_CurrCode" => "VND",
        "vnp_IpAddr" => $vnp_IpAddr,
        "vnp_Locale" => $vnp_Locale,
        "vnp_OrderInfo" => $vnp_OrderInfo,
        "vnp_OrderType" => $vnp_OrderType,
        "vnp_ReturnUrl" => $vnp_Returnurl,
        "vnp_TxnRef" => $vnp_TxnRef,
    );

    ksort($inputData);
    $query = "";
    $i = 0;
    $hashdata = "";
    foreach ($inputData as $key => $value) {
        if ($i == 1) {
            $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
        } else {
            $hashdata .= urlencode($key) . "=" . urlencode($value);
            $i = 1;
        }
        $query .= urlencode($key) . "=" . urlencode($value) . '&';
    }

    // Tạo URL hoàn chỉnh đúng định dạng
    $vnpSecureHash = hash_hmac('sha512', $hashdata, $vnp_HashSecret);
    $vnp_Url = $vnp_Url . "?" . $query . 'vnp_SecureHash=' . $vnpSecureHash;

    return $vnp_Url;
}
public function vnpayIPN(Request $request)
{
    Log::info('VNPay IPN received', [
        'all_data' => $request->all(),
        'headers' => $request->headers->all(),
        'method' => $request->method(),
    ]);

    $inputData = $request->all();
    $vnp_SecureHash = $inputData['vnp_SecureHash'];
    unset($inputData['vnp_SecureHash']);
    ksort($inputData);

    $hashData = "";
    $i = 0;
    foreach ($inputData as $key => $value) {
        if ($i == 1) { $hashData .= '&' . urlencode($key) . "=" . urlencode($value); }
        else { $hashData .= urlencode($key) . "=" . urlencode($value); $i = 1; }
    }

    $secureHash = hash_hmac('sha512', $hashData, env('VNP_HASH_SECRET'));

    Log::info('VNPay IPN validation', [
        'received_hash' => $vnp_SecureHash,
        'calculated_hash' => $secureHash,
        'is_valid' => ($secureHash == $vnp_SecureHash),
        'order_id' => $inputData['vnp_TxnRef'] ?? null,
        'response_code' => $inputData['vnp_ResponseCode'] ?? null,
    ]);

    if ($secureHash == $vnp_SecureHash) {
        $orderId = $inputData['vnp_TxnRef'];
        $order = Order::find($orderId);

        Log::info('Order found in IPN', [
            'order_id' => $orderId,
            'order_exists' => !is_null($order),
            'current_status' => $order ? $order->status : null,
        ]);

        if ($order) {
            if ($inputData['vnp_ResponseCode'] == '00') {
                $order->status = 'paid'; // Cập nhật trạng thái thành Đã thanh toán
                $order->save();

                Log::info('Order status updated to paid', ['order_id' => $order->id]);

                // Gửi email xác nhận đơn hàng thành công
                $this->sendOrderSuccessEmail($order);

                Log::info('Order payment successful and email sent', ['order_id' => $order->id]);
            } else {
                $order->status = 'failed';
                $order->save();
                Log::warning('Order payment failed', [
                    'order_id' => $order->id,
                    'response_code' => $inputData['vnp_ResponseCode']
                ]);
            }
            return response()->json(['RspCode' => '00', 'Message' => 'Confirm Success']);
        } else {
            Log::error('Order not found in IPN', ['order_id' => $orderId]);
        }
    } else {
        Log::warning('Invalid VNPay signature', [
            'received_hash' => $vnp_SecureHash,
            'calculated_hash' => $secureHash,
        ]);
    }
    return response()->json(['RspCode' => '97', 'Message' => 'Invalid signature']);
}

    // 📌 VNPay Return URL - Xử lý khi user quay lại từ VNPay
    public function vnpayReturn(Request $request)
    {
        $inputData = $request->all();
        $vnp_SecureHash = $inputData['vnp_SecureHash'];
        unset($inputData['vnp_SecureHash']);
        ksort($inputData);

        $hashData = "";
        $i = 0;
        foreach ($inputData as $key => $value) {
            if ($i == 1) { $hashData .= '&' . urlencode($key) . "=" . urlencode($value); }
            else { $hashData .= urlencode($key) . "=" . urlencode($value); $i = 1; }
        }

        $secureHash = hash_hmac('sha512', $hashData, env('VNP_HASH_SECRET'));
        $orderId = $inputData['vnp_TxnRef'];
        $order = Order::find($orderId);

        if ($secureHash == $vnp_SecureHash) {
            if ($order) {
                if ($inputData['vnp_ResponseCode'] == '00') {
                    // Đảm bảo trạng thái đã được cập nhật từ IPN
                    if ($order->status !== 'paid') {
                        $order->status = 'paid';
                        $order->save();

                        // Gửi email xác nhận nếu chưa gửi từ IPN
                        $this->sendOrderSuccessEmail($order);
                    }

                    // Redirect về trang thành công với thông tin đơn hàng
                    return redirect('/checkout/success?order_id=' . $order->id . '&status=success');
                } else {
                    // Thanh toán thất bại
                    return redirect('/checkout/failed?order_id=' . $order->id . '&status=failed');
                }
            }
        }

        // Lỗi xác thực
        return redirect('/checkout/error?order_id=' . $orderId . '&status=error');
    }


}
