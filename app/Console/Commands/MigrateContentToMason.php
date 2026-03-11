<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use MiPressCz\Core\Models\Entry;

class MigrateContentToMason extends Command
{
    protected $signature = 'mipress:migrate-content-to-mason {--dry-run : Show changes without saving}';

    protected $description = 'Convert entries data.content HTML to Mason TextBrick in the content column';

    public function handle(): int
    {
        $dryRun = $this->option('dry-run');
        $migrated = 0;
        $skipped = 0;

        Entry::withTrashed()->each(function (Entry $entry) use ($dryRun, &$migrated, &$skipped) {
            // Skip if content column already has Mason blocks
            if (! empty($entry->content)) {
                $skipped++;

                return;
            }

            $data = $entry->data ?? [];
            $htmlContent = $data['content'] ?? null;

            if (! $htmlContent) {
                $skipped++;

                return;
            }

            $masonContent = [
                [
                    'type' => 'masonBrick',
                    'attrs' => [
                        'id' => 'text',
                        'config' => [
                            'content' => $htmlContent,
                        ],
                    ],
                ],
            ];

            $this->line("  Migrating entry: <info>{$entry->title}</info> (ID: {$entry->id})");

            if (! $dryRun) {
                $newData = array_filter($data, fn ($k) => $k !== 'content', ARRAY_FILTER_USE_KEY);
                $entry->update([
                    'content' => $masonContent,
                    'data' => $newData,
                ]);
            }

            $migrated++;
        });

        $action = $dryRun ? 'Would migrate' : 'Migrated';
        $this->info("{$action} {$migrated} entr(ies). Skipped: {$skipped}.");

        return self::SUCCESS;
    }
}
