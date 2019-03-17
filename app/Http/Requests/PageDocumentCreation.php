<?php

namespace App\Http\Requests;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;

class PageDocumentCreation extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Gate::authorize('require-permission', 'notebooks.pages');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'attachment' => 'required|mimes:jpeg,png,gif,pdf,csv'
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
            'attachment.mimes' => "Only jpeg, png, gif, csv and pdf files are allowed."
        ];
    }
}
