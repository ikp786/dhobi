<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class AddressCollection extends JsonResource
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

            'address_id'  => $this->id,
            'type'        => $this->type,
            'name'        => $this->name,
            'location'    => $this->location,
            'latitude'    => $this->latitude,
            'longitude'   => $this->longitude,
            'mobile'      => $this->mobile,
            'email'       => $this->email,
            'address'     => $this->address,
            'pincode'     => $this->pincode,
        ];
    }
}
