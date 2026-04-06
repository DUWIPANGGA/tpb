<?php

use App\Http\Controllers\Web\Admin\Barang\BarangController;
use App\Http\Controllers\Web\Admin\Barang\Kategori\KategoriController;
use App\Http\Controllers\Web\Admin\Barang\Satuan\SatuanController;
use App\Http\Controllers\Web\Admin\DashboardController;
use App\Http\Controllers\Web\Admin\Laporan\LaporanController;
use App\Http\Controllers\Web\Admin\Pengguna\AdminController;
use App\Http\Controllers\Web\Admin\Pengguna\PenggunaController;
use App\Http\Controllers\Web\Admin\Permohonan\PermohonanController;
use App\Http\Controllers\Web\Auth\LoginController;
use App\Http\Controllers\Web\Auth\LogoutController;
use App\Http\Controllers\Web\Ormawa\BerandaController;
use App\Http\Controllers\Web\Ormawa\InformasiController;
use App\Http\Controllers\Web\Ormawa\KeranjangController;
use App\Http\Controllers\Web\Ormawa\PengembalianController;
use App\Http\Controllers\Web\Admin\Pengembalian\PengembalianController as PengembalianControllerAdmin;
use App\Http\Controllers\Web\Ormawa\RiwayatController;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [LoginController::class, 'index'])->name('login');
Route::post('masuk', [LoginController::class, 'store'])->name('login.store');

Route::get('/api/docs', function () {
    return view('docs.swagger', [
        'openapiUrl' => route('api.docs.openapi'),
    ]);
})->name('api.docs');

Route::get('/api/docs/openapi.json', function () {
    return response()->json([
        'openapi' => '3.0.3',
        'info' => [
            'title' => 'TPB Ormawa Mobile API',
            'version' => '1.0.0',
            'description' => 'API untuk integrasi mobile app Flutter. Khusus akun ormawa.',
        ],
        'servers' => [
            [
                'url' => url('/api/v1'),
                'description' => 'Current environment',
            ],
        ],
        'tags' => [
            ['name' => 'Auth'],
            ['name' => 'Barang'],
            ['name' => 'Keranjang'],
            ['name' => 'Transaksi'],
            ['name' => 'Realtime'],
        ],
        'components' => [
            'securitySchemes' => [
                'bearerAuth' => [
                    'type' => 'http',
                    'scheme' => 'bearer',
                    'bearerFormat' => 'Sanctum Token',
                ],
            ],
            'schemas' => [
                'ApiSuccess' => [
                    'type' => 'object',
                    'properties' => [
                        'success' => ['type' => 'boolean', 'example' => true],
                        'message' => ['type' => 'string', 'example' => 'OK'],
                        'data' => ['type' => 'object'],
                        'server_time' => ['type' => 'string', 'example' => '2026-04-06T10:00:00+07:00'],
                    ],
                ],
            ],
        ],
        'paths' => [
            '/auth/login' => [
                'post' => [
                    'tags' => ['Auth'],
                    'summary' => 'Login Ormawa (utama: name + password)',
                    'requestBody' => [
                        'required' => true,
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'required' => ['password'],
                                    'properties' => [
                                        'name' => ['type' => 'string', 'example' => 'Esa'],
                                        'nim' => ['type' => 'string', 'example' => '1234567', 'description' => 'Opsional, kompatibilitas lama'],
                                        'password' => ['type' => 'string', 'example' => '@Poli1234567'],
                                        'device_name' => ['type' => 'string', 'example' => 'flutter-android'],
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'responses' => [
                        '200' => ['description' => 'Login berhasil'],
                        '401' => ['description' => 'Unauthorized'],
                        '422' => ['description' => 'Validation error'],
                    ],
                ],
            ],
            '/auth/me' => [
                'get' => [
                    'tags' => ['Auth'],
                    'summary' => 'Get profile',
                    'security' => [['bearerAuth' => []]],
                    'responses' => [
                        '200' => ['description' => 'Success'],
                        '401' => ['description' => 'Unauthorized / SESSION_EXPIRED'],
                    ],
                ],
            ],
            '/auth/logout' => [
                'post' => [
                    'tags' => ['Auth'],
                    'summary' => 'Logout',
                    'security' => [['bearerAuth' => []]],
                    'responses' => [
                        '200' => ['description' => 'Success'],
                        '401' => ['description' => 'Unauthorized / SESSION_EXPIRED'],
                    ],
                ],
            ],
            '/barang' => [
                'get' => [
                    'tags' => ['Barang'],
                    'summary' => 'List barang',
                    'security' => [['bearerAuth' => []]],
                    'parameters' => [
                        ['name' => 'search', 'in' => 'query', 'schema' => ['type' => 'string']],
                        ['name' => 'per_page', 'in' => 'query', 'schema' => ['type' => 'integer', 'default' => 10]],
                    ],
                    'responses' => [
                        '200' => ['description' => 'Success'],
                        '401' => ['description' => 'Unauthorized / SESSION_EXPIRED'],
                    ],
                ],
            ],
            '/barang/{id}' => [
                'get' => [
                    'tags' => ['Barang'],
                    'summary' => 'Detail barang',
                    'security' => [['bearerAuth' => []]],
                    'parameters' => [
                        ['name' => 'id', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'integer']],
                    ],
                    'responses' => [
                        '200' => ['description' => 'Success'],
                        '401' => ['description' => 'Unauthorized / SESSION_EXPIRED'],
                        '404' => ['description' => 'Not found'],
                    ],
                ],
            ],
            '/keranjang' => [
                'get' => [
                    'tags' => ['Keranjang'],
                    'summary' => 'List keranjang',
                    'security' => [['bearerAuth' => []]],
                    'responses' => [
                        '200' => ['description' => 'Success'],
                        '401' => ['description' => 'Unauthorized / SESSION_EXPIRED'],
                    ],
                ],
                'post' => [
                    'tags' => ['Keranjang'],
                    'summary' => 'Tambah item keranjang',
                    'security' => [['bearerAuth' => []]],
                    'requestBody' => [
                        'required' => true,
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'required' => ['barang_id', 'jumlah'],
                                    'properties' => [
                                        'barang_id' => ['type' => 'integer', 'example' => 1],
                                        'jumlah' => ['type' => 'integer', 'example' => 1],
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'responses' => [
                        '201' => ['description' => 'Created'],
                        '401' => ['description' => 'Unauthorized / SESSION_EXPIRED'],
                        '422' => ['description' => 'Validation error'],
                    ],
                ],
            ],
            '/keranjang/{id}' => [
                'put' => [
                    'tags' => ['Keranjang'],
                    'summary' => 'Update item keranjang',
                    'security' => [['bearerAuth' => []]],
                    'parameters' => [
                        ['name' => 'id', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'integer']],
                    ],
                    'requestBody' => [
                        'required' => true,
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'required' => ['jumlah'],
                                    'properties' => [
                                        'jumlah' => ['type' => 'integer', 'example' => 2],
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'responses' => [
                        '200' => ['description' => 'Success'],
                        '401' => ['description' => 'Unauthorized / SESSION_EXPIRED'],
                    ],
                ],
                'delete' => [
                    'tags' => ['Keranjang'],
                    'summary' => 'Hapus item keranjang',
                    'security' => [['bearerAuth' => []]],
                    'parameters' => [
                        ['name' => 'id', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'integer']],
                    ],
                    'responses' => [
                        '200' => ['description' => 'Success'],
                        '401' => ['description' => 'Unauthorized / SESSION_EXPIRED'],
                    ],
                ],
            ],
            '/permohonan' => [
                'get' => [
                    'tags' => ['Transaksi'],
                    'summary' => 'List permohonan',
                    'security' => [['bearerAuth' => []]],
                    'responses' => [
                        '200' => ['description' => 'Success'],
                        '401' => ['description' => 'Unauthorized / SESSION_EXPIRED'],
                    ],
                ],
            ],
            '/permohonan/submit' => [
                'post' => [
                    'tags' => ['Transaksi'],
                    'summary' => 'Submit permohonan dari keranjang',
                    'security' => [['bearerAuth' => []]],
                    'requestBody' => [
                        'required' => true,
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'required' => ['nama_kegiatan', 'hari_atau_tanggal', 'waktu_mulai', 'waktu_selesai', 'phone'],
                                    'properties' => [
                                        'unit_kerja' => ['type' => 'string', 'example' => 'HIMA TI'],
                                        'nama_kegiatan' => ['type' => 'string', 'example' => 'Seminar Proker'],
                                        'hari_atau_tanggal' => ['type' => 'string', 'example' => '2026-04-20'],
                                        'waktu_mulai' => ['type' => 'string', 'example' => '08:00'],
                                        'waktu_selesai' => ['type' => 'string', 'example' => '11:00'],
                                        'phone' => ['type' => 'string', 'example' => '081234567890'],
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'responses' => [
                        '201' => ['description' => 'Created'],
                        '401' => ['description' => 'Unauthorized / SESSION_EXPIRED'],
                    ],
                ],
            ],
            '/pengembalian' => [
                'get' => [
                    'tags' => ['Transaksi'],
                    'summary' => 'List pengembalian',
                    'security' => [['bearerAuth' => []]],
                    'responses' => [
                        '200' => ['description' => 'Success'],
                        '401' => ['description' => 'Unauthorized / SESSION_EXPIRED'],
                    ],
                ],
            ],
            '/pengembalian/{permohonanId}' => [
                'post' => [
                    'tags' => ['Transaksi'],
                    'summary' => 'Submit pengembalian',
                    'security' => [['bearerAuth' => []]],
                    'parameters' => [
                        ['name' => 'permohonanId', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'integer']],
                    ],
                    'requestBody' => [
                        'required' => true,
                        'content' => [
                            'multipart/form-data' => [
                                'schema' => [
                                    'type' => 'object',
                                    'required' => ['bukti_foto'],
                                    'properties' => [
                                        'bukti_foto' => ['type' => 'string', 'format' => 'binary'],
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'responses' => [
                        '201' => ['description' => 'Created'],
                        '401' => ['description' => 'Unauthorized / SESSION_EXPIRED'],
                    ],
                ],
            ],
            '/riwayat' => [
                'get' => [
                    'tags' => ['Transaksi'],
                    'summary' => 'List riwayat',
                    'security' => [['bearerAuth' => []]],
                    'responses' => [
                        '200' => ['description' => 'Success'],
                        '401' => ['description' => 'Unauthorized / SESSION_EXPIRED'],
                    ],
                ],
            ],
            '/sync/status' => [
                'get' => [
                    'tags' => ['Realtime'],
                    'summary' => 'Sinkronisasi status realtime',
                    'security' => [['bearerAuth' => []]],
                    'parameters' => [
                        ['name' => 'since', 'in' => 'query', 'schema' => ['type' => 'string', 'example' => '2026-04-06T10:00:00+07:00']],
                    ],
                    'responses' => [
                        '200' => ['description' => 'Success'],
                        '401' => ['description' => 'Unauthorized / SESSION_EXPIRED'],
                    ],
                ],
            ],
        ],
    ]);
})->name('api.docs.openapi');

Route::get('/api/docs/markdown', function () {
    $docPath = base_path('docs/API_MOBILE.md');

    abort_unless(File::exists($docPath), 404, 'Dokumentasi API tidak ditemukan.');

    $markdown = File::get($docPath);
    $html = Str::markdown($markdown);

    return view('docs.api-mobile', [
        'content' => $html,
    ]);
})->name('api.docs.markdown');

Route::get('/api/docs/postman', function () {
    $postmanPath = base_path('docs/postman/TPB_Ormawa_API_v1.postman_collection.json');

    abort_unless(File::exists($postmanPath), 404, 'Postman collection tidak ditemukan.');

    return response()->download($postmanPath, 'TPB_Ormawa_API_v1.postman_collection.json', [
        'Content-Type' => 'application/json',
    ]);
})->name('api.docs.postman');

Route::prefix('informasi')->group(function () {
    Route::get('tracking', [InformasiController::class, 'index'])->name('tracking');
    Route::get('tracking/json', [InformasiController::class, 'json'])->name('tracking.json');
});

Route::middleware(['auth:admin'])->group(function () {
    Route::resource('admin/logout', LogoutController::class);

    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::prefix('pengguna')->group(function () {
        // Route Pengguna Admin
        Route::get('admin', [AdminController::class, 'index'])->name('admin');
        Route::post('admin', [AdminController::class, 'store'])->name('admin.store');
        Route::put('admin/{admin}', [AdminController::class, 'update'])->name('admin.update');
        Route::delete('admin/{admin}', [AdminController::class, 'destroy'])->name('admin.destroy');

        // Route Pengguna Mahasiswa
        Route::get('mahasiswa', [PenggunaController::class, 'index'])->name('mahasiswa');
        Route::post('mahasiswa', [PenggunaController::class, 'store'])->name('mahasiswa.store');
        Route::put('mahasiswa/{mahasiswa}', [PenggunaController::class, 'update'])->name('mahasiswa.update');
        Route::delete('mahasiswa/{mahasiswa}', [PenggunaController::class, 'destroy'])->name('mahasiswa.destroy');
    });

    Route::prefix('kelola-barang')->group(function () {
        // Route Barang
        Route::get('data-barang', [BarangController::class, 'index'])->name('barang');
        Route::post('data-barang', [BarangController::class, 'store'])->name('barang.store');
        Route::put('data-barang/{data_barang}', [BarangController::class, 'update'])->name('barang.update');
        Route::delete('data-barang/{data_barang}', [BarangController::class, 'destroy'])->name('barang.destroy');

        // Route Kategori
        Route::get('data-kategori', [KategoriController::class, 'index'])->name('kategori');
        Route::post('data-kategori', [KategoriController::class, 'store'])->name('kategori.store');
        Route::put('data-kategori/{data_kategori}', [KategoriController::class, 'update'])->name('kategori.update');
        Route::delete('data-kategori/{data_kategori}', [KategoriController::class, 'destroy'])->name('kategori.destroy');

        // Route Satuan
        Route::get('satuan', [SatuanController::class, 'index'])->name('satuan');
        Route::post('satuan', [SatuanController::class, 'store'])->name('satuan.store');
        Route::put('satuan/{satuan}', [SatuanController::class, 'update'])->name('satuan.update');
        Route::delete('satuan/{satuan}', [SatuanController::class, 'destroy'])->name('satuan.destroy');
    });

    Route::get('verifikasi-permohonan', [PermohonanController::class, 'index'])->name('verifikasi-permohonan');
    Route::put('verifikasi-permohonan/{id}', [PermohonanController::class, 'update'])->name('verifikasi-permohonan.update');

    Route::get('verifikasi-pengembalian', [PengembalianControllerAdmin::class, 'index'])->name('verifikasi-pengembalian');
    Route::put('verifikasi-pengembalian/{id}', [PengembalianControllerAdmin::class, 'update'])->name('verifikasi-pengembalian.update');

    Route::get('laporan', [LaporanController::class, 'index'])->name('laporan');
    Route::get('laporan/export-pdf', [LaporanController::class, 'exportPdf'])->name('laporan.export');
});

Route::middleware(['auth:ormawa', 'ormawa.session'])->group(function () {
    Route::resource('logout/ormawa', LogoutController::class);

    Route::get('beranda', [BerandaController::class, 'index'])->name('beranda');
    Route::get('beranda/{nama_barang}', [BerandaController::class, 'show'])->name('beranda.show');
    Route::post('beranda/{nama_barang}/{barangId}', [BerandaController::class, 'store'])->name('beranda.store');

    Route::get('keranjang', [KeranjangController::class, 'index'])->name('keranjang');
    Route::post('keranjang', [KeranjangController::class, 'store'])->name('keranjang.store');
    Route::delete('/keranjang/{id}', [KeranjangController::class, 'destroy'])->name('keranjang.destroy');

    Route::prefix('informasi')->group(function () {
        Route::get('pengembalian', [PengembalianController::class, 'index'])->name('pengembalian');
        Route::post('pengembalian/{id}', [PengembalianController::class, 'store'])->name('pengembalian.store');
    });
    Route::get('riwayat', [RiwayatController::class, 'index'])->name('riwayat');
});
