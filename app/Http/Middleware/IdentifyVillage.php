<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpFoundation\Response;

class IdentifyVillage
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Schema::hasTable('villages')) {
            return $next($request);
        }

        $host = $request->getHost();
        $subdomain = explode('.', $host)[0] ?? null;

        $village = null;

        if ($subdomain && !in_array($host, ['localhost', '127.0.0.1'], true)) {
            $village = \App\Models\Village::where('slug', $subdomain)->first();
        }

        if (!$village) {
            $village = \App\Models\Village::query()->first();
        }

        if ($village) {
            app()->instance('currentVillage', $village);
        }

        return $next($request);
    }
}
