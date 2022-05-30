<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\Rule;

class AddToCartRequest extends FormRequest
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
            // 'user_id'                   => 'exists:users,id',
            'product_id'                => 'required|exists:products,id',
            'category_id'               => 'required|exists:categories,id',
            'product_quantity'          => 'required|integer',
            'device_token'              => 'required'
        ];
    }

    public function messags()
    {
        return [
            'product_id.required'      => 'Product Id should be required',
            'product_id.exists'        => 'Product Id not found',
            'product_id.exists'        => 'Product Id should be integer',
            'category_id.required'     => 'Category Id should be required',
            'category_id.exists'       => 'Category Id not found',
            'category_id.exists'       => 'Category Id should be integer'
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'ResponseCode'      => 200,
            'Status'            => False,
            'Message'           => $validator->errors()->first()
        ]));
    }
}
