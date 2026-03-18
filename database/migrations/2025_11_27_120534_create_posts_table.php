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
    Schema::create('posts', function (Blueprint $table) {
        $table->id();
        $table->unsignedInteger('topic_id')->nullable();
        $table->string('title');
        $table->string('slug');
        $table->string('image');
        $table->longText('content');
        $table->tinyText('description')->nullable();
        $table->enum('post_type', ['post', 'page'])->default('post');
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
        Schema::dropIfExists('posts');
    }
};
