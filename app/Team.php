<?php

namespace App;

use App\User;
use App\Model;
use App\Events\TeamDeletion;
use App\Events\TeamMemberAdded;
use App\Events\TeamMemberRemoved;
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
}
