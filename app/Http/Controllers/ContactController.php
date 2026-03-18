<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail; // Thêm dòng này
use App\Mail\ContactReplyMail;

class ContactController extends Controller
{
    // LIST
    public function index()
    {
        return response()->json([
            'status' => true,
            'data' => Contact::orderByDesc('id')->get()
        ]);
    }

    // SHOW
    public function show($id)
    {
        $contact = Contact::find($id);

        if (!$contact) {
            return response()->json([
                'status' => false,
                'message' => 'Không tìm thấy liên hệ'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data' => $contact
        ]);
    }

    // Laravel: ContactController.php

// File: App\Http\Controllers\ContactController.php

public function store(Request $request)
{
    try {
        $contact = new \App\Models\Contact();
        
        // Gán các trường khớp 100% với ảnh database bạn gửi
        $contact->name = $request->name;
        $contact->email = $request->email;
        $contact->phone = $request->phone;
        $contact->content = $request->content;
        
        // Các trường mặc định để tránh lỗi SQL
        $contact->user_id = null; // Hoặc lấy ID người dùng nếu đã đăng nhập
        $contact->reply_id = 0;   // Mặc định chưa có phản hồi
        $contact->created_at = now();
        $contact->status = 1;     // 1: Mới
        
        $contact->save();

        return response()->json([
            'status' => true,
            'message' => 'Gửi liên hệ thành công!',
            'data' => $contact
        ], 201);

    } catch (\Exception $e) {
        return response()->json([
            'status' => false,
            'message' => 'Lỗi lưu dữ liệu: ' . $e->getMessage()
        ], 500);
    }
}
    // REPLY
    public function reply(Request $request, $id)
    {
        // 1. Tìm liên hệ
        $contact = Contact::find($id);
        if (!$contact) {
            return response()->json(['status' => false, 'message' => 'Không tìm thấy liên hệ'], 404);
        }

        // 2. Kiểm tra dữ liệu
        $request->validate([
            'reply_content' => 'required|string'
        ]);

        try {
            // 3. Gửi Email thực tế về Gmail của khách hàng
            Mail::to($contact->email)->send(new ContactReplyMail($contact, $request->reply_content));

            // 4. Cập nhật trạng thái trong Database
            $contact->update([
                'reply_content' => $request->reply_content,
                'reply_id' => Auth::id() ?? 1, // ID người trả lời
                'status' => 2, // Đã phản hồi
                'updated_at' => now(),
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Đã gửi phản hồi thành công đến email: ' . $contact->email
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Lỗi khi gửi email: ' . $e->getMessage()
            ], 500);
        }
    }

    // DELETE
    public function destroy($id)
    {
        Contact::findOrFail($id)->delete();

        return response()->json([
            'status' => true,
            'message' => 'Xóa thành công'
        ]);
    }
    
}
