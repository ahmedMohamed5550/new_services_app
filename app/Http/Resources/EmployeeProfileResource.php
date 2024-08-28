<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EmployeeProfileResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->user->name,
            'image' => $this->user->image,
            'desc' => $this->desc,
            'min_price' => $this->min_price,
            'status' => $this->status,
            'phone' => $this->user->phone,
            // 'average_rating' => $this->when(isset($this->average_rating), $this->average_rating),
            // 'total_rates' => $this->when(isset($this->feedbacks), $this->feedbacks->count(), 0),
        ];
    }
}
