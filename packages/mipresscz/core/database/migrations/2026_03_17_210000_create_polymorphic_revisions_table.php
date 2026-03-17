<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('revisions', function (Blueprint $table): void {
            $table->ulid('id')->primary();
            $table->ulidMorphs('revisionable');
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('type');
            $table->text('note')->nullable();
            $table->unsignedInteger('revision_number');
            $table->json('content');
            $table->timestamp('created_at')->nullable();
            $table->softDeletes();

            $table->index(['revisionable_type', 'revisionable_id', 'created_at'], 'revisions_revisionable_created_index');
            $table->unique(['revisionable_type', 'revisionable_id', 'revision_number'], 'revisions_revisionable_number_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('revisions');
    }
};
