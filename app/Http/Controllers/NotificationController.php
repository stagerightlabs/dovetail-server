<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\NotificationResource;

class NotificationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(\Illuminate\Http\Request $request)
    {
        return NotificationResource::collection($request->user()->unreadNotifications);
    }

    /**
     * View a single notification
     *
     * @param  string $uuid
     * @return JsonResponse
     */
    public function show(\Illuminate\Http\Request $request, $uuid)
    {
        return new NotificationResource(
            $request->user()->notifications()->findOrFail($uuid)
        );
    }

    /**
     * Mark a comment as read
     *
     * @param  string $uuid
     * @return JsonResponse
     */
    public function update(\Illuminate\Http\Request $request, $uuid)
    {
        $notification = $request->user()->notifications()->findOrFail($uuid);

        $notification->markAsRead();

        return new NotificationResource($notification);
    }
}
