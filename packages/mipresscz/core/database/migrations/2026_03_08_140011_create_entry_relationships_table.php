<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('entry_relationships', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('parent_entry_id')->constrained('entries')->cascadeOnDelete();
            $table->foreignUlid('related_entry_id')->constrained('entries')->cascadeOnDelete();
            $table->string('field_handle');
            $table->integer('order')->default(0);

            $table->index(['parent_entry_id', 'field_handle']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('entry_relationships');
    }
};
