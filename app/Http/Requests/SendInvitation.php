<?php

namespace App\Http\Requests;

use App\User;
use App\Invitation;
use Illuminate\Foundation\Http\FormRequest;
use App\Rules\ValidEmail;

class SendInvitation extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->can('send', Invitation::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'email' => ['required', new ValidEmail, 'unique:invitations']
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
            'unique' => 'An invitation cannot be sent to that email address at this time.'
        ];
    }

    /**
     * Configure the validator instance.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if (User::where('email', $this->get('email'))->exists()) {
                $validator->errors()->add('email', 'An invitation cannot be sent to that email address at this time.');
            }
        });
    }
}
