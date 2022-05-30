<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDeliveryBoyRequest extends FormRequest
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
            'name'           => 'required',
            'mobile'         => 'required|unique:users,mobile,'.$this->id.'|numeric|digits_between:10,13',
            'email'          => 'required|email|unique:users,email,'.$this->id,
            // 'password'       => 'required',            
        ];
    }
}
