<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('global_sets', function (Blueprint $table) {
            $table->dropUnique(['handle']);
            $table->dropUnique(['name']);
            $table->unique(['handle', 'locale']);
            $table->unique(['name', 'locale']);
        });
    }

    public function down(): void
    {
        Schema::table('global_sets', function (Blueprint $table) {
            $table->dropUnique(['handle', 'locale']);
            $table->dropUnique(['name', 'locale']);
            $table->unique(['handle']);
            $table->unique(['name']);
        });
    }
};
