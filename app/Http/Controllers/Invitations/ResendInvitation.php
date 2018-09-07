<?php

namespace App\Http\Controllers\Invitations;

use App\Invitation;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Notifications\InvitationSent;
use App\Http\Resources\InvitationResource;
use Illuminate\Support\Facades\Notification;

class ResendInvitation extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  string $hashid
     * @return \Illuminate\Http\Response
     */
    public function __invoke($hashid)
    {
        $this->authorize('resend', Invitation::class);

        $invitation = Invitation::findOrFail(hashid($hashid));

        // Deliver the invitation
        Notification::route('mail', $invitation->email)
            ->notify(new InvitationSent($invitation));

        return new InvitationResource($invitation);
    }
}
