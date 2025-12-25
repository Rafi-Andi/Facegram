<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->following->id,
            "full_name" => $this->following->full_name,
            "username" => $this->following->username,
            "bio" => $this->following->bio,
            "is_private" => $this->following->is_private,
            "created_at" => $this->following->created_at,
            "is_requested" => $this->is_accepted ? false : true,
        ];
    }
}
