<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('entries', function (Blueprint $table) {
            $table->string('meta_title')->nullable()->after('featured_image_id');
            $table->text('meta_description')->nullable()->after('meta_title');
            $table->unsignedBigInteger('meta_og_image_id')->nullable()->after('meta_description');
            $table->foreign('meta_og_image_id')->references('id')->on('curator')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('entries', function (Blueprint $table) {
            $table->dropForeign(['meta_og_image_id']);
            $table->dropColumn(['meta_title', 'meta_description', 'meta_og_image_id']);
        });
    }
};
