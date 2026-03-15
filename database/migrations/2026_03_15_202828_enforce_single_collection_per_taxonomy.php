<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $assignments = DB::table('collection_taxonomy')
            ->join('collections', 'collections.id', '=', 'collection_taxonomy.collection_id')
            ->select('collection_taxonomy.collection_id', 'collection_taxonomy.taxonomy_id')
            ->orderBy('collections.handle')
            ->get()
            ->groupBy('taxonomy_id');

        foreach ($assignments as $taxonomyAssignments) {
            $taxonomyAssignments
                ->skip(1)
                ->each(function (object $assignment): void {
                    DB::table('collection_taxonomy')
                        ->where('collection_id', $assignment->collection_id)
                        ->where('taxonomy_id', $assignment->taxonomy_id)
                        ->delete();
                });
        }

        Schema::table('collection_taxonomy', function (Blueprint $table): void {
            $table->unique('taxonomy_id');
        });
    }

    public function down(): void
    {
        Schema::table('collection_taxonomy', function (Blueprint $table): void {
            $table->dropUnique('collection_taxonomy_taxonomy_id_unique');
        });
    }
};
