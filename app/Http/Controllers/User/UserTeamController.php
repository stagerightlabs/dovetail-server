<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\TeamResource;
use Illuminate\Http\Resources\Json\JsonResource;

class UserTeamController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param Request  $request
     * @param string $permission
     *
     * @return JsonResponse
     */
    public function show(Request $request)
    {
        return TeamResource::collection($request->user()->teams);
    }
}
