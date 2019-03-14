<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\Resources\Json\JsonResource;

class PermissionCheckController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  Request  $request
     * @param string $permission
     *
     * @return JsonResponse
     */
    public function show(Request $request, $permission)
    {
        return new JsonResource([
            'key' => $permission,
            'allowed' => $request->user()->hasPermission($permission)
        ]);
    }
}
