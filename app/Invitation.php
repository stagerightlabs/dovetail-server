<?php

namespace App;

use App\Model;

class Invitation extends Model
{
    protected $fillable = ['organization_id', 'email', 'revoked', 'completed_at'];
    protected $dates = ['created_at', 'updated_at', 'completed_at'];

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function getCodeAttribute()
    {
        return hashid($this->id, 'invitation');
    }
}
