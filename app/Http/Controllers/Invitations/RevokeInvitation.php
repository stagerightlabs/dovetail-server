<?php

namespace App\Http\Controllers\Invitations;

use App\Invitation;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Http\Controllers\Controller;
use App\Http\Resources\InvitationResource;

class RevokeInvitation extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  string $hashid
     * @return \Illuminate\Http\Response
     */
    public function __invoke($hashid)
    {
        $invitation = Invitation::findOrFail(hashid($hashid));

        $this->authorize('revoke', $invitation);

        $invitation->revoked_at = Carbon::now();
        $invitation->revoked_by = auth()->user()->id;
        $invitation->save();

        return new InvitationResource($invitation);
    }
}
