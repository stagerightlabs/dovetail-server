<?php

namespace App\Http\Controllers\Notebooks;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\NotebookResource;

class NotebookFollowerController extends Controller
{
    /**
     * Add a notebook follower
     *
     * @param  string $hashid
     * @return JsonResponse
     */
    public function store(\Illuminate\Http\Request $request, $hashid)
    {
        $notebook = $request->organization()->notebooks()->findOrFail(hashid($hashid));

        $notebook->addFollower($request->user());

        return new NotebookResource($notebook);
    }

    /**
     * Remove a notebook follower
     *
     * @param  string $hashid
     * @return JsonResponse
     */
    public function destroy(\Illuminate\Http\Request $request, $hashid)
    {
        $notebook = $request->organization()->notebooks()->findOrFail(hashid($hashid));

        $notebook->removeFollower($request->user());

        return new NotebookResource($notebook);
    }
}
