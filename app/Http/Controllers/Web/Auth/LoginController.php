<?php

namespace App\Http\Controllers\Web\Auth;

use App\Http\Controllers\Controller;
use App\Models\Ormawa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Sanctum\PersonalAccessToken;

class LoginController extends Controller
{
    public function index()
    {
        if (Auth::guard('admin')->check()) {
            return redirect()->route('dashboard');
        }

        if (Auth::guard('ormawa')->check()) {
            return redirect()->route('beranda');
        }

        return view('pages.Auth.Login.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'password' => 'required|string|min:6',
        ]);

        $credentials = $request->only('name', 'password');

        if (Auth::guard('admin')->attempt(['name' => $credentials['name'], 'password' => $request->password])) {
            $request->session()->regenerate();
            return redirect()->route('dashboard');
        }

        if (Auth::guard('ormawa')->attempt(['name' => $credentials['name'], 'password' => $request->password])) {
            $request->session()->regenerate();

            /** @var Ormawa|null $ormawa */
            $ormawa = Auth::guard('ormawa')->user();

            if (!$ormawa) {
                return redirect()->route('login')->with('error', 'Gagal membuat sesi login.');
            }

            $nonce = Str::random(64);

            Ormawa::query()->where('id', $ormawa->id)->update([
                'active_session_nonce' => $nonce,
            ]);

            // Login web baru akan mengakhiri sesi mobile sebelumnya.
            PersonalAccessToken::query()
                ->where('tokenable_type', Ormawa::class)
                ->where('tokenable_id', $ormawa->id)
                ->delete();

            $request->session()->put('ormawa_active_session_nonce', $nonce);

            return redirect()->route('beranda');
        }

        return redirect()->back()->with('error', 'Username atau kata sandi salah')->withInput();
    }
}
