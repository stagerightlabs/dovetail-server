<?php

namespace App\Http\Controllers;

use App\Logo;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\LogoCreation;
use App\Http\Controllers\Controller;
use App\Http\Resources\LogoResource;

class LogoController extends Controller
{
    /**
     * Fetch a list of available logos
     *
     * @param  Request $request
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        return LogoResource::collection($request->organization()->logos);
    }

    /**
     * Store a new logo
     *
     * @return JsonResponse
     */
    public function store(LogoCreation $request)
    {
        // Storage Path
        $path = $request->organization()->slug . '/logos';

        // Create Logo
        $logo = Logo::create([
            'owner_id' => hashid($request->get('owner_hashid')),
            'owner_type' => $request->get('owner_type'),
            'original' => $request->get('logo')->storePublicly($path, 's3'),
            'filename' => $request->get('logo')->getClientOriginalName()
        ]);

        return new LogoResource($logo);
    }

    /**
     * Remove a logo from storage.
     *
     * @param string $hashid
     * @return JsonResponse
     */
    public function destroy($hashid)
    {
        $logo = Logo::findOrFail(hashid($hashid));

        $logo->delete();

        return response()->json([], 204);
    }
}
