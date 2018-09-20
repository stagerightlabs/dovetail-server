<?php

namespace App\Http\Controllers;

use App\Logo;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\LogoResource;
use App\Http\Controllers\Controller;
use App\NullFile;
use App\Events\LogoAdded;

class LogoController extends Controller
{
    /**
     * Fetch a list of available logos
     *
     * @return JsonResponse
     */
    public function index()
    {
        return LogoResource::collection(request()->organization()->logos);
    }

    /**
     * Store a new logo
     *
     * @return JsonResponse
     */
    public function store()
    {
        request()->validate([
            'owner_type' => 'required|in:organization,user',
            'owner_hashid' => 'required',
            'logo' => 'required|image'
        ]);

        // Storage Path
        $path = request()->organization()->slug . '/logos';

        // Create Logo
        $logo = Logo::create([
            'owner_id' => hashid(request('owner_hashid')),
            'owner_type' => request('owner_type'),
            'original' => request('logo')->storePublicly($path, 's3'),
            'filename' => request('logo')->getClientOriginalName()
        ]);

        return new LogoResource($logo);
    }

    /**
     * Remove a logo from storage.
     *
     * @param string $hashid
     * @return JsonResponse
     */
    public function delete($hashid)
    {
        $logo = Logo::findOrFail(hashid($hashid));

        $logo->delete();

        return response()->json([], 204);
    }
}