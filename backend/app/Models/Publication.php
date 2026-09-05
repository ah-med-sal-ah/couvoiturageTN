<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

class Publication extends Model
{
    /** @use HasFactory<\Database\Factories\PublicationFactory> */
    use HasFactory;

    public const STATUS_PASSENGER = 'passenger';

    public const STATUS_DRIVER = 'driver';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'status',
        'reservation_enabled',
        'departure_location_id',
        'arrival_location_id',
        'available_seats',
        'remarks',
        'departure_date',
        'departure_time',
        'phone',
        'facebook',
        'instagram',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'departure_date' => 'date:Y-m-d',
            'available_seats' => 'integer',
            'reservation_enabled' => 'boolean',
        ];
    }

    /**
     * Whether this publication is a Driver post - the only kind that
     * carries a meaningful reservation availability state.
     */
    public function isDriverPost(): bool
    {
        return $this->status === self::STATUS_DRIVER;
    }

    /**
     * Whether the trip's departure date/time has already passed, compared
     * against the server's current time (never the client's). Combines the
     * two raw columns rather than requiring a dedicated datetime column.
     */
    public function isPastDeadline(): bool
    {
        $departsAt = Carbon::parse(
            $this->departure_date->format('Y-m-d').' '.$this->departure_time
        );

        return $departsAt->isPast();
    }

    /**
     * The single source of truth for whether a Driver post should read as
     * unavailable/reserved to viewers - true once its departure deadline
     * has passed, regardless of the owner's own `reservation_enabled`
     * setting (which is left untouched either way; see the migration and
     * PublicationController for why the raw flag is never overwritten).
     * Always false for Passenger posts, which have no reservation concept.
     */
    public function isEffectivelyUnavailable(): bool
    {
        if (! $this->isDriverPost()) {
            return false;
        }

        return (bool) $this->reservation_enabled || $this->isPastDeadline();
    }

    /**
     * The author of the publication.
     *
     * @return BelongsTo<User, Publication>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * The publication's departure location.
     *
     * @return BelongsTo<Location, Publication>
     */
    public function departureLocation(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'departure_location_id');
    }

    /**
     * The publication's arrival location.
     *
     * @return BelongsTo<Location, Publication>
     */
    public function arrivalLocation(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'arrival_location_id');
    }
}
