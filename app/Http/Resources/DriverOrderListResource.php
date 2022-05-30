<?php

namespace App\Http\Resources;

use App\Models\Address;
use App\Models\OrderProduct;
use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;

class DriverOrderListResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $user = User::find($this->user_id);

        return [
            'id'                     => $this->id,
            'username'               => $user->name,
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

        $address = Address::find($this->address_id);
        $product_name = OrderProduct::where('order_id', $this->id)->pluck('product_name')->join(',');
        $data =  [
            'id'                     => $this->id,
            'order_id'               => $this->order_number,
            'name'                   => isset($address->name) ? $address->name : '',
            'product_name'           => $product_name,
            'order_date'             => date('d/m/Y', strtotime($this->created_at)),
            'pickup_date'            => date('d/m/Y', strtotime($this->pickup_date)),
            'delivery_date'          => date('d/m/Y', strtotime($this->delivery_date)),
            'pickup_time'            => $this->pickup_time,
            'delivery_time'          => $this->delivery_time,
            'order_delivery_status'  => $this->order_delivery_status,
            'payment_method'         => $this->payment_method,
            'total_amount'           => $this->order_amount
        ];
    }
}
