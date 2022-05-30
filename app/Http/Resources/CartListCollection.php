<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class CartListCollection extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {

        return [
            'cart_id'                    => $this->id,
            'product_id'                 => $this->product_id,
            'product_name'               => $this->product_name,
            'product_quantity_phav'      => $this->product_quantity_phav,
            'product_quantity_half_kg'   => $this->product_quantity_half_kg,
            'product_quantity_kg'        => $this->product_quantity_kg,
            'product_total_quantity'     => $this->product_total_quantity,
            'product_phav_amount'        => $this->product_phav_amount,
            'product_half_kg_amount'     => $this->product_half_kg_amount,
            'product_kg_amount'          => $this->product_kg_amount,
            'total_amount'               => $this->total_amount
        ];
    }
}
