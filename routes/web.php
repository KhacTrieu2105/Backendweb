<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
Route::get('/', function () {
    return view('welcome');
});


Route::get('/init-db', function () {
    try {
        // Tắt kiểm tra khóa ngoại trên PostgreSQL
        DB::statement('SET CONSTRAINTS ALL DEFERRED'); 
        
        // Chạy migrate thường (không fresh) để nó cố gắng tạo những bảng còn thiếu
        Artisan::call('migrate', ['--force' => true]);
        
        return "Database đã cố gắng khởi tạo các bảng! Bạn có thể thử kết nối Frontend.";
    } catch (\Exception $e) {
        // Nếu vẫn lỗi, ta dùng phương án 'mạnh' hơn là bỏ qua lỗi ràng buộc
        return "Lỗi: " . $e->getMessage() . ". Hãy kiểm tra tên các file migration.";
    }
});