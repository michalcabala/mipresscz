<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Blocks were replaced by the Mason plugin. The blocks table is no
        // longer needed; Mason bricks live in app/Mason/ and config/mason.php.
        Schema::dropIfExists('blocks');
    }

    public function down(): void
    {
        Schema::create('blocks', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('name')->unique();
            $table->string('title');
            $table->string('handle')->unique();
            $table->json('fields')->nullable();
            $table->string('icon')->nullable();
            $table->string('description')->nullable();
            $table->string('preview_image')->nullable();
            $table->string('category')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('order')->default(0);
            $table->timestamps();
        });
    }
};
