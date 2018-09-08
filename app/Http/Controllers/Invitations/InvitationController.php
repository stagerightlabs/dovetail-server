<?php

namespace App\Http\Controllers\Invitations;

use App\Invitation;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Notifications\InvitationSent;
use App\Http\Resources\InvitationResource;
use Illuminate\Support\Facades\Notification;
use App\Http\Requests\SendInvitation;

class InvitationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return InvitationResource::collection(
            request()->organization->invitations
        );
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(SendInvitation $request)
    {
        // Create the invitation record
        $invitation = Invitation::create([
            'email' => $request->get('email'),
            'organization_id' => request()->organization->id
        ]);

        // Deliver the invitation
        Notification::route('mail', $request->get('email'))
            ->notify(new InvitationSent($invitation));

        return new InvitationResource($invitation);
    }

    /**
     * Remove the specified resource from storage.
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
