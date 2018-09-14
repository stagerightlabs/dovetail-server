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
        // Fetch the invitation
        $invitation = Invitation::whereNull('revoked_at')
            ->whereNull('completed_at')
            ->findOrFail(hashid($code, 'invitation'));

        // Create the new user
        $user = User::create([
            'name' => $request->get('name'),
            'email' => $invitation->email,
            'password' => Hash::make($request->get('password')),
            'access_level' => AccessLevel::$ORGANIZATION_MEMBER,
            'organization_id' => $invitation->organization->id
        ]);

        // Send a welcome message
        event(new Registered($user));

        // Mark the invitation as complete
        $invitation->complete();

        // Return a new auth token
        return response()->authorization(
            $request->merge(['email' => $invitation->email])
        );
    }
}
