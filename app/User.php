<?php

namespace App;

use App\AccessLevel;
use App\Organization;
use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'organization_id', 'access_level'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'created_at',
        'updated_at',
        'email_verified_at',
        'phone_verified_at'
        // 'deleted_at'
    ];

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function isSuperAdmin()
    {
        return $this->access_level >= AccessLevel::$SUPER_ADMIN;
    }

    public function getRankAttribute()
    {
        return AccessLevel::rank($this->access_level);
    }

    public function getHashidAttribute()
    {
        return hashid($this->id);
    }
}
