<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up()
{
    Schema::create('products', function (Blueprint $table) {
        $table->id();
        $table->unsignedInteger('category_id');
        $table->string('name');
        $table->string('slug');
        $table->string('thumbnail');
        $table->longText('content');
        $table->tinyText('description')->nullable();
        $table->decimal('price_buy', 12, 2);
        $table->datetime('created_at');
        $table->unsignedInteger('created_by')->default(1);
        $table->datetime('updated_at')->nullable();
        $table->unsignedInteger('updated_by')->nullable();
        $table->unsignedTinyInteger('status')->default(1);
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
