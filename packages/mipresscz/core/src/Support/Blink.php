<?php

namespace MiPressCz\Core\Support;

class Blink
{
    /** @var array<string, mixed> */
    protected array $store = [];

    public function get(string $key, mixed $default = null): mixed
    {
        return $this->store[$key] ?? $default;
    }

    public function put(string $key, mixed $value): void
    {
        $this->store[$key] = $value;
    }

    public function has(string $key): bool
    {
        return array_key_exists($key, $this->store);
    }

    public function forget(string $key): void
    {
        unset($this->store[$key]);
    }

    public function flush(): void
    {
        $this->store = [];
    }

    /**
     * Get a value from the cache, or resolve and cache it.
     */
    public function once(string $key, callable $callback): mixed
    {
        if ($this->has($key)) {
            return $this->get($key);
        }

        $value = $callback();
        $this->put($key, $value);

        return $value;
    }
}
