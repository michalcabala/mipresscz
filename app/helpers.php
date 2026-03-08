<?php

use App\Models\GlobalSet;

if (! function_exists('global_set')) {
    /**
     * Get a value from a global set.
     */
    function global_set(string $handle, ?string $key = null, mixed $default = null): mixed
    {
        if ($key === null) {
            return GlobalSet::findByHandle($handle);
        }

        return GlobalSet::getValue($handle, $key, $default);
    }
}
