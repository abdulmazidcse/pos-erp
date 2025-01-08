<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CheckAppVersion
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $currentVersion = $request->header('App-Version');
        $platform = $request->header('Platform');

        if ($currentVersion && $platform) {
            $versionData = DB::table('app_version_control')
                ->where('platform', $platform)
                ->first();

            if ($versionData && version_compare($currentVersion, $versionData->latest_version, '<')) {
                $request->attributes->set('optional_update', true);
            }
        }

        return $next($request);
    }
}
