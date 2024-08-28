<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EmployeeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'desc' => $this->desc,
            'location' => $this->location,
            'imageSSN' => $this->imageSSN,
            'livePhoto' => $this->livePhoto,
            'nationalId' => $this->nationalId,
            'min_price' => $this->min_price,
            'user_id' => $this->user_id,
            'service_id' => $this->service_id,
            'works' => $this->whenLoaded('works')
        ];
    }
}
