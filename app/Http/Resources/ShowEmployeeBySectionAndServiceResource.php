<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ShowEmployeeBySectionAndServiceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'image' => $this->user->image,
                'phone' => $this->user->phone,
                'userType' => $this->user->userType,
            ],
            'employee' => [
                'id' => $this->id,
                'description' => $this->description,
                'total_rates' => $this->feedbacks->count(),
                'average_rating' => round($this->feedbacks->avg('rating'), 2),
                'likes'=>$this->whenLoaded('likes')->count(),
            ],
            'service' => [
                'id' => $this->service->id,
                'name' => $this->service->name,
                'desc' => $this->service->desc,
            ],
            'section' => [
                'id' => $this->section->id,
                'name' => $this->section->name,
                'desc' => $this->section->desc,
            ],
        ];
    }

}
