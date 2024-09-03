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
            'description' => $this->description,
            'phone_number_1' => $this->phone_number_1,
            'phone_number_2' => $this->phone_number_2,
            'mobile_number_1' => $this->mobile_number_1,
            'mobile_number_2' => $this->mobile_number_2,
            'fax_number' => $this->fax_number,
            'whatsapp_number' => $this->whatsapp_number,
            'facebook_link' => $this->facebook_link,
            'website' => $this->website,
            'user_id' => $this->user_id,
            'service_id' => $this->service_id,
            'section_id' => $this->section_id,
            'works' => $this->whenLoaded('works')
        ];
    }
}
