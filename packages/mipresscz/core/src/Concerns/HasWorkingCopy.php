<?php

namespace MiPressCz\Core\Concerns;

use Illuminate\Foundation\Auth\User;
use MiPressCz\Core\Enums\EntryStatus;
use MiPressCz\Core\Models\Revision;

/**
 * Provides working copy (staging area) functionality for entries.
 *
 * A working copy is a special revision with action='working' that holds
 * unsaved changes for a published entry. At most one working copy
 * exists per entry at any time.
 */
trait HasWorkingCopy
{
    public function workingCopy(): ?Revision
    {
        return $this->revisions()->workingCopy()->first();
    }

    public function hasWorkingCopy(): bool
    {
        return $this->revisions()->workingCopy()->exists();
    }

    public function makeWorkingCopy(?User $user = null): Revision
    {
        $existing = $this->workingCopy();

        $attributes = [
            'entry_id' => $this->id,
            'user_id' => $user?->id ?? auth()->id() ?? $this->author_id,
            'title' => $this->title,
            'data' => $this->data,
            'content' => $this->content,
            'status' => $this->status->value,
            'action' => 'working',
            'is_current' => false,
            'created_at' => now(),
        ];

        if ($existing) {
            $existing->update($attributes);

            return $existing;
        }

        return Revision::create($attributes);
    }

    public function saveToWorkingCopy(array $attributes, ?User $user = null): Revision
    {
        $existing = $this->workingCopy();

        $data = [
            'entry_id' => $this->id,
            'user_id' => $user?->id ?? auth()->id() ?? $this->author_id,
            'title' => $attributes['title'] ?? $this->title,
            'data' => $attributes['data'] ?? $this->data,
            'content' => $attributes['content'] ?? $this->content,
            'status' => $attributes['status'] ?? $this->status->value,
            'action' => 'working',
            'is_current' => false,
            'created_at' => now(),
        ];

        if ($existing) {
            $existing->update($data);

            return $existing;
        }

        return Revision::create($data);
    }

    public function publishWorkingCopy(?User $user = null, ?string $message = null): static
    {
        $wc = $this->workingCopy();

        if (! $wc) {
            return $this;
        }

        $userId = $user?->id ?? auth()->id() ?? $this->author_id;

        // Apply working copy data to the entry
        $this->title = $wc->title;
        $this->data = $wc->data;
        $this->content = $wc->content;
        $this->status = EntryStatus::Published;
        $this->published_at ??= now();

        // Save entry without triggering another working copy via observer
        $this->saveQuietly();

        // Create a publish revision snapshot
        $this->revisions()->where('is_current', true)->update(['is_current' => false]);

        Revision::create([
            'entry_id' => $this->id,
            'user_id' => $userId,
            'title' => $this->title,
            'data' => $this->data,
            'content' => $this->content,
            'status' => EntryStatus::Published->value,
            'action' => 'publish',
            'message' => $message,
            'is_current' => true,
            'created_at' => now(),
        ]);

        // Delete the working copy
        $wc->delete();

        return $this;
    }

    public function deleteWorkingCopy(): bool
    {
        $wc = $this->workingCopy();

        if ($wc) {
            return $wc->delete();
        }

        return false;
    }

    public function fromWorkingCopy(): static
    {
        $wc = $this->workingCopy();

        if (! $wc) {
            return $this;
        }

        $clone = $this->replicate();
        $clone->id = $this->id;
        $clone->title = $wc->title;
        $clone->data = $wc->data;
        $clone->content = $wc->content;
        $clone->status = EntryStatus::from($wc->status);
        $clone->exists = true;

        return $clone;
    }
}
