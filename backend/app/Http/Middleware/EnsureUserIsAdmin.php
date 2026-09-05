<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
{
    /**
     * Reject any request whose authenticated user isn't an administrator.
     * Applied after `auth:sanctum`, so `$request->user()` is already
     * resolved here - this is the real, server-side authorization boundary
     * for admin-only routes; the frontend's own nav/route checks are only
     * for UX and are never trusted as security on their own.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user()?->is_admin) {
            abort(403, 'This action is unauthorized.');
        }

        return $next($request);
    }
}
