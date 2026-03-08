<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('blueprints', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('collection_id')->constrained('collections')->cascadeOnDelete();
            $table->string('name');
            $table->string('title');
            $table->string('handle');
            $table->json('fields')->nullable();
            $table->boolean('is_default')->default(false);
            $table->string('icon')->nullable();
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['collection_id', 'handle']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blueprints');
    }
};
