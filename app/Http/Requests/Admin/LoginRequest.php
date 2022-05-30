<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
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
            'email'         => 'required|email',
            'password'      => 'required',
        ];
    }

    public function messages()
    {
        return [
            'email.required'     => 'Email should be required',
            'email.email'        => 'Email should be a valid format',
            'email.exists'       => 'Email does dont exits',
            'password.required'  => 'Paasword should be valid format'
        ];
    }
}