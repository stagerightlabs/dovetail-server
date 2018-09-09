<?php

namespace App\Http\Controllers\Members;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\UpdateMember;
use App\Http\Controllers\Controller;
use App\Http\Resources\MemberResource;

class MembersController extends Controller
{
    public function __construct()
    {
        $this->middleware('org.admin')->except('index');
    }


    /**
     * Display a listing of organization members
     *
     * @return JsonResponse
     */
    public function index()
    {
        return MemberResource::collection(request()->organization->users);
    }

    /**
     * Update an organization member
     *
     * @param  UpdateMember  $request
     * @param  string $hashid
     * @return JsonResponse
     */
    public function update(UpdateMember $request, $hashid)
    {
        // Fetch the current user
        $user = User::where('organization_id', request()->organization->id)
            ->findOrFail(hashid($hashid));

        // Has there been a change to the user's phone or email?
        $emailChange = $user->email != $request->get('email');
        $phoneChange = $user->phone != $request->get('phone');

        // Update the User
        $user->email = $request->get('email');
        $user->email_verified_at = $emailChange ? null : $user->email_verified_at;
        $user->name = $request->get('name');
        $user->phone = $request->get('phone');
        $user->phone_verified_at = $phoneChange ? null : $user->phone_verified_at;
        $user->save();

        return new MemberResource($user);
    }
}
