<?php

namespace App\Http\Middleware;

use App\Models\Ormawa;
use Closure;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;
use Symfony\Component\HttpFoundation\Response;

class EnsureOrmawaApiUser
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user || !($user instanceof Ormawa)) {
            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak. API ini hanya untuk akun ormawa.',
            ], 403);
        }

        $plainToken = (string) $request->bearerToken();
        $tokenModel = PersonalAccessToken::findToken($plainToken);

        if (!$tokenModel || !$tokenModel->can('ormawa')) {
            return response()->json([
                'success' => false,
                'message' => 'Token tidak valid untuk API ormawa.',
            ], 403);
        }

        if ((string) $tokenModel->tokenable_type !== Ormawa::class || (int) $tokenModel->tokenable_id !== (int) $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Token tidak sesuai akun yang aktif.',
            ], 403);
        }

        $activeNonce = (string) ($user->active_session_nonce ?? '');
        $tokenName = (string) $tokenModel->name;
        $tokenNonce = '';

        if (str_contains($tokenName, '|')) {
            $parts = explode('|', $tokenName);
            $tokenNonce = (string) end($parts);
        }

        if ($activeNonce === '' || $tokenNonce === '' || !hash_equals($activeNonce, $tokenNonce)) {
            $tokenModel->delete();

            return response()->json([
                'success' => false,
                'message' => 'Session habis. Silakan login ulang.',
                'code' => 'SESSION_EXPIRED',
            ], 401);
        }

        return $next($request);
    }
}
