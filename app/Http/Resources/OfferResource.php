<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OfferResource extends JsonResource
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
            'offer_id'                  => $this->id,
            'minimum_order_value'       => $this->minimum_order_value,
            'quantity_type'             => $this->quantity_type,
            'description'               => $this->description,
            'product_name'              => isset($this->products->name) ? $this->products->name : '',
            'product_image'             => isset($this->products->images->image) ? asset('storage/app/public/product_images/' . $this->products->images->image)  : '',
        ];
    }
}
