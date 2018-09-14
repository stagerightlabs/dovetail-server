<?php

namespace App;

use App\User;
use App\Model;
use App\Organization;
use App\Events\CategoryDeletion;

class Category extends Model
{
    protected $fillable = ['name', 'organization_id', 'created_by'];
    protected $dispatchesEvents = [
        'deleting' => CategoryDeletion::class
    ];

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
