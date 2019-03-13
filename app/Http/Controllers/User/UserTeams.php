<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\TeamResource;
use Illuminate\Http\Resources\Json\JsonResource;

class UserTeams extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param string $permission
     *
     * @return JsonResponse
     */
    public function __invoke(Request $request)
    {
        return TeamResource::collection($request->user()->teams);
    }
}
