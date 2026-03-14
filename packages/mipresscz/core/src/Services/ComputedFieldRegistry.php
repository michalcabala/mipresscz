<?php

namespace MiPressCz\Core\Services;

use Closure;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class ComputedFieldRegistry
{
    public const WILDCARD = '*';

    /** @var array<string, Closure> Keyed as "scope.field" */
    private array $callbacks = [];

    /**
     * Register computed field callback(s) for one or more collection handles.
     * Use ComputedFieldRegistry::WILDCARD ('*') to register for all collections.
     *
     * @param  string|array<int, string>  $scopes  Collection handle(s) or '*' for all
     * @param  string|array<string, Closure>  $field  Field name or array of field => callback
     */
    public function register(string|array $scopes, string|array $field, ?Closure $callback = null): void
    {
        foreach (Arr::wrap($scopes) as $scope) {
            if (is_array($field)) {
                foreach ($field as $fieldName => $fieldCallback) {
                    $this->callbacks["{$scope}.{$fieldName}"] = $fieldCallback;
                }

                continue;
            }

            $this->callbacks["{$scope}.{$field}"] = $callback;
        }
    }

    /**
     * Get all registered callbacks for a given collection handle.
     * Merges wildcard ('*') callbacks with scope-specific ones.
     */
    public function getCallbacks(string $scope): Collection
    {
        $wildcardCallbacks = collect($this->callbacks)
            ->filter(fn ($_, $key) => str_starts_with($key, self::WILDCARD.'.'))
            ->keyBy(fn ($_, $key) => substr($key, 2));

        $scopedCallbacks = collect($this->callbacks)
            ->filter(fn ($_, $key) => str_starts_with($key, "{$scope}."))
            ->keyBy(fn ($_, $key) => substr($key, strlen($scope) + 1));

        // Scoped callbacks override wildcard callbacks
        return $wildcardCallbacks->merge($scopedCallbacks);
    }

    /**
     * Check if a specific computed field is registered for a scope (or wildcard).
     */
    public function has(string $scope, string $field): bool
    {
        return isset($this->callbacks["{$scope}.{$field}"])
            || isset($this->callbacks[self::WILDCARD.".{$field}"]);
    }
}
