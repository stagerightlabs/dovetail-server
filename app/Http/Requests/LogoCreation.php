<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LogoCreation extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'owner_type' => 'required|in:organization,user',
            'owner_hashid' => 'required',
            'logo' => 'required|image'
        ];
    }
}
