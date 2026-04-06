# API Mobile Ormawa (Flutter)

Base URL:

- `http://<host-app>/api/v1`

Dokumentasi di browser:

- `http://<host-app>/api/docs`

OpenAPI JSON (untuk Swagger):

- `http://<host-app>/api/docs/openapi.json`

Dokumentasi markdown (fallback):

- `http://<host-app>/api/docs/markdown`

Download Postman Collection:

- `http://<host-app>/api/docs/postman`

Format response:

- Semua endpoint mengembalikan JSON dengan format:
    - `success` (boolean)
    - `message` (string)
    - `data` (object/array/null)
    - `server_time` (ISO-8601)

## Keamanan dan Batasan

- API **hanya untuk akun ormawa**.
- Admin tidak bisa login API mobile.
- Auth menggunakan **Sanctum Bearer Token**.
- Semua endpoint API memakai rate limit: **25 request/menit**.
- Middleware proteksi:
    - `auth:sanctum`
    - `ormawa.api` (cek token ability + model harus Ormawa)
    - `throttle:25,1`
- Single session lintas platform:
    - Jika login di web, sesi mobile lama akan habis.
    - Jika login di mobile, sesi web lama akan habis.
    - API akan mengembalikan `401` dengan `code: SESSION_EXPIRED`.

## Authentication

### 1) Login Ormawa

- `POST /auth/login`
- Header: `Accept: application/json`
- Body JSON:

```json
{
    "name": "Esa",
    "password": "@Poli1234567",
    "device_name": "flutter-android"
}
```

Alternatif body (pakai NIM):

```json
{
    "nim": "1234567",
    "password": "@Poli1234567",
    "device_name": "flutter-android"
}
```

Catatan: endpoint yang sama menerima `name` **atau** `nim` + `password`.

- Success `200`:

```json
{
    "success": true,
    "message": "Login berhasil.",
    "data": {
        "access_token": "<token>",
        "token_type": "Bearer",
        "user": {
            "id": 1,
            "name": "Esa",
            "nim": "1234567",
            "organisasi": "HIMA TI"
        }
    },
    "server_time": "2026-04-06T10:00:00+07:00"
}
```

### 2) Profil Login

- `GET /auth/me`
- Header:
    - `Accept: application/json`
    - `Authorization: Bearer <token>`

### 3) Logout

- `POST /auth/logout`
- Header:
    - `Accept: application/json`
    - `Authorization: Bearer <token>`

## Barang

### 1) List Barang

- `GET /barang?search=laptop&per_page=10`
- Header:
    - `Accept: application/json`
    - `Authorization: Bearer <token>`
- Notes:
    - `per_page` default `10`, max `50`.

### 2) Detail Barang

- `GET /barang/{id}`

## Keranjang

### 1) List Keranjang

- `GET /keranjang?per_page=10`

### 2) Tambah Keranjang

- `POST /keranjang`
- Body JSON:

```json
{
    "barang_id": 12,
    "jumlah": 2
}
```

### 3) Ubah Jumlah Keranjang

- `PUT /keranjang/{id}`
- Body JSON:

```json
{
    "jumlah": 3
}
```

### 4) Hapus Item Keranjang

- `DELETE /keranjang/{id}`

## Permohonan

### 1) List Permohonan (status peminjaman)

- `GET /permohonan?per_page=10`

### 2) Submit Permohonan dari Keranjang

- `POST /permohonan/submit`
- Body JSON:

```json
{
    "unit_kerja": "HIMA TI",
    "nama_kegiatan": "Seminar Proker",
    "hari_atau_tanggal": "2026-04-20",
    "waktu_mulai": "08:00",
    "waktu_selesai": "11:00",
    "phone": "081234567890"
}
```

## Pengembalian

### 1) List Data Pengembalian

- `GET /pengembalian`

### 2) Submit Pengembalian

- `POST /pengembalian/{permohonanId}`
- Body form-data:
    - `bukti_foto` (file: jpg/png/jpeg/webp, max 3 MB)

## Riwayat dan Sinkronisasi Realtime

### 1) Riwayat

- `GET /riwayat?per_page=10`

### 2) Sync Status (untuk polling Flutter)

- `GET /sync/status?since=2026-04-06T10:00:00+07:00`
- Return: data perubahan status terbaru, cocok untuk refresh realtime di mobile.

## Error Umum

- `401 Unauthorized`: token tidak valid / belum login.
- `401 Unauthorized` + `code: SESSION_EXPIRED`: sesi digantikan oleh login di perangkat/platform lain.
- `403 Forbidden`: user bukan ormawa atau token ability tidak cocok.
- `422 Unprocessable Entity`: validasi gagal.
- `429 Too Many Requests`: melebihi 25 request per menit.

## Contoh Header Standar Flutter

```http
Accept: application/json
Authorization: Bearer <token>
```

## Catatan Integrasi Flutter

1. Simpan token di secure storage (misal `flutter_secure_storage`).
2. Gunakan interceptor untuk attach `Authorization` otomatis.
3. Untuk status realtime, panggil endpoint `/sync/status` berkala (polling 10-30 detik, sesuaikan kebutuhan).
4. Saat mendapat `401`, arahkan user login ulang.
5. Saat mendapat `429`, lakukan retry dengan backoff.
