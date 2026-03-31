<?php

namespace App\Http\Middleware;

use App\Models\Badge;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureLegacyBadge
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->created_at && $user->created_at->diffInDays(now()) >= 365) {
            $legacyBadge = Badge::where('name', 'Utilisateur ancien')->first();

            if ($legacyBadge) {
                $user->badges()->syncWithoutDetaching([$legacyBadge->id]);
            }
        }

        return $next($request);
    }
}
