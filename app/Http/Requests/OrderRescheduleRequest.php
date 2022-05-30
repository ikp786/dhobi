<?php

namespace App\Http\Requests;


use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class OrderRescheduleRequest extends FormRequest
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
            'order_id'              => 'required|exists:orders,id',
            'pickup_time_slot_id'   => 'required|exists:time_slots,id',
            'delivery_time_slot_id' => 'required|exists:time_slots,id',
            'pickup_date'           => 'required|date_format:d/m/Y|after_or_equal:today',
            'delivery_date'         => 'required|date_format:d/m/Y|after_or_equal:pickup_date'
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
