<?php

namespace App\Http\Controllers\Invitations;

use App\Invitation;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\InvitationResource;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @resource Invitations
 */
class Confirm extends Controller
{
    /**
     * Confirm that an invitation is redeemable
     *
     * @param  \Illuminate\Http\Request  $request
     * @return JsonResource
     */
    public function __invoke(Request $request, $code)
    {
        $invitation = Invitation::whereNull('revoked_at')
            ->whereNull('completed_at')
            ->findOrFail(hashid($code, 'invitation'));

        return new JsonResource([
            'email' => $invitation->email,
            'code' => $invitation->code,
            'invited-by' => $invitation->organization->name,
        ]);
    }
}
