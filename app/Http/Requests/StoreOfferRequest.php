<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOfferRequest extends FormRequest
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
            'product_id'               => 'required|integer|exists:products,id',
            'minimum_order_value'      => 'required|numeric|digits_between:1,8',
            'quantity_type'            => 'required|In:pav,half_kg,kg,',
            'description'              => 'required',
            'status'                   => 'required|In:0,1'
        ];
    }
}
