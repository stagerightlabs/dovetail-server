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
    public function index()
    {
        return NotificationResource::collection(auth()->user()->unreadNotifications);
    }

    /**
     * View a single notification
     *
     * @param  string $uuid
     * @return JsonResponse
     */
    public function show($uuid)
    {
        return new NotificationResource(
            auth()->user()->notifications()->findOrFail($uuid)
        );
    }

    /**
     * Mark a comment as read
     *
     * @param  string $uuid
     * @return JsonResponse
     */
    public function update($uuid)
    {
        $notification = auth()->user()->notifications()->findOrFail($uuid);

        $notification->markAsRead();

        return new NotificationResource($notification);
    }
}
