<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Lấy user từ guard sanctum
        $user = Auth::guard('sanctum')->user();

        // 2. Kiểm tra user tồn tại VÀ đúng cột 'roles' (kiểm tra lại database của bạn là role hay roles)
        if ($user && ($user->roles === 'admin' || $user->role === 'admin')) {
            return $next($request);
        }

        // Trả về lỗi 403 nếu không đủ quyền
        return response()->json([
            'status' => false,
            'message' => 'Bạn không có quyền truy cập vào khu vực quản trị! (Admin Middleware chặn)',
            'debug_user_role' => $user ? ($user->roles ?? $user->role) : 'Guest'
        ], 403);
    }
}