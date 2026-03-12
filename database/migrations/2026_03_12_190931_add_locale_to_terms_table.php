<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $foreignKeys = collect(Schema::getForeignKeys('terms'))->pluck('name');
        $indexes = collect(Schema::getIndexes('terms'))->pluck('name');

        // Drop taxonomy_id FK if it still exists (needed to modify the composite unique)
        if ($foreignKeys->contains('terms_taxonomy_id_foreign')) {
            Schema::table('terms', function (Blueprint $table) {
                $table->dropForeign(['taxonomy_id']);
            });
        }

        // Drop old unique (taxonomy_id, slug) if it still exists
        if ($indexes->contains('terms_taxonomy_id_slug_unique')) {
            Schema::table('terms', function (Blueprint $table) {
                $table->dropUnique(['taxonomy_id', 'slug']);
            });
        }

        Schema::table('terms', function (Blueprint $table) use ($indexes, $foreignKeys) {
            // Add locale column if not yet present
            if (! Schema::hasColumn('terms', 'locale')) {
                $table->string('locale')->default('cs')->after('taxonomy_id');
            }

            // Add origin_id column + FK if not yet present
            if (! Schema::hasColumn('terms', 'origin_id')) {
                $table->foreignUlid('origin_id')->nullable()->after('locale');
                $table->foreign('origin_id')->references('id')->on('terms')->nullOnDelete();
            }

            // Add new unique (taxonomy_id, locale, slug) if not yet present
            if (! $indexes->contains('terms_taxonomy_id_locale_slug_unique')) {
                $table->unique(['taxonomy_id', 'locale', 'slug']);
            }

            // Re-add FK on taxonomy_id if still missing
            if (! $foreignKeys->contains('terms_taxonomy_id_foreign')) {
                $table->foreign('taxonomy_id')->references('id')->on('taxonomies')->cascadeOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('terms', function (Blueprint $table) {
            $table->dropForeign(['taxonomy_id']);
            $table->dropForeign(['origin_id']);
            $table->dropUnique(['taxonomy_id', 'locale', 'slug']);
            $table->dropColumn(['locale', 'origin_id']);
            $table->unique(['taxonomy_id', 'slug']);
            $table->foreign('taxonomy_id')->references('id')->on('taxonomies')->cascadeOnDelete();
        });
    }
};
