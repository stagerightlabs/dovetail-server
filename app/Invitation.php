<?php

namespace App;

use App\User;
use App\Model;
use Illuminate\Support\Carbon;

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

    public function revoker()
    {
        return $this->belongsTo(User::class, 'revoked_by');
    }

    public function complete()
    {
        $this->completed_at = Carbon::now();
        $this->save();
    }
}
