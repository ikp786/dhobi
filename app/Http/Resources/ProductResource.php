<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
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
            'id'                => $this->id,
            'name'              => $this->name,
            'price'             => $this->price,
            'category_id'       => $this->category_id,
            'category_name'     => @$this->categories->name,
            'images'            => asset('storage/app/public/product_images/' . $this->image),
            'add_on_services'   => AddOnServiceResource::collection($this->addOnServiceMappingInProduct)
        ];
    }
}
