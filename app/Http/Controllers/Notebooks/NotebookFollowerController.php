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
    public function store($hashid)
    {
        $notebook = request()->organization()->notebooks()->findOrFail(hashid($hashid));

        $notebook->addFollower(auth()->user());

        return new NotebookResource($notebook);
    }

    /**
     * Remove a notebook follower
     *
     * @param  string $hashid
     * @return JsonResponse
     */
    public function destroy($hashid)
    {
        $notebook = request()->organization()->notebooks()->findOrFail(hashid($hashid));

        $notebook->removeFollower(auth()->user());

        return new NotebookResource($notebook);
    }
}
