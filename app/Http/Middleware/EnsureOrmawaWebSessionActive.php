<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureOrmawaWebSessionActive
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::guard('ormawa')->check()) {
            return $next($request);
        }

        $user = Auth::guard('ormawa')->user();
        $sessionNonce = (string) $request->session()->get('ormawa_active_session_nonce', '');
        $activeNonce = (string) ($user->active_session_nonce ?? '');

        if ($sessionNonce === '' || $activeNonce === '' || !hash_equals($activeNonce, $sessionNonce)) {
            Auth::guard('ormawa')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->with('error', 'Session habis. Akun Anda login di perangkat lain.');
        }

        return $next($request);
    }
}
