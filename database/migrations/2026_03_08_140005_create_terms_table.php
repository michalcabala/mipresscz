<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('terms', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('taxonomy_id')->constrained('taxonomies')->cascadeOnDelete();
            $table->ulid('parent_id')->nullable();
            $table->string('title');
            $table->string('slug');
            $table->text('description')->nullable();
            $table->json('data')->nullable();
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('parent_id')->references('id')->on('terms')->nullOnDelete();
            $table->unique(['taxonomy_id', 'slug']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('terms');
    }
};
