<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use MiPressCz\Core\Models\Blueprint;

class MigrateBlueprintFields extends Command
{
    protected $signature = 'mipress:migrate-blueprint-fields {--dry-run : Show changes without saving}';

    protected $description = 'Rename media field type to curator in all blueprint field definitions';

    public function handle(): int
    {
        $dryRun = $this->option('dry-run');
        $updated = 0;

        Blueprint::query()->each(function (Blueprint $blueprint) use ($dryRun, &$updated) {
            $fields = $blueprint->fields ?? [];
            $changed = false;

            $fields = array_map(function (array $field) use (&$changed) {
                if (($field['type'] ?? '') === 'media') {
                    $field['type'] = 'curator';
                    $changed = true;
                }

                return $field;
            }, $fields);

            if ($changed) {
                $this->line("  Updating blueprint: <info>{$blueprint->title}</info> ({$blueprint->handle})");
                if (! $dryRun) {
                    $blueprint->update(['fields' => $fields]);
                }
                $updated++;
            }
        });

        $action = $dryRun ? 'Would update' : 'Updated';
        $this->info("{$action} {$updated} blueprint(s).");

        return self::SUCCESS;
    }
}
