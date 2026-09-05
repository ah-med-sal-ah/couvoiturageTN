<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Publication\StorePublicationRequest;
use App\Http\Requests\Publication\UpdateReservationRequest;
use App\Http\Resources\PublicationResource;
use App\Models\Publication;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class PublicationController extends Controller
{
    /**
     * List publications, newest first, with optional status/route filters.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $validated = $request->validate([
            'status' => ['sometimes', 'in:passenger,driver'],
            'departure_location_id' => ['sometimes', 'integer', 'exists:locations,id'],
            'arrival_location_id' => ['sometimes', 'integer', 'exists:locations,id'],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:50'],
        ]);

        // `mine=1` scopes the feed to the authenticated user's own posts -
        // this is what powers the History page, reusing this same endpoint
        // and its existing status/location filters instead of a new route.
        // Read leniently via boolean() (accepts true/1/"true"/"1"/"on"/"yes")
        // rather than Laravel's strict `boolean` validation rule, which
        // rejects the string "true" that axios sends for a JS boolean param.
        $mine = $request->boolean('mine');

        $publications = Publication::query()
            ->with(['user', 'departureLocation', 'arrivalLocation'])
            ->when($mine, fn ($query) => $query->where('user_id', $request->user()->id))
            ->when($validated['status'] ?? null, fn ($query, $status) => $query->where('status', $status))
            ->when($validated['departure_location_id'] ?? null, fn ($query, $id) => $query->where('departure_location_id', $id))
            ->when($validated['arrival_location_id'] ?? null, fn ($query, $id) => $query->where('arrival_location_id', $id))
            ->latest('created_at')
            ->paginate($validated['per_page'] ?? 10);

        return PublicationResource::collection($publications);
    }

    /**
     * Create a new publication owned by the authenticated user.
     */
    public function store(StorePublicationRequest $request): PublicationResource
    {
        $publication = $request->user()->publications()->create($request->validated());

        $publication->load(['user', 'departureLocation', 'arrivalLocation']);

        return new PublicationResource($publication);
    }

    /**
     * Show a single publication with full details.
     */
    public function show(Publication $publication): PublicationResource
    {
        $publication->load(['user', 'departureLocation', 'arrivalLocation']);

        return new PublicationResource($publication);
    }

    /**
     * Toggle a Driver post's reservation availability. Ownership and the
     * Driver-only rule are enforced by UpdateReservationRequest, not here -
     * the frontend's own restrictions are never trusted as the boundary.
     */
    public function updateReservation(UpdateReservationRequest $request, Publication $publication): PublicationResource
    {
        $publication->update([
            'reservation_enabled' => $request->boolean('reservation_enabled'),
        ]);

        $publication->load(['user', 'departureLocation', 'arrivalLocation']);

        return new PublicationResource($publication);
    }
}
