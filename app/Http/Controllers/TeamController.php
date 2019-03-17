<?php

namespace App\Http\Controllers;

use App\Team;
use Illuminate\Http\Request;
use App\Http\Requests\TeamUpdate;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\TeamCreation;
use App\Http\Resources\TeamResource;

class TeamController extends Controller
{
    /**
     * Retrieve a listing of the teams.
     *
     * @param  Request $request
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        $teams = $request->organization()->teams()->withCount('members')->get();

        return TeamResource::collection($teams);
    }

    /**
     * Store a newly created team in storage.
     *
     * @param  TeamCreation $request
     * @return JsonResponse
     */
    public function store(TeamCreation $request)
    {
        $team = Team::create([
            'name' => $request->name,
            'organization_id' => $request->organization()->id,
            'created_by' => $request->user()->id,
        ]);

        return new TeamResource($team);
    }

    /**
     * Retrieve the specified team.
     *
     * @param  Request $request
     * @param  string $hashid
     * @return JsonResponse
     */
    public function show(Request $request, $hashid)
    {
        return new TeamResource(
            $request->organization()->teams()->with('members')->findOrFail(hashid($hashid))
        );
    }

    /**
     * Update the specified team in storage.
     *
     * @param  TeamUpdate $request
     * @param  string $hashid
     * @return JsonResponse
     */
    public function update(TeamUpdate $request, $hashid)
    {
        $team = $request->team;
        $team->name = $request->get('name');
        $team->save();

        return new TeamResource($team);
    }

    /**
     * Remove the specified team from storage.
     *
     * @param  Request $request
     * @param  string $hashid
     * @return JsonResponse
     */
    public function destroy(Request $request, $hashid)
    {
        $this->requirePermission('teams.destroy');

        $team = $request->organization()->teams()->findOrFail(hashid($hashid));

        $team->delete();

        return response()->json([], 204);
    }
}
