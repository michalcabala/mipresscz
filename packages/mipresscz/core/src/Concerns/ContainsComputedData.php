<?php

namespace MiPressCz\Core\Concerns;

use Illuminate\Support\Collection;
use MiPressCz\Core\Services\ComputedFieldRegistry;

trait ContainsComputedData
{
    protected bool $withComputedData = true;

    /**
     * Get all computed values for this entry.
     */
    public function computedData(): Collection
    {
        return $this->getComputedCallbacks()->map(fn ($_, $field) => $this->getComputed($field));
    }

    /**
     * Get all registered computed field keys.
     */
    public function computedKeys(): Collection
    {
        return $this->getComputedCallbacks()->keys();
    }

    /**
     * Get a single computed value by key.
     */
    public function getComputed(string $key): mixed
    {
        $instance = $this->instanceWithoutComputed();

        if ($this->withComputedData && ($callback = $this->getComputedCallbacks()->get($key))) {
            return $callback($instance);
        }

        return null;
    }

    /**
     * Check if a computed callback exists for the given key.
     */
    protected function hasComputedCallback(string $key): bool
    {
        return $this->getComputedCallbacks()->has($key);
    }

    /**
     * Get all registered callbacks from the registry, scoped by collection handle.
     */
    protected function getComputedCallbacks(): Collection
    {
        $scope = $this->collection?->handle ?? '';

        if ($scope === '') {
            return collect();
        }

        return app(ComputedFieldRegistry::class)->getCallbacks($scope);
    }

    /**
     * Clone this instance with computed data disabled (prevents recursion).
     */
    protected function instanceWithoutComputed(): static
    {
        $clone = clone $this;
        $clone->withComputedData = false;

        return $clone;
    }
}
