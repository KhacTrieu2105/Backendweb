<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
Route::get('/', function () {
    return view('welcome');
});

Route::get('/init-db', function () {
    try {
        // 1. Xóa sạch bảng cũ và tạo lại bảng mới (Fresh)
        // Lưu ý: Lệnh này sẽ xóa sạch dữ liệu cũ nếu có
        Artisan::call('migrate:fresh', ['--force' => true]);

        // 2. Đổ dữ liệu từ DatabaseSeeder
        Artisan::call('db:seed', ['--force' => true]);
        
        return "THÀNH CÔNG RỰC RỠ! Đã làm mới database và đổ dữ liệu mẫu.";
    } catch (\Exception $e) {
        return "Lỗi rồi Triệu ơi: " . $e->getMessage();
    }
});