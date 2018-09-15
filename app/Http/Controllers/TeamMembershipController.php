<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Auth\Access\AuthorizationException;

class TeamMembershipController extends Controller
{
    /**
     * Add a member to this team
     *
     * @param  string $team
     * @param  string $member
     * @return JsonResponse
     */
    public function store($team)
    {
        $this->requirePermission('teams.membership');

        request()->validate([
            'member' => 'required|string'
        ]);

        // Fetch the team and the user
        $team = request()->organization()->teams()->findOrFail(hashid($team));
        $member = request()->organization()->users()->findOrFail(hashid(request('member')));

        // Users cannot add themselves to teams
        if (auth()->user()->is($member)) {
            throw new AuthorizationException("You cannot add yourself to a team.");
        }

        $team->addMember($member);

        return response()->json([], 201);
    }

    /**
     * Add a member to this team
     *
     * @param  string $team
     * @param  string $member
     * @return JsonResponse
     */
    public function delete($team, $member)
    {
        $this->requirePermission('teams.membership');

        // Fetch the team and the member
        $team = request()->organization()->teams()->findOrFail(hashid($team));
        $member = request()->organization()->users()->findOrFail(hashid($member));

        // Users cannot remove themselves to teams
        if (auth()->user()->is($member)) {
            throw new AuthorizationException("You cannot add yourself to a team.");
        }

        $team->members()->detach($member);

        return response()->json([], 204);
    }
}
