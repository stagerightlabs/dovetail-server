<?php

namespace App\Http\Requests;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;

class TeamUpdate extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Gate::authorize('require-permission', 'teams.update');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $team = $this->organization()
            ->teams()
            ->with('members')
            ->findOrFail(hashid($this->route('hashid')));

        $this->offsetSet('team', $team);

        return [
            'name' => "required|iunique:teams,name,{$team->id},id,organization_id," . $this->organization()->id,
        ];
    }
}
