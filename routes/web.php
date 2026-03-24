<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
Route::get('/', function () {
    return view('welcome');
});


Route::get('/init-db', function () {
    try {
        Artisan::call('migrate:fresh --force');
        return "Database đã được khởi tạo và tạo bảng thành công!";
    } catch (\Exception $e) {
        return "Lỗi: " . $e->getMessage();
    }
});