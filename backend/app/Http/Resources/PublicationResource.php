<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PublicationResource extends JsonResource
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
            'status' => $this->status,
            // Reservation availability only ever applies to Driver posts -
            // null (rather than false) tells the frontend "not applicable"
            // so a Passenger post can never be mistaken for an available
            // or reserved Driver post.
            //
            // `reservation_enabled` is the owner's own raw stored setting
            // (what History's toggle reflects and controls). `is_unavailable`
            // is the effective, computed state every card/detail view should
            // actually render from - true once the departure deadline has
            // passed, even if the owner never turned reservation on. Keeping
            // these separate means a passed deadline never overwrites the
            // owner's real setting.
            'reservation_enabled' => $this->isDriverPost() ? (bool) $this->reservation_enabled : null,
            'is_unavailable' => $this->isDriverPost() ? $this->isEffectivelyUnavailable() : null,
            'departure_location' => new LocationResource($this->whenLoaded('departureLocation')),
            'arrival_location' => new LocationResource($this->whenLoaded('arrivalLocation')),
            'available_seats' => $this->available_seats,
            'remarks' => $this->remarks,
            'departure_date' => $this->departure_date?->format('Y-m-d'),
            'departure_time' => $this->departure_time !== null ? substr((string) $this->departure_time, 0, 5) : null,
            'phone' => $this->phone,
            'facebook' => $this->facebook,
            'instagram' => $this->instagram,
            'is_own' => $request->user()?->id === $this->user_id,
            'author' => [
                'id' => $this->whenLoaded('user', fn () => $this->user->id),
                'full_name' => $this->whenLoaded('user', fn () => $this->user->full_name),
                'profile_photo_url' => $this->whenLoaded('user', fn () => $this->user->profile_photo_url),
                'gender' => $this->whenLoaded('user', fn () => $this->user->gender),
            ],
            'created_at' => $this->created_at,
        ];
    }
}
