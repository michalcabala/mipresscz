<?php

declare(strict_types=1);

namespace MiPressCz\Core\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use InvalidArgumentException;
use MiPressCz\Core\Enums\RevisionType;
use MiPressCz\Core\Models\Revision;
use RuntimeException;

class RevisionService
{
    public function createRevision(Model $model, RevisionType $type, ?string $note = null): Revision
    {
        $this->guardRevisionable($model);

        /** @var Revision $revision */
        $revision = $model->createRevision($type, $note);

        return $revision;
    }

    public function restoreRevision(Revision $revision): Model
    {
        $model = $revision->revisionable;

        if (! $model instanceof Model) {
            throw new RuntimeException('The revision cannot be restored because its revisionable model is missing.');
        }

        $this->guardRevisionable($model);

        $attributes = Arr::only($revision->content ?? [], $model->getFillable());

        if (method_exists($model, 'withoutAutomaticRevisions')) {
            $model->withoutAutomaticRevisions(function () use ($model, $attributes): void {
                $model->fill($attributes);
                $model->save();
            });
        } else {
            $model->fill($attributes);
            $model->save();
        }

        $this->createRevision(
            $model,
            RevisionType::Rollback,
            sprintf('Restored from revision #%d', $revision->revision_number),
        );

        return $model->fresh() ?? $model;
    }

    /**
     * @return array{
     *     added: array<int, array{field: string, old: mixed, new: mixed}>,
     *     removed: array<int, array{field: string, old: mixed, new: mixed}>,
     *     changed: array<int, array{field: string, old: mixed, new: mixed}>
     * }
     */
    public function diffRevisions(Revision $old, Revision $new): array
    {
        return $this->diffSnapshots($old->content ?? [], $new->content ?? []);
    }

    /**
     * @param  array<string, mixed>  $old
     * @param  array<string, mixed>  $new
     * @return array{
     *     added: array<int, array{field: string, old: mixed, new: mixed}>,
     *     removed: array<int, array{field: string, old: mixed, new: mixed}>,
     *     changed: array<int, array{field: string, old: mixed, new: mixed}>
     * }
     */
    public function diffSnapshots(array $old, array $new): array
    {
        $oldFlat = Arr::dot($old);
        $newFlat = Arr::dot($new);

        $added = [];
        $removed = [];
        $changed = [];

        $allFields = collect(array_keys($oldFlat))
            ->merge(array_keys($newFlat))
            ->unique()
            ->sort()
            ->values();

        foreach ($allFields as $field) {
            $hasOld = array_key_exists($field, $oldFlat);
            $hasNew = array_key_exists($field, $newFlat);
            $oldValue = $oldFlat[$field] ?? null;
            $newValue = $newFlat[$field] ?? null;

            if (! $hasOld && $hasNew) {
                $added[] = [
                    'field' => $field,
                    'old' => null,
                    'new' => $newValue,
                ];

                continue;
            }

            if ($hasOld && ! $hasNew) {
                $removed[] = [
                    'field' => $field,
                    'old' => $oldValue,
                    'new' => null,
                ];

                continue;
            }

            if ($oldValue !== $newValue) {
                $changed[] = [
                    'field' => $field,
                    'old' => $oldValue,
                    'new' => $newValue,
                ];
            }
        }

        return [
            'added' => $added,
            'removed' => $removed,
            'changed' => $changed,
        ];
    }

    public function pruneRevisions(Model $model, int $keepLast = 50): int
    {
        $this->guardRevisionable($model);

        $keepLast = max(0, $keepLast);

        $revisionIds = $model->revisions()
            ->where('type', '!=', RevisionType::Published->value)
            ->get(['id'])
            ->skip($keepLast)
            ->pluck('id');

        if ($revisionIds->isEmpty()) {
            return 0;
        }

        return Revision::query()
            ->whereIn('id', $revisionIds)
            ->delete();
    }

    private function guardRevisionable(Model $model): void
    {
        if (! method_exists($model, 'createRevision') || ! method_exists($model, 'revisions')) {
            throw new InvalidArgumentException(sprintf('Model [%s] does not support revisions.', $model::class));
        }
    }
}
