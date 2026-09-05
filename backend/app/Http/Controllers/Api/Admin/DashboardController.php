<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Publication;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    /**
     * Number of days shown on the "posts per day" chart. Capped rather than
     * unbounded so the chart stays readable as the table grows - a
     * dashboard concern, not a limitation of the underlying data.
     */
    private const POSTS_PER_DAY_WINDOW = 30;

    /**
     * Platform-wide statistics for the admin dashboard. Counts and grouping
     * are done in the database (see below) rather than hydrating every row
     * into PHP/JS just to tally them - the one exception is the Driver
     * reservation split, which needs Publication::isEffectivelyUnavailable()
     * (deadline + reservation_enabled combined) evaluated per row; that
     * logic lives once on the model (see Part 4 notes there) so it can't
     * drift from what Home/Passenger/Driver/History actually display.
     */
    public function index(): JsonResponse
    {
        $totalUsers = User::count();

        $totalPosts = Publication::count();
        $passengerPosts = Publication::where('status', Publication::STATUS_PASSENGER)->count();
        $driverPosts = Publication::where('status', Publication::STATUS_DRIVER)->count();

        $driverAvailability = Publication::where('status', Publication::STATUS_DRIVER)
            // `status` must be selected too, even though it's the filter -
            // isDriverPost()/isEffectivelyUnavailable() both read it off the
            // model, and an unselected column comes back null in PHP (not
            // "still driver"), which would silently make every row read as
            // available regardless of its real reservation/deadline state.
            ->get(['status', 'reservation_enabled', 'departure_date', 'departure_time'])
            ->reduce(function (array $carry, Publication $publication) {
                $publication->isEffectivelyUnavailable() ? $carry['reserved']++ : $carry['available']++;

                return $carry;
            }, ['available' => 0, 'reserved' => 0]);

        $postsPerDay = Publication::query()
            ->selectRaw('date(created_at) as date, count(*) as count')
            ->where('created_at', '>=', now()->subDays(self::POSTS_PER_DAY_WINDOW - 1)->startOfDay())
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return response()->json([
            'users' => [
                'total' => $totalUsers,
            ],
            'posts' => [
                'total' => $totalPosts,
                'passenger' => $passengerPosts,
                'driver' => $driverPosts,
                'passenger_percentage' => $this->percentage($passengerPosts, $totalPosts),
                'driver_percentage' => $this->percentage($driverPosts, $totalPosts),
            ],
            'driver_reservations' => [
                'total_driver_posts' => $driverPosts,
                'available' => $driverAvailability['available'],
                'reserved' => $driverAvailability['reserved'],
                'available_percentage' => $this->percentage($driverAvailability['available'], $driverPosts),
                'reserved_percentage' => $this->percentage($driverAvailability['reserved'], $driverPosts),
            ],
            'posts_per_day' => $postsPerDay->map(fn ($row) => [
                'date' => $row->date,
                'count' => (int) $row->count,
            ]),
        ]);
    }

    /**
     * Round to one decimal; zero (never NaN/division-by-zero) when the
     * denominator is empty.
     */
    private function percentage(int $part, int $total): float
    {
        return $total > 0 ? round(($part / $total) * 100, 1) : 0.0;
    }
}
