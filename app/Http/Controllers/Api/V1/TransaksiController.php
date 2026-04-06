<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Keranjang;
use App\Models\Pengembalian;
use App\Models\Permohonan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class TransaksiController extends ApiController
{
    public function permohonanIndex(Request $request)
    {
        $user = $request->user();
        $perPage = max(1, min((int) $request->query('per_page', 10), 50));

        $paginator = Permohonan::query()
            ->select([
                'id',
                'unit_kerja',
                'nama_kegiatan',
                'hari_atau_tanggal',
                'waktu_mulai',
                'waktu_selesai',
                'mahasiswa_id',
                'phone',
                'barang_id',
                'status',
                'jumlah',
                'created_at',
                'updated_at',
            ])
            ->where('mahasiswa_id', $user->id)
            ->with([
                'barang:id,nama_barang,foto,kategori_id,satuan_id',
                'barang.kategori:id,nama_kategori',
                'barang.satuan:id,nama_satuan',
                'pengembalian:id,permohonans_id,status_pengembalian,bukti_foto,updated_at',
            ])
            ->orderByDesc('id')
            ->paginate($perPage)
            ->appends($request->query());

        $items = collect($paginator->items())->map(function (Permohonan $item) {
            return [
                'id' => $item->id,
                'nama_kegiatan' => $item->nama_kegiatan,
                'unit_kerja' => $item->unit_kerja,
                'hari_atau_tanggal' => optional($item->hari_atau_tanggal)->toDateString(),
                'waktu_mulai' => $item->waktu_mulai,
                'waktu_selesai' => $item->waktu_selesai,
                'jumlah' => $item->jumlah,
                'status' => $item->status,
                'phone' => $item->phone,
                'barang' => [
                    'id' => $item->barang?->id,
                    'nama_barang' => $item->barang?->nama_barang,
                    'foto_url' => $item->barang?->foto ? asset('storage/' . $item->barang->foto) : null,
                    'kategori' => $item->barang?->kategori,
                    'satuan' => $item->barang?->satuan,
                ],
                'pengembalian' => $item->pengembalian ? [
                    'status_pengembalian' => $item->pengembalian->status_pengembalian,
                    'bukti_foto_url' => $item->pengembalian->bukti_foto ? asset('storage/' . $item->pengembalian->bukti_foto) : null,
                    'updated_at' => optional($item->pengembalian->updated_at)->toIso8601String(),
                ] : null,
                'updated_at' => optional($item->updated_at)->toIso8601String(),
            ];
        })->values();

        return $this->success([
            'items' => $items,
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'per_page' => $paginator->perPage(),
                'last_page' => $paginator->lastPage(),
                'total' => $paginator->total(),
            ],
        ], 'Data permohonan berhasil diambil.');
    }

    public function submitPermohonan(Request $request)
    {
        try {
            $payload = $request->validate([
                'unit_kerja' => ['nullable', 'string', 'max:255'],
                'nama_kegiatan' => ['required', 'string', 'max:255'],
                'hari_atau_tanggal' => ['required', 'date'],
                'waktu_mulai' => ['required', 'date_format:H:i'],
                'waktu_selesai' => ['required', 'date_format:H:i'],
                'phone' => ['required', 'string', 'max:30'],
            ]);
        } catch (ValidationException $e) {
            return $this->error('Validasi gagal.', $e->errors());
        }

        $user = $request->user();

        $keranjangItems = Keranjang::query()
            ->select(['id', 'barang_id', 'mahasiswa_id', 'jumlah'])
            ->where('mahasiswa_id', $user->id)
            ->with('barang:id,nama_barang')
            ->get();

        if ($keranjangItems->isEmpty()) {
            return $this->error('Keranjang kosong.', [], 422);
        }

        DB::beginTransaction();

        try {
            foreach ($keranjangItems as $item) {
                Permohonan::create([
                    'unit_kerja' => $payload['unit_kerja'] ?? $user->organisasi,
                    'nama_kegiatan' => $payload['nama_kegiatan'],
                    'hari_atau_tanggal' => $payload['hari_atau_tanggal'],
                    'waktu_mulai' => $payload['waktu_mulai'],
                    'waktu_selesai' => $payload['waktu_selesai'],
                    'mahasiswa_id' => $user->id,
                    'phone' => $payload['phone'],
                    'barang_id' => $item->barang_id,
                    'jumlah' => $item->jumlah,
                    'status' => 'Menunggu',
                ]);
            }

            Keranjang::query()
                ->where('mahasiswa_id', $user->id)
                ->delete();

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            return $this->error('Gagal memproses permohonan.', ['exception' => $e->getMessage()], 500);
        }

        return $this->success([
            'nama_kegiatan' => $payload['nama_kegiatan'],
            'total_item' => $keranjangItems->count(),
        ], 'Permohonan berhasil dibuat.', 201);
    }

    public function pengembalianIndex(Request $request)
    {
        $user = $request->user();

        $items = Permohonan::query()
            ->select([
                'id',
                'unit_kerja',
                'nama_kegiatan',
                'hari_atau_tanggal',
                'waktu_mulai',
                'waktu_selesai',
                'mahasiswa_id',
                'barang_id',
                'status',
                'jumlah',
                'updated_at',
            ])
            ->where('mahasiswa_id', $user->id)
            ->with([
                'barang:id,nama_barang,foto,kategori_id,satuan_id',
                'barang.kategori:id,nama_kategori',
                'barang.satuan:id,nama_satuan',
                'pengembalian:id,permohonans_id,status_pengembalian,bukti_foto,updated_at',
            ])
            ->orderByDesc('id')
            ->get()
            ->groupBy('nama_kegiatan')
            ->map(function ($group, $kegiatan) {
                $first = $group->first();
                return [
                    'nama_kegiatan' => $kegiatan,
                    'unit_kerja' => $first->unit_kerja,
                    'hari_atau_tanggal' => optional($first->hari_atau_tanggal)->toDateString(),
                    'waktu_mulai' => $first->waktu_mulai,
                    'waktu_selesai' => $first->waktu_selesai,
                    'status' => $first->status,
                    'status_pengembalian' => optional($first->pengembalian)->status_pengembalian,
                    'permohonans' => $group->map(function (Permohonan $item) {
                        return [
                            'id' => $item->id,
                            'jumlah' => $item->jumlah,
                            'status' => $item->status,
                            'barang' => [
                                'id' => $item->barang?->id,
                                'nama_barang' => $item->barang?->nama_barang,
                                'foto_url' => $item->barang?->foto ? asset('storage/' . $item->barang->foto) : null,
                                'kategori' => $item->barang?->kategori,
                                'satuan' => $item->barang?->satuan,
                            ],
                        ];
                    })->values(),
                    'updated_at' => optional($first->updated_at)->toIso8601String(),
                ];
            })
            ->values();

        return $this->success($items, 'Data pengembalian berhasil diambil.');
    }

    public function submitPengembalian(Request $request, int $permohonanId)
    {
        try {
            $payload = $request->validate([
                'bukti_foto' => ['required', 'image', 'mimes:jpeg,png,jpg,webp', 'max:3072'],
            ]);
        } catch (ValidationException $e) {
            return $this->error('Validasi gagal.', $e->errors());
        }

        $user = $request->user();

        $target = Permohonan::query()
            ->select(['id', 'nama_kegiatan', 'mahasiswa_id', 'status'])
            ->where('id', $permohonanId)
            ->where('mahasiswa_id', $user->id)
            ->first();

        if (!$target) {
            return $this->error('Permohonan tidak ditemukan.', [], 404);
        }

        if ($target->status !== 'Disetujui') {
            return $this->error('Permohonan belum disetujui, belum bisa pengembalian.', [], 422);
        }

        $permohonanIds = Permohonan::query()
            ->select(['id'])
            ->where('mahasiswa_id', $user->id)
            ->where('nama_kegiatan', $target->nama_kegiatan)
            ->pluck('id');

        $alreadyReturned = Pengembalian::query()
            ->select(['id'])
            ->whereIn('permohonans_id', $permohonanIds)
            ->exists();

        if ($alreadyReturned) {
            return $this->error('Pengembalian untuk kegiatan ini sudah pernah diajukan.', [], 422);
        }

        $storedPath = $payload['bukti_foto']->store('bukti_pengembalian', 'public');

        DB::beginTransaction();

        try {
            foreach ($permohonanIds as $id) {
                Pengembalian::create([
                    'permohonans_id' => $id,
                    'mahasiswa_id' => $user->id,
                    'bukti_foto' => $storedPath,
                    'status_pengembalian' => 'Menunggu',
                ]);
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            return $this->error('Gagal menyimpan pengembalian.', ['exception' => $e->getMessage()], 500);
        }

        return $this->success([
            'nama_kegiatan' => $target->nama_kegiatan,
            'bukti_foto_url' => asset('storage/' . $storedPath),
        ], 'Pengembalian berhasil diajukan.', 201);
    }

    public function riwayat(Request $request)
    {
        return $this->permohonanIndex($request);
    }

    public function sync(Request $request)
    {
        $user = $request->user();
        $since = $request->query('since');

        $base = Permohonan::query()
            ->select(['id', 'nama_kegiatan', 'status', 'mahasiswa_id', 'updated_at'])
            ->where('mahasiswa_id', $user->id)
            ->with('pengembalian:id,permohonans_id,status_pengembalian,updated_at');

        if ($since) {
            $base->where('updated_at', '>=', $since);
        }

        $changed = $base
            ->orderByDesc('updated_at')
            ->get()
            ->map(function (Permohonan $item) {
                return [
                    'permohonan_id' => $item->id,
                    'nama_kegiatan' => $item->nama_kegiatan,
                    'status' => $item->status,
                    'status_pengembalian' => optional($item->pengembalian)->status_pengembalian,
                    'updated_at' => optional($item->updated_at)->toIso8601String(),
                ];
            })
            ->values();

        return $this->success([
            'items' => $changed,
            'count' => $changed->count(),
        ], 'Sinkronisasi status berhasil.');
    }
}
