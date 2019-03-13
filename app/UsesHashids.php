<?php

namespace App;

/**
 * A hashid helper for model classes
 */
trait UsesHashids
{
    /**
     * Return the model's hashid as a string
     *
     * @return string
     */
    public function getHashidAttribute()
    {
        return hashid($this->id);
    }
}
