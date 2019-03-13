<?php

namespace App\Http\Controllers;

use App\Team;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\TeamResource;

class TeamController extends Controller
{
    /**
     * Retrieve a listing of the teams.
     *
     * @return JsonResponse
     */
    public function index(\Illuminate\Http\Request $request)
    {
        $teams = $request->organization()->teams()->withCount('members')->get();

        return TeamResource::collection($teams);
    }

    /**
     * Store a newly created team in storage.
     *
     * @return JsonResponse
     */
    public function store(\Illuminate\Http\Request $request)
    {
        $this->requirePermission('teams.create');

        $request->validate([
            'name' => 'required|iunique:teams,name,null,null,organization_id,' . $request->organization()->id,
        ], [
            'name.iunique' => 'That name is already in use'
        ]);

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
     * @param  string $hashid
     * @return JsonResponse
     */
    public function show(\Illuminate\Http\Request $request, $hashid)
    {
        return new TeamResource(
            $request->organization->teams()->with('members')->findOrFail(hashid($hashid))
        );
    }

    /**
     * Update the specified team in storage.
     *
     * @param  string $hashid
     * @return JsonResponse
     */
    public function update(\Illuminate\Http\Request $request, $hashid)
    {
        $this->requirePermission('teams.update');

        $team = $request->organization->teams()->with('members')->findOrFail(hashid($hashid));

        $request->validate([
            'name' => "required|iunique:teams,name,{$team->id},id,organization_id," . $request->organization()->id,
        ]);

        $team->name = request('name');
        $team->save();

        return new TeamResource($team);
    }

    /**
     * Remove the specified team from storage.
     *
     * @param  string $hashid
     * @return JsonResponse
     */
    public function delete(\Illuminate\Http\Request $request, $hashid)
    {
        $this->requirePermission('teams.delete');

        $team = $request->organization->teams()->findOrFail(hashid($hashid));

        $team->delete();

        return response()->json([], 204);
    }
}
