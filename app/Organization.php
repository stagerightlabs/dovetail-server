<?php

namespace App;

use App\User;
use App\Model;
use App\Invitation;

class Organization extends Model
{
    protected $fillable = ['name'];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function invitations()
    {
        return $this->hasMany(Invitation::class);
    }
}
