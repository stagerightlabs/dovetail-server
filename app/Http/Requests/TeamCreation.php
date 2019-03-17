<?php

namespace App\Http\Requests;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;

class TeamCreation extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Gate::authorize('require-permission', 'teams.create');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|iunique:teams,name,null,null,organization_id,' . $this->organization()->id
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'name.iunique' => 'That name is already in use'
        ];
    }
}
