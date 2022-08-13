<?php

namespace App\Http\Resources;

use App\Models\Address;
use App\Models\OrderProduct;
use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;

class DriverOrderList extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $address = Address::find($this->address_id);
        $user = User::find($this->user_id);
        $product_name = OrderProduct::where('order_id',$this->id)->pluck('product_name')->join(',');
        $data =  [
            'id'                     => $this->id,
            'order_id'               => $this->order_number,
            'name'                   => isset($user->name) ? $user->name : '',
            'mobile'                 => isset($user->mobile) ? $user->mobile : '',
            'product_name'           => $product_name,
            'order_date'             => date('d/m/Y',strtotime($this->created_at)),
            'pickup_date'            => date('d/m/Y',strtotime($this->pickup_date)),
            'delivery_date'          => date('d/m/Y',strtotime($this->delivery_date)),
            'pickup_time'            => $this->pickup_time,
            'delivery_time'          => $this->delivery_time,
            'order_delivery_status'  => $this->order_delivery_status,
            'payment_method'         => $this->payment_method,
            'total_amount'           => $this->order_amount,
            'remark'                 => $this->remark ?? ''
        ];

        if($this->order_delivery_status == 'Pending'){
            $data['address'] = isset($address) ? new AddressCollection($address) : '';
        }else{
            $data['address'] = isset($address) ? new AddressCollection($address) : '';
        }
        return $data;
    }
}
