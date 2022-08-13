<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
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
            'name'              => 'required',
            'price'             => 'required|numeric',
            'category_id'       => 'required|exists:categories,id',
            'sub_category_id'   => 'required|exists:sub_categories,id',
            // 'add_on_service'    => 'required|array',
            'image'             => 'mimes:jpg,jpeg,png',
            'status'            => 'required|In:0,1'
        ];
    }
}
