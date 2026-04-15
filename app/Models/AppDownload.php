<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppDownload extends Model
{
    protected $fillable = [
        'ip_address',
        'user_agent',
        'device_type',
        'platform',
        'version',
    ];

    /**
     * Get total download count
     */
    public static function totalDownloads(): int
    {
        return static::count();
    }

    /**
     * Get downloads today
     */
    public static function todayDownloads(): int
    {
        return static::whereDate('created_at', today())->count();
    }

    /**
     * Get downloads this month
     */
    public static function monthDownloads(): int
    {
        return static::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
    }

    /**
     * Parse device info from user agent
     */
    public static function parseDeviceInfo(string $userAgent): array
    {
        $deviceType = 'Unknown';
        $platform = 'Unknown';

        if (preg_match('/Android/i', $userAgent)) {
            $platform = 'Android';
            if (preg_match('/Mobile/i', $userAgent)) {
                $deviceType = 'Mobile';
            } else {
                $deviceType = 'Tablet';
            }
        } elseif (preg_match('/iPhone/i', $userAgent)) {
            $deviceType = 'Mobile';
            $platform = 'iOS';
        } elseif (preg_match('/iPad/i', $userAgent)) {
            $deviceType = 'Tablet';
            $platform = 'iOS';
        } elseif (preg_match('/Windows/i', $userAgent)) {
            $deviceType = 'Desktop';
            $platform = 'Windows';
        } elseif (preg_match('/Macintosh/i', $userAgent)) {
            $deviceType = 'Desktop';
            $platform = 'macOS';
        } elseif (preg_match('/Linux/i', $userAgent)) {
            $deviceType = 'Desktop';
            $platform = 'Linux';
        }

        return [
            'device_type' => $deviceType,
            'platform' => $platform,
        ];
    }
}
