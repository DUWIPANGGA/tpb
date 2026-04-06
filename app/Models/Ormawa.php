<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Model;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\HasApiTokens;

class Ormawa extends Model
{
    use HasApiTokens, HasFactory;

    protected $table = 'ormawas';

    protected $fillable = ['name', 'nim',  'organisasi', 'password', 'active_session_nonce'];

    protected $hidden = [
        'password',
        'active_session_nonce',
    ];

    protected $casts = [
        'password' => 'hashed',
    ];

    protected static function booted()
    {
        static::creating(function ($mahasiswa) {
            $mahasiswa->password = Hash::make('@Poli' . $mahasiswa->nim);
        });
    }
}
