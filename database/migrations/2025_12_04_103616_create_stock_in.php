<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ✅ CÁCH 2: CHỈ TẠO NẾU CHƯA TỒN TẠI
        if (!Schema::hasTable('stock_insr')) {
            Schema::create('stock_insr', function (Blueprint $table) {
                $table->id();
                $table->string('code')->unique(); // NK000001
                $table->unsignedBigInteger('supplier_id')->nullable();
                $table->decimal('total_amount', 15, 2)->default(0);
                $table->text('note')->nullable();
                $table->tinyInteger('status')->default(1);
                $table->unsignedBigInteger('created_by')->nullable();
                $table->unsignedBigInteger('updated_by')->nullable();
                $table->timestamps();

                // Foreign keys
                $table->foreign('supplier_id')
                      ->references('id')->on('suppliers')
                      ->onDelete('set null');

                $table->foreign('created_by')
                      ->references('id')->on('users')
                      ->onDelete('cascade');

                $table->foreign('updated_by')
                      ->references('id')->on('users')
                      ->onDelete('set null');
            });
        }
    }

    public function down(): void
    {
        // ❌ KHÔNG DROP để tránh mất dữ liệu
    }
};
