<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EmployeeProfileResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            "user" => [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'image' => $this->user->image,
                'phone' => $this->user->phone,
            ],
            "employee_data" => [
                'id' => $this->id,
                'type' => $this->type,
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
                'website' => $this->website,
                'total_rates' => $this->feedbacks->count(),
                'average_rating' => round($this->feedbacks->avg('rating'), 2),
            ],
            'service' => [
                'id' => $this->service->id,
                'name' => $this->service->name,
            ],
            'section' => [
                'id' => $this->section->id,
                'name' => $this->section->name,
            ],
            'location' => $this->user->locations->first() ? [
                'id' => $this->user->locations->first()->id,
                'city' => $this->user->locations->first()->city,
                'bitTitle' => $this->user->locations->first()->bitTitle,
                'street' => $this->user->locations->first()->street,
                'specialMarque' => $this->user->locations->first()->specialMarque,
                'lat' => $this->user->locations->first()->lat,
                'long' => $this->user->locations->first()->long,
            ] : null,
            'feedbacks' => $this->feedbacks->map(function ($feedback) {
                return [
                    'id' => $feedback->id,
                    'rating' => $feedback->rating,
                    'comment' => $feedback->comment,
                    'created_at' => $feedback->created_at->toDateTimeString(),
                    'updated_at' => $feedback->created_at->toDateTimeString(),
                ];
            }),
        ];
    }
}
