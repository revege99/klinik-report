<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdminOrMaster
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || ! $user->canManageClinicProfile()) {
            abort(403, 'Menu ini hanya bisa diakses oleh admin klinik atau master.');
        }

        return $next($request);
    }
}
