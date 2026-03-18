<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;

class AuthController extends Controller
{
    /**
     * Xử lý đăng nhập
     */
    public function login(Request $request)
    {
        // 1. Validate dữ liệu
        $validator = $request->validate([
            'identifier' => 'required|string',
            'password'   => 'required|string',
        ]);

        $identifier = $request->identifier;
        $password = $request->password;

        // 2. Xác định loại đăng nhập: Email, Phone hay Username
        $field = filter_var($identifier, FILTER_VALIDATE_EMAIL)
            ? 'email'
            : (preg_match('/^[0-9]{9,11}$/', $identifier) ? 'phone' : 'username');

        // 3. Thử đăng nhập
        if (!Auth::attempt([$field => $identifier, 'password' => $password])) {
            return response()->json([
                'status'  => false,
                'message' => 'Thông tin đăng nhập không chính xác',
            ], 401);
        }

        /** @var \App\Models\User $user */
        $user = Auth::user();

        // 4. Kiểm tra trạng thái tài khoản
        if ($user->status == 0) {
            // Xóa session nếu có (đề phòng)
            Auth::guard('web')->logout(); 
            return response()->json([
                'status'  => false,
                'message' => 'Tài khoản của bạn đã bị khóa hoặc chưa kích hoạt.',
            ], 403);
        }

        // 5. Tạo Token (Sanctum)
        // Xóa các token cũ để tránh rác (tùy chọn)
        // $user->tokens()->delete(); 
        $token = $user->createToken('auth_token')->plainTextToken;

        // 6. Trả về kết quả (QUAN TRỌNG: Phải có status => true)
        return response()->json([
            'status'  => true,
            'message' => 'Đăng nhập thành công',
            'user'    => $user,
            'token'   => $token,
        ]);
    }

    /**
     * Xử lý đăng ký
     */
    public function register(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users,email',
            'phone'    => 'nullable|string|max:20|unique:users,phone', // Nên check unique phone
            'password' => 'required|string|min:6|confirmed',
        ]);

        // Tạo username từ email (cắt phần trước @)
        // Ví dụ: abc@gmail.com -> abc
        $baseUsername = explode('@', $request->email)[0];
        $username = $baseUsername;
        
        // Kiểm tra nếu username đã tồn tại thì thêm số ngẫu nhiên
        if (User::where('username', $username)->exists()) {
            $username = $baseUsername . rand(100, 999);
        }

        $user = User::create([
            'name'       => $request->name,
            'email'      => $request->email,
            'phone'      => $request->phone,
            'username'   => $username,
            'password'   => Hash::make($request->password),
            'roles'      => 'customer', // Mặc định là khách hàng
            'status'     => 1,          // 1: Hoạt động, 0: Khóa
            'created_at' => now(),
            'created_by' => 0,          // 0 hoặc null biểu thị tự đăng ký
        ]);

        // Tự động tạo token sau khi đăng ký thành công (để login luôn)
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status'  => true,
            'message' => 'Đăng ký tài khoản thành công',
            'data'    => $user,
            'token'   => $token // Trả về token luôn nếu muốn auto login
        ], 201);
    }

    /**
     * Lấy thông tin User hiện tại (Profile)
     * Dùng cho API /user-profile
     */
    public function showProfile(Request $request)
    {
        return response()->json([
            'status' => true,
            'user'   => $request->user()
        ]);
    }

    /**
     * Cập nhật thông tin User hiện tại (Profile)
     * Dùng cho API /user-profile (PUT/PATCH)
     */
    public function updateProfile(Request $request)
    {
        try {
            // 1. Lấy User từ Token
            /** @var \App\Models\User $user */
            $user = Auth::user();
            if (!$user) {
                return response()->json(['status' => false, 'message' => 'Không tìm thấy người dùng'], 401);
            }

            // 2. Validate dữ liệu (Chú ý: Email unique ngoại trừ user hiện tại)
            $request->validate([
                'name'    => 'required|string|max:255',
                'phone'   => 'nullable|string|max:15',
                'address' => 'nullable|string',
                'avatar'  => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            // 3. Cập nhật thông tin cơ bản
            $user->name = $request->name;
            $user->phone = $request->phone;
            $user->address = $request->address;

            // 4. Xử lý Avatar (nếu có gửi file)
            if ($request->hasFile('avatar')) {
                $file = $request->file('avatar');
                $extension = $file->getClientOriginalExtension();
                $filename = time() . '.' . $extension;
                $file->move(public_path('storage/avatars'), $filename);
                $user->avatar = 'avatars/' . $filename;
            }

            // 5. LƯU VÀO DATABASE
            $user->save();

            return response()->json([
                'status' => true,
                'message' => 'Cập nhật hồ sơ thành công!',
                'user' => $user
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Lỗi: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Đăng xuất
     */
    public function logout(Request $request)
    {
        // Xóa token hiện tại đang dùng
        if ($request->user()) {
            $request->user()->currentAccessToken()->delete();
        }

        return response()->json([
            'status'  => true,
            'message' => 'Đăng xuất thành công'
        ]);
    }
}
