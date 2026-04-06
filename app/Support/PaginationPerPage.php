<?php

namespace App\Support;

class PaginationPerPage
{
    public static function resolve(int $mobile = 55, int $desktop = 10): int
    {
        $request = request();

        if ($request->header('sec-ch-ua-mobile') === '?1') {
            return $mobile;
        }

        $userAgent = strtolower((string) $request->userAgent());
        $isMobile = (bool) preg_match(
            '/android|iphone|ipad|ipod|blackberry|iemobile|opera mini|mobile/i',
            $userAgent
        );

        return $isMobile ? $mobile : $desktop;
    }
}
