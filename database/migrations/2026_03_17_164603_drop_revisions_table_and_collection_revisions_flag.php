<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('revisions');

        if (Schema::hasTable('collections') && Schema::hasColumn('collections', 'revisions_enabled')) {
            Schema::table('collections', function (Blueprint $table): void {
                $table->dropColumn('revisions_enabled');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('collections') && ! Schema::hasColumn('collections', 'revisions_enabled')) {
            Schema::table('collections', function (Blueprint $table): void {
                $table->boolean('revisions_enabled')->default(false);
            });
        }

        if (! Schema::hasTable('revisions')) {
            Schema::create('revisions', function (Blueprint $table): void {
                $table->ulid('id')->primary();
                $table->foreignUlid('entry_id')->constrained('entries')->cascadeOnDelete();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->string('title');
                $table->json('data')->nullable();
                $table->json('content')->nullable();
                $table->json('snapshot')->nullable();
                $table->string('status');
                $table->string('action')->default('revision');
                $table->string('message')->nullable();
                $table->boolean('is_current')->default(false);
                $table->timestamp('created_at')->nullable();
            });
        }
    }
};
