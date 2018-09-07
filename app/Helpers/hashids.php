<?php

use Vinkla\Hashids\Facades\Hashids;

/**
 * A helper method for quickly encoding and decoding hashids
 *
 * @param  int|string $value
 * @param  string $connection
 * @return int|string|null
 */
function hashid($value, $connection = 'main')
{
    // Have we been given an integer?
    if (is_integer($value)) {
        return Hashids::connection($connection)->encode($value);
    }

    if (is_string($value) && $decoded = Hashids::connection($connection)->decode($value)) {
        return $decoded[0];
    }

    // Otherwise
    return null;
}
