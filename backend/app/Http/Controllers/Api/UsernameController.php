<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UsernameController extends Controller
{
    /**
     * Check whether a username is free, and if not, suggest an available
     * alternative. Public (no auth) so it works during registration, before
     * an account exists - the actual uniqueness constraint is still
     * enforced server-side by RegisterRequest/UpdateProfileRequest at
     * submit time, this is purely a proactive UX hint.
     */
    public function check(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'username' => ['required', 'string', 'max:50'],
        ]);

        $username = $validated['username'];
        $available = ! User::where('username', $username)->exists();

        return response()->json([
            'available' => $available,
            'suggestion' => $available ? null : $this->suggest($username),
        ]);
    }

    /**
     * Build a free username from the requested one by appending a number,
     * matching the same charset RegisterRequest/UpdateProfileRequest allow.
     */
    private function suggest(string $username): string
    {
        $base = preg_replace('/[^a-zA-Z0-9_.]/', '', $username) ?: 'user';
        $base = substr($base, 0, 45);

        for ($i = 1; $i <= 20; $i++) {
            $candidate = "{$base}{$i}";
            if (! User::where('username', $candidate)->exists()) {
                return $candidate;
            }
        }

        do {
            $candidate = $base.random_int(100, 9999);
        } while (User::where('username', $candidate)->exists());

        return $candidate;
    }
}
