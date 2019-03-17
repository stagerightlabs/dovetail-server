<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\TeamMembershipCreation;

class TeamMembershipController extends Controller
{
    /**
     * Add a member to this team
     *
     * @param  TeamMembershipCreation $request
     * @param  string $team
     * @param  string $member
     * @return JsonResponse
     */
    public function store(TeamMembershipCreation $request, $team)
    {
        // Fetch the team and the user
        $team = $request->organization()->teams()->findOrFail(hashid($team));
        $member = $request->organization()->users()->findOrFail(hashid($request->get('member')));

        $team->addMember($member);

        return response()->json([], 201);
    }

    /**
     * Add a member to this team
     *
     * @param  Request $request
     * @param  string $team
     * @param  string $member
     * @return JsonResponse
     */
    public function destroy(Request $request, $team, $member)
    {
        $this->requirePermission('teams.membership');

        // Fetch the team and the member
        $team = $request->organization()->teams()->findOrFail(hashid($team));
        $member = $request->organization()->users()->findOrFail(hashid($member));

        $team->removeMember($member);

        return response()->json([], 204);
    }
}
