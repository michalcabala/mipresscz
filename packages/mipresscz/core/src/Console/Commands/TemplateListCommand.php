<?php

namespace MiPressCz\Core\Console\Commands;

use Illuminate\Console\Command;
use MiPressCz\Core\Services\TemplateManager;

class TemplateListCommand extends Command
{
    protected $signature = 'mipresscz:template:list';

    protected $description = 'List all available frontend templates';

    public function handle(TemplateManager $templateManager): int
    {
        $templates = $templateManager->getAvailable();
        $active = $templateManager->getActive();

        if ($templates->isEmpty()) {
            $this->warn('No templates found in resources/views/templates/');
            $this->line('Create a template directory with a template.json metadata file.');

            return self::SUCCESS;
        }

        $this->table(
            ['Active', 'Slug', 'Name', 'Version', 'Author', 'Description'],
            $templates->map(fn (array $t): array => [
                $t['slug'] === $active ? '<fg=green>✓</>' : '',
                $t['slug'] ?? '—',
                $t['name'] ?? '—',
                $t['version'] ?? '—',
                $t['author'] ?? '—',
                isset($t['description']) ? mb_strimwidth($t['description'], 0, 50, '…') : '—',
            ])
        );

        $this->newLine();
        $this->line("Active template: <fg=green>{$active}</>");

        return self::SUCCESS;
    }
}
