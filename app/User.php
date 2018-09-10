<?php

namespace App;

use App\AccessLevel;
use App\Organization;
use Illuminate\Support\Collection;
use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, Notifiable, SoftDeletes;

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
        'phone_verified_at',
        'deleted_at',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'permission_flags' => 'array'
    ];

    /**
     * The organization that this user belongs to
     *
     * @return Organization
     */
    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    /**
     * Scope a query to members of a given organization.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param Organization $organization
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeInOrganization($query, $organization = null)
    {
        if (!$organization) {
            $organization = request()->organization;
        }

        if ($organization instanceof Organization) {
            $organization = $organization->id;
        }

        return $query->where('organization_id', $organization);
    }

    /**
     * Is this user a 'super-admin'?
     *
     * @return boolean
     */
    public function isSuperAdmin()
    {
        return $this->access_level >= AccessLevel::$SUPER_ADMIN;
    }

    /**
     * Return an appropriate label for this user's access level
     *
     * @return string
     */
    public function getRankAttribute()
    {
        return AccessLevel::rank($this->access_level);
    }

    /**
     * Return this users ID as a hashid
     *
     * @return string
     */
    public function getHashidAttribute()
    {
        return hashid($this->id);
    }

    /**************************************************************************
     * Permissions
     **************************************************************************/

    /**
     * Return the user's permissions as a collection
     *
     * @return Collection
     */
    public function getPermissionsAttribute()
    {
        $permissions = $this->permission_flags ?? [];

        return collect(array_merge(self::$defaultPermissions, $permissions));
    }

    /**
     * The default permission set for every user
     *
     * @var array
     */
    public static $defaultPermissions = [
        'notebooks.create' => false,
        'notebooks.update' => false,
        'notebooks.delete' => false
    ];

    /**
     * Check to see if a user has been granted a specific permission
     *
     * @param string $check
     * @return boolean
     */
    public function isAllowedTo($check)
    {
        return $this->permissions->get($check, false);
    }

    /**
     * Set the permissions for a user, either one at a time or as a group
     *
     * @param Collection|array|string $permission
     * @param boolean $value
     * @return void
     */
    public function applyPermissions($permission, $value = false)
    {
        if ($permission instanceof Collection) {
            $permission = $permission->toArray();
        }

        if (!is_array($permission)) {
            $permission = [$permission => $value];
        }

        $existing = $this->permission_flags ?? [];

        $this->permission_flags = array_merge($existing, $permission);
    }
}
