<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\AppBaseController;
class VersionController extends AppBaseController
{
    public function checkVersion(Request $request)
    {
        $request->validate([
            'version' => 'required|string',
            'platform' => 'required|string|in:Android,iOS',
        ]);

        $platform = $request->platform;
        $currentVersion = $request->version;

        // Fetch version details from the database
        $versionData = DB::table('app_version_control')
            ->where('platform', $platform)
            ->first();

        if (!$versionData) {
            return response()->json(['error' => 'Platform not supported'], 400);
        }

        // Determine update requirements
        $forceUpdate = version_compare($currentVersion, $versionData->minimum_version, '<');
        $optionalUpdate = version_compare($currentVersion, $versionData->latest_version, '<') && !$forceUpdate;

        // Respond with the update status
        return response()->json([
            'force_update' => $forceUpdate,
            'optional_update' => $optionalUpdate,
            'update_url' => $platform === 'Android'
                ? 'https://play.google.com/store/apps/details?id=com.yourapp.package'
                : 'https://apps.apple.com/app/idYOUR_APP_ID',
        ]);
    }
}
