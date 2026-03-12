<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('menu_items', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('menu_id')->constrained('menus')->cascadeOnDelete();
            $table->ulid('parent_id')->nullable();
            $table->integer('order')->default(0);
            $table->string('type')->default('custom_link');
            $table->string('title');
            $table->string('url')->nullable();
            $table->string('target')->default('_self');
            $table->foreignUlid('entry_id')->nullable()->constrained('entries')->nullOnDelete();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('parent_id')
                ->references('id')
                ->on('menu_items')
                ->nullOnDelete();

            $table->index(['menu_id', 'parent_id', 'order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('menu_items');
    }
};
