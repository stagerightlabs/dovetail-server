<?php

namespace App\Policies;

use App\User;
use App\AccessLevel;
use Illuminate\Auth\Access\HandlesAuthorization;
use App\Organization;

class OrganizationPolicy
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
     * Is this user allowed to edit their organization?
     *
     * @param User $user
     * @return boolean
     */
    public function edit(User $user, Organization $organization)
    {
        return $user->access_level >= AccessLevel::$ORGANIZATION_ADMIN
            && $user->organization_id == $organization->id;
    }

    /**
     * Is this user allowed to read organization settings?
     *
     * @param User $user
     * @param Organization $organization
     * @return boolean
     */
    public function readSetting(User $user, Organization $organization)
    {
        return $user->organization_id == $organization->id;
    }

    /**
     * Is this user allowed to read organization settings?
     *
     * @param User $user
     * @param Organization $organization
     * @return boolean
     */
    public function writeSetting(User $user, Organization $organization)
    {
        return $user->access_level >= AccessLevel::$ORGANIZATION_ADMIN
            && $user->organization_id == $organization->id;
    }
}
