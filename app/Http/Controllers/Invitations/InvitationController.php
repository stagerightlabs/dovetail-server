<?php

namespace App\Http\Controllers\Invitations;

use App\Invitation;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Notifications\InvitationSent;
use App\Http\Requests\SendInvitation;
use App\Http\Resources\InvitationResource;
use Illuminate\Support\Facades\Notification;

/**
 * @resource Invitations
 *
 * Manage requests sent to users
 */
class InvitationController extends Controller
{
    /**
     * View all invitations
     *
     * @return \Illuminate\Http\Response
     */
    public function index(\Illuminate\Http\Request $request)
    {
        $this->authorize('view', Invitation::class);

        return InvitationResource::collection(
            $request->organization()->invitations
        );
    }

    /**
     * Send an invitation
     *
     * @return \Illuminate\Http\Response
     */
    public function store(SendInvitation $request)
    {
        // Create the invitation record
        $invitation = Invitation::create([
            'email' => $request->get('email'),
            'organization_id' => $request->organization()->id
        ]);

        // Deliver the invitation
        Notification::route('mail', $request->get('email'))
            ->notify(new InvitationSent($invitation));

        return new InvitationResource($invitation);
    }

    /**
     * Delete an invitation
     *
     * @param  string $hashid
     * @return \Illuminate\Http\Response
     */
    public function destroy($hashid)
    {
        $invitation = Invitation::findOrFail(hashid($hashid));

        $this->authorize('destroy', $invitation);

        $invitation->delete();

        return response()->json([], 204);
    }
}
