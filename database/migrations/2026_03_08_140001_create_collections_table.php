<?php

use App\Enums\DateBehavior;
use App\Enums\DefaultStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('collections', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('name')->unique();
            $table->string('title');
            $table->string('handle')->unique();
            $table->text('description')->nullable();
            $table->boolean('is_tree')->default(false);
            $table->string('route_template')->nullable();
            $table->string('sort_field')->default('order');
            $table->string('sort_direction')->default('asc');
            $table->string('date_behavior')->default(DateBehavior::None->value);
            $table->boolean('revisions_enabled')->default(false);
            $table->string('default_status')->default(DefaultStatus::Draft->value);
            $table->string('icon')->nullable();
            $table->boolean('is_active')->default(true);
            $table->json('settings')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('collections');
    }
};
