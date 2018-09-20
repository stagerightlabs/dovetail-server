<?php

namespace App;

use Illuminate\Database\Eloquent\Model as EloquentModel;

class Model extends EloquentModel
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
