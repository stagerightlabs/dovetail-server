<?php

namespace App;

use Illuminate\Database\Eloquent\Model as EloquentModel;

class Model extends EloquentModel
{
    public function getHashidAttribute()
    {
        return hashid($this->id);
    }
}
