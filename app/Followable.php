<?php

namespace App;

use App\User;
use App\Follow;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * Allows a model to be followed by a user
 */
trait Followable
{
    /**
     * The users who follow this resource
     *
     * @return MorphMany
     */
    public function follows()
    {
        return $this->morphMany(Follow::class, 'followable');
    }

    /**
     * The users who are following this resource
     *
     * @return Collection
     */
    public function getFollowers()
    {
        return User::findMany(
            $this->follows()->whereNull('disabled_at')->pluck('user_id')
        );
    }

    /**
     * Determine if a given user is a follower
     *
     * @param User $user
     * @return boolean
     */
    public function hasFollower(User $user)
    {
        return $this->follows()->where('user_id', $user->id)->exists();
    }

    /**
     * Add a follower to this resource
     *
     * @param User|Collection $users
     * @return void
     */
    public function addFollower($users)
    {
        if ($users instanceof User) {
            $users = collect([$users]);
        }

        $users->each(function ($user) {
            // If this user is already a follower, reset the record
            if ($follow = $this->follows()->where('user_id', $user->id)->first()) {
                $follow->disabled_at = null;
                $follow->save();
                return;
            }

            // Otherwise create a new follow record.
            $this->follows()->create(['user_id' => $user->id]);
        });
    }

    /**
     * Disable future notifications for a follower
     *
     * @param User $user
     * @return void
     */
    public function disableFollower(User $user)
    {
        if (! $follow = $this->follows()->where('user_id', $user->id)->get()) {
            return;
        }

        $follow->disabled_at = Carbon::now();
        $follow->save();
    }

    /**
     * Remove a following from storage
     *
     * @param User $user
     * @return void
     */
    public function removeFollower(User $user)
    {
        if ($follow = $this->follows()->where('user_id', $user->id)->first()) {
            $follow->delete();
        }
    }
}
