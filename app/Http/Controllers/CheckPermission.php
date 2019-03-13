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
     * @param string $permission
     *
     * @return JsonResponse
     */
    public function __invoke(Request $request, $permission)
    {
        return new JsonResource([
            'key' => $permission,
            'allowed' => $request->user()->hasPermission($permission)
        ]);
    }
}
