<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('locales', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('code', 10)->unique();
            $table->string('name', 100);
            $table->string('native_name', 100);
            $table->string('flag', 50)->nullable();
            $table->boolean('is_default')->default(false);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_admin_available')->default(true);
            $table->boolean('is_frontend_available')->default(true);
            $table->enum('direction', ['ltr', 'rtl'])->default('ltr');
            $table->string('date_format', 30)->default('d.m.Y');
            $table->string('url_prefix', 10)->nullable()->unique();
            $table->string('fallback_locale', 10)->nullable();
            $table->unsignedSmallInteger('order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('locales');
    }
};
