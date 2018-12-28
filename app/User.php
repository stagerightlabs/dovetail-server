<?php

namespace App;

use App\Team;
use App\AccessLevel;
use App\Organization;
use App\Events\UserCreated;
use App\Notifications\VerifyEmail;
use Illuminate\Support\Collection;
use Laravel\Passport\HasApiTokens;
use Illuminate\Database\Query\Builder;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Notifications\ResetPassword as ResetPasswordNotification;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, Notifiable, SoftDeletes;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

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
     * The event map for the model.
     *
     * @var array
     */
    protected $dispatchesEvents = [
        'created' => UserCreated::class
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
     * This user's avatar
     *
     * @return MorphOne
     */
    public function avatar()
    {
        return $this->morphOne(Logo::class, 'owner')->withDefault([
            'url_original' => ''
        ]);
    }

    /**
     * The notebooks that belong to this user alone
     *
     * @return HasMany
     */
    public function notebooks()
    {
        return $this->hasMany(Notebook::class, 'user_id')->whereNull('team_id');
    }

    /**
     * All of the notebooks that this user has access to
     *
     * @return Builder
     */
    public function availableNotebooks()
    {
        return Notebook::where(function ($query) {
            return $query->where('organization_id', $this->organization_id)
                ->whereNull('team_id')
                ->whereNull('user_id');
        })->orWhere(function ($query) {
            return $query->where('user_id', $this->id)
                ->whereNull('team_id');
        })->orWhere(function ($query) {
            return $query->whereIn('team_id', $this->teams()->pluck('teams.id'))
                ->whereNull('user_id');
        });
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
            $organization = request()->organization();
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
     * Is this user an organization administrator?
     *
     * @return boolean
     */
    public function isOrganizationAdministrator()
    {
        return $this->access_level >= AccessLevel::$ORGANIZATION_ADMIN;
    }

    /**
     * Has this user been flagged for read only access?
     *
     * @return boolean
     */
    public function isReadOnly()
    {
        return $this->access_level == AccessLevel::$ORGANIZATION_READ_ONLY;
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

    /**
     * Fetch this user's teams
     *
     * @return BelongsToMany
     */
    public function teams()
    {
        return $this->belongsToMany(Team::class);
    }

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
        'teams.create' => false,
        'teams.update' => false,
        'teams.membership' => false,
        'teams.delete' => false,
        'notebooks.create' => false,
        'notebooks.update' => false,
        'notebooks.delete' => false,
        'notebooks.pages' => false,
    ];

    /**
     * The default permission set assigned to new administrators
     *
     * @var array
     */
    public static $defaultAdminPermissions = [
        'teams.create' => true,
        'teams.update' => true,
        'teams.membership' => true,
        'teams.delete' => true,
        'notebooks.create' => true,
        'notebooks.update' => true,
        'notebooks.delete' => true,
        'notebooks.pages' => true,
    ];

    /**
     * Check to see if a user has been granted a specific permission
     *
     * @param string $check
     * @return boolean
     */
    public function hasPermission($check)
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

    /**
     * Assign a set of permissions to this user based on their access level
     *
     * @return self
     */
    public function assignAccessLevelPermissions()
    {
        if ($this->access_level >= AccessLevel::$ORGANIZATION_ADMIN) {
            $this->applyPermissions(self::$defaultAdminPermissions);
        }

        return $this;
    }

    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token));
    }

    /**
     * Send the email verification notification.
     *
     * @return void
     */
    public function sendEmailVerificationNotification()
    {
        $this->email_verification_code = str_random(24);
        $this->save();

        $this->notify(new VerifyEmail);
    }

    /**
     * Mark the given user's email as verified.
     *
     * @return bool
     */
    public function markEmailAsVerified()
    {
        return $this->forceFill([
            'email_verified_at' => $this->freshTimestamp(),
            'email_verification_code' => null
        ])->save();
    }
}
