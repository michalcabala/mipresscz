<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('global_sets', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('name')->unique();
            $table->string('title');
            $table->string('handle')->unique();
            $table->json('fields')->nullable();
            $table->json('data')->nullable();
            $table->string('locale')->default('cs');
            $table->ulid('origin_id')->nullable();
            $table->timestamps();

            $table->foreign('origin_id')->references('id')->on('global_sets')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('global_sets');
    }
};
