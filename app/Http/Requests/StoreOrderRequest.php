<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;


class StoreOrderRequest extends FormRequest
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
        // echo date('Y-m-d');die;
        return [
            'payment_method'        => 'required|In:Cod,Online',
            'address_id'            => 'required|exists:addresses,id',
            'pickup_time_slot_id'   => 'required|exists:time_slots,id',
            'delivery_time_slot_id' => 'required|exists:time_slots,id',
            'pickup_date'           => 'required|date_format:d/m/Y|after_or_equal:today',
            'delivery_date'         => 'required|date_format:d/m/Y|after_or_equal:pickup_date'
        ];
    }


    public function messags()
    {
        return [
            'payment_method.required'        => 'Payment method should be required',
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
