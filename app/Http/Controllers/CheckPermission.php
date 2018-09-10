<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CheckPermission extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return JsonResponse
     */
    public function __invoke(Request $request, $permission)
    {
        return new JsonResource([
            'allowed' => auth()->user()->isAllowedTo($permission)
        ]);
    }
}
