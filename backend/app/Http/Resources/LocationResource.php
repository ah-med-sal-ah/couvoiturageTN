<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LocationResource extends JsonResource
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
            'name_fr' => $this->name_fr,
            'name_ar' => $this->name_ar,
            'governorate_fr' => $this->governorate_fr,
            'governorate_ar' => $this->governorate_ar,
        ];
    }
}
