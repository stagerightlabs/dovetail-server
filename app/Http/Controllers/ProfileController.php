<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\UpdateProfile;
use App\Http\Resources\ProfileResource;

class ProfileController extends Controller
{
    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show(\Illuminate\Http\Request $request)
    {
        return new ProfileResource($request->user());
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  UpdateProfile  $request
     * @return JsonResponse
     */
    public function update(UpdateProfile $request)
    {
        // Fetch the current user
        $user = $request->user();

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

        return new ProfileResource($user);
    }
}
