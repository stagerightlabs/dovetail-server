<?php

namespace App\Http\Controllers;

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
    public function index()
    {
        return TeamResource::collection(request()->organization()->teams);
    }

    /**
     * Store a newly created team in storage.
     *
     * @return JsonResponse
     */
    public function store()
    {
        $this->requirePermission('teams.create');

        request()->validate([
            'name' => 'required|iunique:teams,name,null,null,organization_id,' . request()->organization()->id,
        ]);

        $team = Team::create([
            'name' => request()->name,
            'organization_id' => request()->organization()->id,
            'created_by' => auth()->user()->id,
        ]);

        return new TeamResource($team);
    }

    /**
     * Retrieve the specified team.
     *
     * @param  string $hashid
     * @return JsonResponse
     */
    public function show($hashid)
    {
        return new TeamResource(
            request()->organization->teams()->findOrFail(hashid($hashid))
        );
    }

    /**
     * Update the specified team in storage.
     *
     * @param  string $hashid
     * @return JsonResponse
     */
    public function update($hashid)
    {
        $this->requirePermission('teams.update');

        $team = request()->organization->teams()->findOrFail(hashid($hashid));

        request()->validate([
            'name' => "required|iunique:teams,name,{$team->id},id,organization_id," . request()->organization()->id,
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
    public function delete($hashid)
    {
        $this->requirePermission('teams.delete');

        $team = request()->organization->teams()->findOrFail(hashid($hashid));

        $team->delete();

        return response()->json([], 204);
    }
}
