<?php

/**
 * Sanitize a string to remove malicious scripts and other content
 * that we don't want to store in our database.
 *
 * @param string $value
 * @return string
 */
function sanitize($value)
{
    return app(HTMLPurifier::class)->purify($value);
}
