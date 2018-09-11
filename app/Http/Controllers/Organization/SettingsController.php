<?php

namespace App\Http\Controllers\Organization;

use App\Organization;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Http\Resources\Json\JsonResource;

class SettingsController extends Controller
{
    public function __construct()
    {
        $this->middleware('org.admin')->except('show');
    }

    /**
     * Retrieve a single config value for this organization
     *
     * @param string $key
     * @return JsonResponse
     */
    public function show($key)
    {
        $this->authorize('readSetting', request()->organization());

        return new JsonResource([
            'key' => $key,
            'value' => request()->organization()->config($key)
        ]);
    }

    /**
     * Retrieve a single config value for this organization
     *
     * @param Request $request
     * @param string $key
     * @return JsonResponse
     */
    public function update(Request $request)
    {
        $this->authorize('writeSetting', request()->organization());

        $request->validate([
            'settings' => 'required'
        ]);

        $validKeys = array_keys(Organization::$defaultConfiguration);

        $config = collect($request->get('settings', []))
            ->filter(function ($value, $key) use ($validKeys) {
                return in_array($key, $validKeys);
            });

        $organization = $request->organization();
        $organization->updateConfiguration($config);
        $organization->save();

        return response()->json([], 204);
    }
}
