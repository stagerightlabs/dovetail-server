<?php

namespace App\Http\Controllers\Organization;

use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\OrganizationResource;

class OrganizationController extends Controller
{
    /**
     * Fetch details about the current user's organization
     *
     * @return JsonResponse
     */
    public function show(\Illuminate\Http\Request $request)
    {
        return new OrganizationResource($request->organization());
    }
}
