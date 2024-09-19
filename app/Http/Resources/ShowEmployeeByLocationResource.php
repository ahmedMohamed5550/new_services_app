<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ShowEmployeeByLocationResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'city' => $this->city,
            'bitTitle' => $this->bitTitle,
            'street' => $this->street,
            'specialMarque' => $this->specialMarque,
            'lat' => $this->lat,
            'long' => $this->long,
            'user' => [
                'id' => $this->user->id ?? null,
                'name' => $this->user->name ?? null,
                'phone' => $this->user->phone ?? null,
                'employee' => $this->user->employee ? [
                    'id' => $this->user->employee->id ?? null,
                    'type' => $this->user->employee->type ?? null,
                    'whatsapp_number' => $this->user->employee->whatsapp_number ?? null,
                    'total_rates' => $this->feedbacks ? $this->feedbacks->count() : 0,
                    'average_rating' => $this->feedbacks ? round($this->feedbacks->avg('rating') ?? 0, 2) : 0,
                    'service' => $this->user->employee->service ? [
                        'id' => $this->user->employee->service->id ?? null,
                        'name' => $this->user->employee->service->name ?? null,
                    ] : null,
                    'section' => $this->user->employee->section ? [
                        'id' => $this->user->employee->section->id ?? null,
                        'name' => $this->user->employee->section->name ?? null,
                    ] : null,
                ] : null,
            ],
        ];
    }




}
