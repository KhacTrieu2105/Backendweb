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
    Schema::create('contacts', function (Blueprint $table) {
        $table->id();
        $table->unsignedInteger('user_id')->nullable();
        $table->string('name');
        $table->string('email');
        $table->string('phone');
        $table->mediumText('content');
        $table->unsignedInteger('reply_id')->default(0);
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
        Schema::dropIfExists('contacts');
    }
};
