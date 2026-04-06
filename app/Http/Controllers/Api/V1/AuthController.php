<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Ormawa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthController extends ApiController
{
    public function login(Request $request)
    {
        try {
            $credentials = $request->validate([
                'name' => ['nullable', 'string', 'max:255', 'required_without:nim'],
                'nim' => ['nullable', 'string', 'max:50', 'required_without:name'],
                'password' => ['required', 'string', 'max:255'],
                'device_name' => ['nullable', 'string', 'max:100'],
            ]);
        } catch (ValidationException $e) {
            return $this->error('Validasi gagal.', $e->errors());
        }

        $query = Ormawa::query()->select(['id', 'name', 'nim', 'organisasi', 'password']);

        $query->where(function ($q) use ($credentials) {
            if (!empty($credentials['name'])) {
                $q->orWhere('name', $credentials['name']);
            }
            if (!empty($credentials['nim'])) {
                $q->orWhere('nim', $credentials['nim']);
            }
        });

        $ormawa = $query->first();

        if (!$ormawa || !Hash::check($credentials['password'], $ormawa->password)) {
            return $this->error('Name/NIM atau password tidak valid.', [], 401);
        }

        $nonce = Str::random(64);
        $ormawa->forceFill([
            'active_session_nonce' => $nonce,
        ])->save();

        // Satu user satu token aktif; login mobile baru akan mengakhiri sesi web sebelumnya.
        $ormawa->tokens()->delete();

        $device = $credentials['device_name'] ?? 'flutter-mobile';
        $tokenName = $device . '|' . $nonce;
        $token = $ormawa->createToken($tokenName, ['ormawa'])->plainTextToken;

        return $this->success([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => [
                'id' => $ormawa->id,
                'name' => $ormawa->name,
                'nim' => $ormawa->nim,
                'organisasi' => $ormawa->organisasi,
            ],
        ], 'Login berhasil.');
    }

    public function me(Request $request)
    {
        /** @var Ormawa $user */
        $user = $request->user();

        $fresh = Ormawa::query()
            ->select(['id', 'name', 'nim', 'organisasi', 'created_at', 'updated_at'])
            ->where('id', $user->id)
            ->first();

        return $this->success($fresh, 'Profil berhasil diambil.');
    }

    public function logout(Request $request)
    {
        /** @var Ormawa $user */
        $user = $request->user();

        if ($user) {
            $user->forceFill([
                'active_session_nonce' => null,
            ])->save();
        }

        if ($user) {
            $user->tokens()->delete();
        }

        return $this->success(null, 'Logout berhasil.');
    }
}
