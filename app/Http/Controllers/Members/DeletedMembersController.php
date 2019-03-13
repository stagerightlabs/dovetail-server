<?php

namespace App\Http\Controllers\Members;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\MemberResource;

class DeletedMembersController extends Controller
{
    public function __construct()
    {
        $this->middleware('org.admin');
    }

    /**
     * Display a listing of this organizations deleted users
     *
     * @return JsonResponse
     */
    public function index(\Illuminate\Http\Request $request)
    {
        return MemberResource::collection(
            $request->organization()->users()->onlyTrashed()->get()
        );
    }

    /**
     * Delete an organization member
     *
     * @param  string $hashid
     * @return JsonResponse
     */
    public function store(\Illuminate\Http\Request $request, $hashid)
    {
        $this->authorize('edit', $request->organization());

        $user = User::inOrganization()->findOrFail(hashid($hashid));

        $user->delete();

        return response()->json([], 204);
    }

    /**
     * Restore an organization member
     *
     * @param  string $hashid
     * @return JsonResponse
     */
    public function destroy(\Illuminate\Http\Request $request, $hashid)
    {
        $this->authorize('edit', $request->organization());

        $user = User::inOrganization()
            ->withTrashed()
            ->findOrFail(hashid($hashid));

        $user->restore();

        return response()->json([], 204);
    }
}
