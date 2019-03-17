<?php

namespace App\Http\Controllers\Members;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\PermissionUpdate;
use Illuminate\Http\Resources\Json\JsonResource;

class PermissionsController extends Controller
{
    public function __construct()
    {
        return $this->middleware('org.admin');
    }

    /**
     * Fetch the permissions for an organization member
     *
     * @param  string $hashid
     * @return JsonResponse
     */
    public function show($hashid)
    {
        $user = User::inOrganization()->findOrFail(hashid($hashid));

        return $this->transform($user);
    }

    /**
     * Update an organization member's permissions
     *
     * @param  PermissionUpdate  $request
     * @param  string $hashid
     * @return JsonResponse
     */
    public function update(PermissionUpdate $request, $hashid)
    {
        $user = User::inOrganization()->findOrFail(hashid($hashid));

        $validPermissions = array_keys(User::$defaultPermissions);

        $permissions = collect($request->get('permissions', []))
            ->filter(function ($value, $key) use ($validPermissions) {
                return in_array($key, $validPermissions);
            });

        $user->applyPermissions($permissions);
        $user->save();

        return $this->transform($user);
    }


    /**
     * Transform a member's permissions into a JsonResponse
     *
     * @param User $user
     * @return JsonResponse
     */
    protected function transform(User $user)
    {
        return new JsonResource($user->permissions);
    }
}
