<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserOrderList extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id'                     => $this->id,
            'order_id'               => $this->order_number,
            'sub_total_amount'       => $this->total_product_amount,
            'add_on_service_amount'  => $this->add_on_service_amount,
            'deliver_charge'         => $this->deliver_charge,
            'total_amount'           => $this->order_amount,
            'order_date'             => date('d/m/Y', strtotime($this->created_at)),
            'delivery_date'          => date('d/m/Y', strtotime($this->delivery_date)),
            'pickup_date'            => date('d/m/Y', strtotime($this->pickup_date)),
            'pickup_time_slot_id'    => $this->pickup_time_slot_id,
            'delivery_time_slot_id'  => $this->delivery_time_slot_id,
            'delivery_time'          => $this->delivery_time,
            'pickup_time'            => $this->pickup_time,
            'order_delivery_status'  => $this->order_delivery_status,
            'payment_method'         => $this->payment_method,
            'address'                => new AddressCollection($this->addresses),
        ];
    }
}
