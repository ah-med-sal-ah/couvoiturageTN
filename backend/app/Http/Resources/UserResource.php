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
            'id' => $this->id,
            'profile_photo_url' => $this->profile_photo_url,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'full_name' => $this->full_name,
            'cin' => $this->cin,
            'age' => $this->age,
            'username' => $this->username,
            'gender' => $this->gender,
            'language' => $this->language,
            'is_admin' => (bool) $this->is_admin,
            'created_at' => $this->created_at,
        ];
    }
}
