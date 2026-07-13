<?php

namespace App\Http\Middleware;

use App\Models\User;
use App\Support\SimmagRole;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureSimmagRole
{
    public function handle(
        Request $request,
        Closure $next,
        string ...$roles
    ): Response {
        if (! Auth::guard('web')->check()) {
            return redirect()
                ->route('login');
        }

        $user = Auth::guard('web')->user();

        if (! $user instanceof User) {
            Auth::guard('web')->logout();

            $request
                ->session()
                ->invalidate();

            $request
                ->session()
                ->regenerateToken();

            return redirect()
                ->route('login');
        }

        if (
            ! SimmagRole::hasAny(
                $user,
                $roles
            )
        ) {
            abort(
                403,
                'Anda tidak memiliki izin untuk mengakses halaman ini.'
            );
        }

        return $next($request);
    }
}