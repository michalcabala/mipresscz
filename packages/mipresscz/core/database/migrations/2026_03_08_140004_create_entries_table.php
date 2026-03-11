<?php

use MiPressCz\Core\Enums\EntryStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('entries', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('collection_id')->constrained('collections')->cascadeOnDelete();
            $table->foreignUlid('blueprint_id')->constrained('blueprints')->cascadeOnDelete();
            $table->ulid('origin_id')->nullable();
            $table->string('locale')->default('cs');
            $table->string('title');
            $table->string('slug');
            $table->string('uri')->nullable();
            $table->json('data')->nullable();
            $table->string('status')->default(EntryStatus::Draft->value);
            $table->timestamp('published_at')->nullable();
            $table->timestamp('expired_at')->nullable();
            $table->ulid('parent_id')->nullable();
            $table->integer('order')->default(0);
            $table->foreignId('author_id')->constrained('users')->cascadeOnDelete();
            $table->boolean('is_pinned')->default(false);
            $table->json('settings')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('origin_id')->references('id')->on('entries')->nullOnDelete();
            $table->foreign('parent_id')->references('id')->on('entries')->nullOnDelete();
            $table->unique(['collection_id', 'slug', 'locale']);
            $table->unique('uri');
            $table->index(['status', 'published_at']);
            $table->index('locale');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('entries');
    }
};
