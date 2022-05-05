<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DiscountResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
           'id' => $this->id,
           'code' => $this->code,
           'amount' => $this->amount,
           'count' => $this->count,
           'start_time' => $this->start_time,
           'expiration_time' => $this->expiration_time,
        ];
    }
}
