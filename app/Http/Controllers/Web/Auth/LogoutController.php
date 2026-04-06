<?php

namespace App\Http\Controllers\Web\Auth;

use App\Http\Controllers\Controller;
use App\Models\Ormawa;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\PersonalAccessToken;

class LogoutController extends Controller
{
    public function index()
    {
        if (Auth::guard('ormawa')->check()) {
            /** @var Ormawa|null $ormawa */
            $ormawa = Auth::guard('ormawa')->user();

            if ($ormawa) {
                Ormawa::query()->where('id', $ormawa->id)->update([
                    'active_session_nonce' => null,
                ]);

                PersonalAccessToken::query()
                    ->where('tokenable_type', Ormawa::class)
                    ->where('tokenable_id', $ormawa->id)
                    ->delete();
            }

            Auth::guard('ormawa')->logout();
        } elseif (Auth::guard('admin')->check()) {
            Auth::guard('admin')->logout();
        } else {
            Auth::logout();
        }

        session()->invalidate();
        session()->regenerateToken();

        // Redirect ke login dengan pesan sukses
        return redirect()->route('login')->with('success', 'Anda telah berhasil logout.');
    }
}
