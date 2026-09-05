<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Location extends Model
{
    /** @use HasFactory<\Database\Factories\LocationFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name_fr',
        'name_ar',
        'governorate_fr',
        'governorate_ar',
    ];

    /**
     * Publications that depart from this location.
     *
     * @return HasMany<Publication>
     */
    public function departures(): HasMany
    {
        return $this->hasMany(Publication::class, 'departure_location_id');
    }

    /**
     * Publications that arrive at this location.
     *
     * @return HasMany<Publication>
     */
    public function arrivals(): HasMany
    {
        return $this->hasMany(Publication::class, 'arrival_location_id');
    }
}
