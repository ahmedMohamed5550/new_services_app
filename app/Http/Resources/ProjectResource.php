<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProjectResource extends JsonResource
{
    public function toArray(Request $request)
    {
        return [
            'id'         => $this->id,
            'desc'       => $this->desc,
            'city'       => $this->city,
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString(),
            'views'      => $this->views ? $this->views : 0,
            'likes'      => $this->likes()->count() ? $this->likes()->count() : 0,
            'user'       => $this->whenLoaded('user'),
            'service'    => $this->whenLoaded('service'),
            'section'    => $this->whenLoaded('section'),
        ];
    }
}
