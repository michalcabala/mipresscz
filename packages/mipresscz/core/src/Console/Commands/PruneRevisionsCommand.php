<?php

declare(strict_types=1);

namespace MiPressCz\Core\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use InvalidArgumentException;
use MiPressCz\Core\Services\RevisionService;

class PruneRevisionsCommand extends Command
{
    protected $signature = 'mipress:prune-revisions
        {--keep= : Maximum number of revisions to keep per revisionable record}
        {--model= : Short model name (for example Entry) or fully-qualified model class}
        {--dry-run : Show how many revisions would be pruned without deleting them}';

    protected $description = 'Prune old content revisions for enabled miPress revisionable models';

    public function handle(RevisionService $revisionService): int
    {
        $keepLast = $this->resolveKeepLast($revisionService);

        try {
            $modelClasses = $this->resolveModelClasses();
        } catch (InvalidArgumentException $exception) {
            $this->components->error($exception->getMessage());

            return self::FAILURE;
        }

        if ($modelClasses === []) {
            $this->components->warn('No revisionable models are configured for pruning.');

            return self::SUCCESS;
        }

        $rows = [];
        $totalRecords = 0;
        $totalPrunable = 0;

        foreach ($modelClasses as $modelClass) {
            $records = 0;
            $prunable = 0;

            foreach ($modelClass::query()->cursor() as $model) {
                ++$records;
                $prunable += $revisionService->countPrunableRevisions($model, $keepLast);
            }

            $totalRecords += $records;
            $totalPrunable += $prunable;

            if (! $this->option('dry-run') && $prunable > 0) {
                foreach ($modelClass::query()->cursor() as $model) {
                    $revisionService->pruneRevisions($model, $keepLast);
                }
            }

            $rows[] = [
                'model' => class_basename($modelClass),
                'records' => $records,
                'revisions' => $prunable,
            ];
        }

        $this->table(['Model', 'Records', $this->option('dry-run') ? 'Would prune' : 'Pruned'], $rows);

        if ($this->option('dry-run')) {
            $this->components->info(sprintf(
                'Dry run complete. %d revisions would be pruned across %d records.',
                $totalPrunable,
                $totalRecords,
            ));

            return self::SUCCESS;
        }

        $this->components->success(sprintf(
            'Pruned %d revisions across %d records.',
            $totalPrunable,
            $totalRecords,
        ));

        return self::SUCCESS;
    }

    private function resolveKeepLast(RevisionService $revisionService): int
    {
        $keepLast = $this->option('keep');

        if ($keepLast === null || $keepLast === '') {
            return $revisionService->configuredMaxRevisions();
        }

        return max(0, (int) $keepLast);
    }

    /**
     * @return list<class-string<Model>>
     */
    private function resolveModelClasses(): array
    {
        $configured = array_values(array_filter(
            config('mipress-revisions.enabled_models', []),
            fn (mixed $modelClass): bool => is_string($modelClass) && is_subclass_of($modelClass, Model::class),
        ));

        $requestedModel = $this->option('model');

        if (! is_string($requestedModel) || $requestedModel === '') {
            return $configured;
        }

        if (class_exists($requestedModel) && is_subclass_of($requestedModel, Model::class)) {
            return [$requestedModel];
        }

        $matched = collect($configured)
            ->first(fn (string $modelClass): bool => Str::lower(class_basename($modelClass)) === Str::lower($requestedModel));

        if (! is_string($matched)) {
            throw new InvalidArgumentException(sprintf(
                'Unable to resolve revisionable model [%s]. Add it to mipress-revisions.enabled_models or pass a valid class name.',
                $requestedModel,
            ));
        }

        return [$matched];
    }
}
