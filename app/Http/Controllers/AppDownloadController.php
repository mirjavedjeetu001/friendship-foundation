<?php

namespace App\Http\Controllers;

use App\Models\AppDownload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AppDownloadController extends Controller
{
    /**
     * Show download page
     */
    public function index()
    {
        $totalDownloads = AppDownload::totalDownloads();
        $todayDownloads = AppDownload::todayDownloads();
        $monthDownloads = AppDownload::monthDownloads();
        
        $appVersion = '1.0';
        $appSize = '2.7 MB';
        $lastUpdated = '15 April 2026';
        $minAndroid = 'Android 7.0+';

        return view('app-download', compact(
            'totalDownloads',
            'todayDownloads', 
            'monthDownloads',
            'appVersion',
            'appSize',
            'lastUpdated',
            'minAndroid'
        ));
    }

    /**
     * Download APK and track
     */
    public function download(Request $request)
    {
        // Track download
        $deviceInfo = AppDownload::parseDeviceInfo($request->userAgent() ?? '');
        
        AppDownload::create([
            'ip_address' => $request->ip(),
            'user_agent' => substr($request->userAgent() ?? '', 0, 255),
            'device_type' => $deviceInfo['device_type'],
            'platform' => $deviceInfo['platform'],
            'version' => '1.0',
        ]);

        // Check if APK exists
        $apkPath = public_path('downloads/AlliedGroup.apk');
        
        if (file_exists($apkPath)) {
            return response()->download($apkPath, 'AlliedGroup.apk', [
                'Content-Type' => 'application/vnd.android.package-archive',
            ]);
        }

        return redirect()->back()->with('error', 'APK ফাইল পাওয়া যায়নি। অনুগ্রহ করে পরে চেষ্টা করুন।');
    }

    /**
     * Get download count (API for AJAX)
     */
    public function count()
    {
        return response()->json([
            'total' => AppDownload::totalDownloads(),
            'today' => AppDownload::todayDownloads(),
            'month' => AppDownload::monthDownloads(),
        ]);
    }
}
