<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
Route::get('/', function () {
    return view('welcome');
});


Route::get('/init-db', function () {
    try {
        // Tách 'migrate:fresh' và mảng tham số ['--force' => true]
        Artisan::call('migrate:fresh', ['--force' => true]);
        return "Database đã được khởi tạo và tạo bảng thành công!";
    } catch (\Exception $e) {
        return "Lỗi: " . $e->getMessage();
    }
});