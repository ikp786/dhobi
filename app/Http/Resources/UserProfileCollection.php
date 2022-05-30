<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
// use Illuminate\Http\Resources\Json\Resource;

class UserProfileCollection extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        // echo 'dfd';die;
        return [
            'user_id'       => $this->id,
            'name'          => $this->name,
            'email'         => $this->email,
            'mobile'        => $this->mobile,
            'address'       => $this->address,
            'dob'           => $this->dob,
            'profile_pic'   => !empty($this->profile_pic) ? asset('storage/app/public/user_images/' . $this->profile_pic) : asset('public/assets/default/default.png'),
        ];
    }
}
