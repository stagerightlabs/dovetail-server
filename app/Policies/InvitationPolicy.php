<?php

namespace App\Policies;

use App\User;
use App\Invitation;
use App\AccessLevel;
use Illuminate\Auth\Access\HandlesAuthorization;

class InvitationPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Whitelist all the policies in this class for users who qualify
     *
     * @param User $user
     * @param string $ability
     * @return void
     */
    public function before($user, $ability)
    {
        if ($user->isSuperAdmin()) {
            return true;
        }
    }

    /**
     * Is this user allowed to send invitations?
     *
     * @param User $user
     * @return boolean
     */
    public function send(User $user)
    {
        return $user->access_level >= AccessLevel::$ORGANIZATION_ADMIN;
    }

    /**
     * Is this user allowed to resend invitations?
     *
     * @param User $user
     * @return boolean
     */
    public function resend(User $user)
    {
        return $user->access_level >= AccessLevel::$ORGANIZATION_ADMIN;
    }

    /**
     * Is this user allowed to revoke invitations?
     *
     * @param User $user
     * @return boolean
     */
    public function revoke(User $user, Invitation $invitation)
    {
        return $user->access_level >= AccessLevel::$ORGANIZATION_ADMIN;
    }

    /**
     * Is this user allowed to destroy invitations?
     *
     * @param User $user
     * @return boolean
     */
    public function destroy(User $user, Invitation $invitation)
    {
        return $user->access_level >= AccessLevel::$ORGANIZATION_ADMIN;
    }
}
