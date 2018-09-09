<?php

namespace App\Http\Controllers\Invitations;

use App\User;
use App\Invitation;
use App\AccessLevel;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;
use App\Http\Requests\InvitationRedemption;

class Redeem extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(InvitationRedemption $request, $code)
    {
        $invitation = Invitation::whereNull('revoked_at')
            ->whereNull('completed_at')
            ->findOrFail(hashid($code, 'invitation'));

        $user = User::create([
            'name' => $request->get('name'),
            'email' => $request->get('email'),
            'password' => Hash::make($request->get('password')),
            'access_level' => AccessLevel::$ORGANIZATION_MEMBER,
            'organization_id' => $invitation->organization->id
        ]);

        event(new Registered($user));

        return response()->authorization($request);
    }
}
