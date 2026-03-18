<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ✅ Tránh lỗi table đã tồn tại
        if (!Schema::hasTable('stock_in_items')) {

            Schema::create('stock_in_items', function (Blueprint $table) {
                $table->id();

                // 🔥 SỬA ĐÚNG TÊN BẢNG CHA
                $table->unsignedBigInteger('stock_in_id');
                $table->unsignedBigInteger('product_id');

                $table->integer('quantity');
                $table->decimal('price', 15, 2);

                $table->timestamps();

                // ===== FOREIGN KEYS =====
                $table->foreign('stock_in_id')
                      ->references('id')
                      ->on('stock_insr')   // ✅ ĐÚNG TÊN
                      ->onDelete('cascade');

                $table->foreign('product_id')
                      ->references('id')
                      ->on('products');
            });
        }
    }

    public function down(): void
    {
        // ❌ Không drop để tránh mất dữ liệu
    }
};
