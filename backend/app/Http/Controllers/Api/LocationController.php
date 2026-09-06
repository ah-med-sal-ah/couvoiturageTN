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
                // Plain `like` is case-insensitive on SQLite but
                // case-sensitive on PostgreSQL. Wrapping both sides in
                // LOWER() keeps the search case-insensitive on every driver
                // the app runs on (PostgreSQL at runtime, SQLite in tests)
                // instead of relying on SQLite's non-standard default.
                $needle = '%'.mb_strtolower($search).'%';

                $query->where(function ($query) use ($needle) {
                    $query->whereRaw('LOWER(name_fr) LIKE ?', [$needle])
                        ->orWhereRaw('LOWER(name_ar) LIKE ?', [$needle])
                        ->orWhereRaw('LOWER(governorate_fr) LIKE ?', [$needle])
                        ->orWhereRaw('LOWER(governorate_ar) LIKE ?', [$needle]);
                });
            })
            ->orderBy('name_fr')
            ->get();

        return LocationResource::collection($locations);
    }
}
