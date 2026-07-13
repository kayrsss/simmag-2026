<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SessionTimeout
{
    private const TIMEOUT_SECONDS = 1800;

    public function handle(
        Request $request,
        Closure $next
    ): Response {
        if (! Auth::guard('web')->check()) {
            return $next($request);
        }

        $lastActivity = (int) $request
            ->session()
            ->get(
                'last_activity_at',
                now()->timestamp
            );

        $inactiveSeconds =
            now()->timestamp
            - $lastActivity;

        if (
            $inactiveSeconds
            >= self::TIMEOUT_SECONDS
        ) {
            Auth::guard('web')->logout();

            $request
                ->session()
                ->invalidate();

            $request
                ->session()
                ->regenerateToken();

            return redirect()
                ->to('/login')
                ->with(
                    'status',
                    'Sesi berakhir karena tidak ada aktivitas selama 30 menit.'
                );
        }

        $request
            ->session()
            ->put(
                'last_activity_at',
                now()->timestamp
            );

        return $next($request);
    }
}