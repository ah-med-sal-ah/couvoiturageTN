<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\LocationResource;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class LocationController extends Controller
{
    /**
     * List Tunisian locations, optionally filtered by a search term.
     *
     * Used to power departure/arrival selectors and Home page filters.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $search = $request->query('search');

        $locations = Location::query()
            ->when($search, function ($query, $search) {
                $query->where(function ($query) use ($search) {
                    $query->where('name_fr', 'like', "%{$search}%")
                        ->orWhere('name_ar', 'like', "%{$search}%")
                        ->orWhere('governorate_fr', 'like', "%{$search}%")
                        ->orWhere('governorate_ar', 'like', "%{$search}%");
                });
            })
            ->orderBy('name_fr')
            ->get();

        return LocationResource::collection($locations);
    }
}
