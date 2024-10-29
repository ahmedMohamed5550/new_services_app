<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProjectResource extends JsonResource
{
    public function toArray(Request $request)
    {
        return [
            'id'             => $this->id,
            'desc'           => $this->desc,
            'city'           => $this->city,
            'created_at'     => $this->created_at->toDateTimeString(),
            'updated_at'     => $this->updated_at->toDateTimeString(),
            'views'          => $this->views ?? 0,
            'likes'          => $this->likes()->count(),
            'total_comments' => $this->comments()->count(),
            // 'comments'       => $this->whenLoaded('comments', function () {
            //     return $this->comments ? $this->comments->map(function ($comment) {
            //         return [
            //             'id'      => $comment->id,
            //             'content' => $comment->content,
            //             'created_at' => $comment->created_at->toDateTimeString(),
            //             'updated_at' => $comment->updated_at->toDateTimeString(),
            //             'user'    => [
            //                 'id'    => $comment->user->id ?? null,
            //                 'name'  => $comment->user->name ?? null,
            //                 'image' => $comment->user->image ?? null,
            //             ],
            //         ];
            //     }) : null;
            // }),
            'user'    => $this->whenLoaded('user'),
            'service' => $this->whenLoaded('service'),
            'section' => $this->whenLoaded('section'),
        ];
    }
}
