<?php

namespace App\Http\Controllers\Organization;

use App\Organization;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\SettingUpdate;
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
     * @param  Request $request
     * @param  string $key
     * @return JsonResponse
     */
    public function show(Request $request, $key)
    {
        $this->authorize('readSetting', $request->organization());

        return new JsonResource([
            'key' => $key,
            'value' => $request->organization()->config($key)
        ]);
    }

    /**
     * Update an organization setting
     *
     * @param  SettingUpdate $request
     * @param  string $key
     * @return JsonResponse
     */
    public function update(SettingUpdate $request)
    {
        $organization = $request->organization();
        $organization->updateConfiguration(request('key'), request('value'));
        $organization->save();

        return response()->json([], 204);
    }
}
