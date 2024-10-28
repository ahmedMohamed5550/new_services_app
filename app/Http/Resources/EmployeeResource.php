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
            'company_image'=>$this->company_image,
            'company_name'=>$this->company_name,
            'imageSSN' => $this->imageSSN,
            'livePhoto' => $this->livePhoto,
            'nationalId' => $this->nationalId,
            'description' => $this->description,
            'phone_number_2' => $this->phone_number_2,
            'mobile_number_1' => $this->mobile_number_1,
            'mobile_number_2' => $this->mobile_number_2,
            'fax_number' => $this->fax_number,
            'whatsapp_number' => $this->whatsapp_number,
            'facebook_link' => $this->facebook_link,
            'instagram_link'=>$this->instagram_link,
            'linked_in_link'=>$this->linked_in_link,
            'tiktok_link'=>$this->tiktok_link,
            'website' => $this->website,
            'user' => $this->whenLoaded('user'),
            'service' => $this->whenLoaded('service'),
            'section' => $this->whenLoaded('section'),
            'locations' => $this->whenLoaded('user.locations'),
        ];
    }
}
