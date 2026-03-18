<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Carbon;

class UserController extends Controller
{
    // LIST
    public function index(Request $request)
    {
        $query = User::query();

        if ($request->search) {
            $query->where('name', 'like', "%{$request->search}%")
                  ->orWhere('email', 'like', "%{$request->search}%");
        }

        $users = $query->orderBy('created_at', 'desc')->get();

        return response()->json([
            'status' => true,
            'data' => $users
        ]);
    }

    // SHOW
    public function show($id)
    {
        return response()->json([
            'status' => true,
            'data' => User::findOrFail($id)
        ]);
    }

    // CREATE
  public function store(Request $request)
{
    try {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'status' => 'required|in:0,1',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'username' => $request->email, // hoặc sinh tự động
            'password' => bcrypt('123456'),
            'roles' => 'customer',
            'status' => $request->status,

            // ⚠️ CỰC KỲ QUAN TRỌNG
            'created_at' => Carbon::now(),
            'created_by' => 1, // hoặc Auth::id()
        ]);

        return response()->json([
            'status' => true,
            'data' => $user,
            'message' => 'Thêm thành viên thành công'
        ], 201);

    } catch (\Exception $e) {
        return response()->json([
            'status' => false,
            'message' => $e->getMessage()
        ], 500);
    }
}

    // UPDATE
   public function update(Request $request, $id)
    {
        try {
            $user = User::findOrFail($id);

            $request->validate([
                'name' => 'required|string|max:255',
                'email' => "required|email|unique:users,email,$id",
                'status' => 'required|in:0,1,2',
                'roles' => 'nullable|string', // Chấp nhận roles từ frontend
            ]);

            // Cập nhật toàn bộ dữ liệu khớp với fillable trong Model
            $user->update($request->all());

            return response()->json([
                'status' => true,
                'message' => 'Cập nhật thành công',
                'data' => $user
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Lỗi cập nhật: ' . $e->getMessage()
            ], 500);
        }
    }

    // Các hàm khác (show, store, destroy) giữ nguyên như code cũ của bạn...


    // DELETE
    public function destroy($id)
    {
        User::findOrFail($id)->delete();

        return response()->json([
            'status' => true,
            'message' => 'Xóa thành công'
        ]);
    }

    /**
     * Đổi mật khẩu cho user hiện tại
     */
    public function changePassword(Request $request)
    {
        try {
            // Validate dữ liệu đầu vào
            $request->validate([
                'old_password' => 'required|string',
                'new_password' => 'required|string|min:6|confirmed',
            ]);

            // Lấy user hiện tại từ token
            $user = $request->user();

            // Kiểm tra mật khẩu cũ có đúng không
            if (!Hash::check($request->old_password, $user->password)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Mật khẩu cũ không đúng'
                ], 400);
            }

            // Cập nhật mật khẩu mới
            $user->update([
                'password' => Hash::make($request->new_password)
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Đổi mật khẩu thành công'
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Lỗi: ' . $e->getMessage()
            ], 500);
        }
    }
}
