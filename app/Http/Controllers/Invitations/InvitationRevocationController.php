<?php

namespace App\Http\Controllers\Invitations;

use App\Invitation;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Http\Controllers\Controller;
use App\Http\Resources\InvitationResource;

class InvitationRevocationController extends Controller
{
    /**
     * Revoke an invitation
     *
     * @param string $hashid
     * @return \Illuminate\Http\Response
     */
    public function update($hashid)
    {
        $invitation = Invitation::findOrFail(hashid($hashid));

        $this->authorize('revoke', $invitation);

        $invitation->revoked_at = Carbon::now();
        $invitation->revoked_by = auth()->user()->id;
        $invitation->save();

        return new InvitationResource($invitation);
    }

    /**
     * Restore an invitation
     *
     * @return \Illuminate\Http\Response
     */
    public function delete($hashid)
    {
        $invitation = Invitation::findOrFail(hashid($hashid));

        $this->authorize('revoke', $invitation);

        $invitation->revoked_at = null;
        $invitation->revoked_by = null;
        $invitation->save();

        return new InvitationResource($invitation);
    }
}
