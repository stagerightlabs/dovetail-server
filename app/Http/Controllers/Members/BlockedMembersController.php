<?php

namespace App\Http\Controllers\Members;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Http\Controllers\Controller;

class BlockedMembersController extends Controller
{
    public function __construct()
    {
        $this->middleware('org.admin');
    }

    /**
     * Block a organization member
     *
     * @param  string $hashid
     * @return JsonResponse
     */
    public function store($hashid)
    {
        $this->authorize('edit', request()->organization());

        $user = User::inOrganization()->findOrFail(hashid($hashid));

        $user->blocked_at = Carbon::now();
        $user->save();

        return response()->json([], 204);
    }

    /**
     * Restore an organization member
     *
     * @param  string $hashid
     * @return JsonResponse
     */
    public function destroy($hashid)
    {
        $this->authorize('edit', request()->organization());

        $user = User::inOrganization()->findOrFail(hashid($hashid));

        $user->blocked_at = null;
        $user->save();

        return response()->json([], 204);
    }
}
