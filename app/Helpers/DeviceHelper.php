<?php

namespace App\Helpers;

class DeviceHelper
{
    public static function getBrowser($userAgent): string
    {
        if (str_contains($userAgent, 'Edg')) {
            return 'Microsoft Edge';
        }

        if (str_contains($userAgent, 'Chrome')) {
            return 'Chrome';
        }

        if (str_contains($userAgent, 'Firefox')) {
            return 'Firefox';
        }

        if (str_contains($userAgent, 'Safari')) {
            return 'Safari';
        }

        if (str_contains($userAgent, 'Opera') || str_contains($userAgent, 'OPR')) {
            return 'Opera';
        }

        return 'Unknown Browser';
    }

    public static function getPlatform($userAgent): string
    {
        if (str_contains($userAgent, 'Windows')) {
            return 'Windows';
        }

        if (str_contains($userAgent, 'Macintosh')) {
            return 'MacOS';
        }

        if (str_contains($userAgent, 'Android')) {
            return 'Android';
        }

        if (str_contains($userAgent, 'iPhone')) {
            return 'iPhone';
        }

        if (str_contains($userAgent, 'Linux')) {
            return 'Linux';
        }

        return 'Unknown OS';
    }

    public static function getDeviceName($platform, $browser): string
    {
        return $platform . ' - ' . $browser;
    }
}