<?php

namespace App;

use App\User;
use App\Model;
use App\Events\TeamDeletion;
use App\Events\TeamMemberAdded;
use App\Events\TeamMemberRemoved;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Team extends Model
{
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * The event map for the model.
     *
     * @var array
     */
    protected $dispatchesEvents = [
        'deleting' => TeamDeletion::class
    ];

    /**
     * The members that belong to this team
     *
     * @return BelongsToMany
     */
    public function members()
    {
        return $this->belongsToMany(User::class);
    }

    /**
     * Scope a query to only include popular users.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scope($query)
    {
        $subQuery = \DB::table('interactions')
            ->select('created_at')
            ->whereRaw('customer_id = customers.id')
            ->latest()
            ->limit(1);

        return $query->select('customers.*')->selectSub($subQuery, 'last_interaction_date');
    }

    /**
     * Add a member to this team
     *
     * @param  User|integer $member
     * @return self
     */
    public function addMember($member)
    {
        $this->members()->attach($member);

        TeamMemberAdded::dispatch($this, $member);

        return $this;
    }

    /**
     * Remove a member from this team
     *
     * @param  User|integer $member
     * @return self
     */
    public function removeMember($member)
    {
        $this->members()->detach($member);

        TeamMemberRemoved::dispatch($this, $member);

        return $this;
    }

    /**
     * Is this user a member of this team?
     *
     * @param  User|integer $member
     * @return boolean
     */
    public function hasMember($member)
    {
        $key = $member instanceof User ? $member->id : $member;

        return $this->members()->pluck('user_id')->contains($key);
    }

    /**
     * Is this user not a member of this team?
     *
     * @param  User|integer $member
     * @return boolean
     */
    public function doesNotHaveMember($member)
    {
        return ! $this->hasMember($member);
    }

    /**
     * The notebooks that belong to this team
     *
     * @return HasMany
     */
    public function notebooks()
    {
        return $this->hasMany(Notebook::class)->whereNull('user_id');
    }
}
