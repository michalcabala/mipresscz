<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('termables', function (Blueprint $table) {
            $table->foreignUlid('term_id')->constrained('terms')->cascadeOnDelete();
            $table->ulidMorphs('termable');
            $table->integer('order')->default(0);

            $table->primary(['term_id', 'termable_id', 'termable_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('termables');
    }
};
