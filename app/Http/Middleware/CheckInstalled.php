<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckInstalled
{
    /**
     * Handle an incoming request.
     *
     * @param Closure(Request): (Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        /*
        |--------------------------------------------------------------------------
        | Check Installation Status
        |--------------------------------------------------------------------------
        */
        $isInstalled = cache()->rememberForever(
            'app_installed',
            fn() => file_exists(storage_path('installed'))
        );

        /*
        |--------------------------------------------------------------------------
        | Route Detection
        |--------------------------------------------------------------------------
        */
        $isInstallerRoute = $request->routeIs('installer.*');

        /*
        |--------------------------------------------------------------------------
        | Allowed Routes Before Installation
        |--------------------------------------------------------------------------
        */
        $allowedRoutes = [
            'installer.*',
        ];

        /*
        |--------------------------------------------------------------------------
        | If Application Not Installed
        |--------------------------------------------------------------------------
        */
        if (
            !$isInstalled &&
            !$request->routeIs($allowedRoutes)
        ) {
            return redirect()->route('installer.welcome');
        }

        /*
        |--------------------------------------------------------------------------
        | Prevent Re-Installation
        |--------------------------------------------------------------------------
        */
        if (
            $isInstalled &&
            $isInstallerRoute
        ) {
            return redirect()->route('login');
        }

        return $next($request);
    }
}
