<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DriverProfile extends JsonResource
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
            'user_id'       => $this->id,
            'unique_id'     => $this->unique_id,
            'online_status' => $this->online_status,
            'name'          => $this->name,
            'email'         => $this->email,
            'mobile'        => $this->mobile,
            'dob'           => $this->dob,
            'address'       => $this->address,
            'profile_pic'   => !empty($this->profile_pic) ? asset('storage/app/public/user_images/' . $this->profile_pic) : asset('storage/app/public/default/default.jpg'),
        ];
    }
}
