<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TimeSlotResource extends JsonResource
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

            'id'          => $this->id,
            'time_slot'   => date('h:i A', strtotime($this->from)).' to '.date('h:i A', strtotime($this->to)),
        ];
    }
}
