<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Barang;
use App\Models\Keranjang;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class KeranjangController extends ApiController
{
    public function index(Request $request)
    {
        $user = $request->user();
        $perPage = max(1, min((int) $request->query('per_page', 10), 50));

        $paginator = Keranjang::query()
            ->select(['id', 'barang_id', 'mahasiswa_id', 'jumlah', 'created_at', 'updated_at'])
            ->where('mahasiswa_id', $user->id)
            ->with([
                'barang:id,nama_barang,foto,kategori_id,satuan_id',
                'barang.kategori:id,nama_kategori',
                'barang.satuan:id,nama_satuan',
                'barang.stock:id,barang_id,stock,status_barang',
            ])
            ->orderByDesc('id')
            ->paginate($perPage)
            ->appends($request->query());

        $items = collect($paginator->items())->map(function (Keranjang $keranjang) {
            return [
                'id' => $keranjang->id,
                'jumlah' => $keranjang->jumlah,
                'barang' => [
                    'id' => $keranjang->barang?->id,
                    'nama_barang' => $keranjang->barang?->nama_barang,
                    'foto_url' => $keranjang->barang?->foto ? asset('storage/' . $keranjang->barang->foto) : null,
                    'kategori' => $keranjang->barang?->kategori,
                    'satuan' => $keranjang->barang?->satuan,
                    'stock' => $keranjang->barang?->stock,
                ],
                'updated_at' => optional($keranjang->updated_at)->toIso8601String(),
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
        ], 'Data keranjang berhasil diambil.');
    }

    public function store(Request $request)
    {
        try {
            $payload = $request->validate([
                'barang_id' => ['required', 'integer', 'exists:barangs,id'],
                'jumlah' => ['required', 'integer', 'min:1'],
            ]);
        } catch (ValidationException $e) {
            return $this->error('Validasi gagal.', $e->errors());
        }

        $user = $request->user();

        $barang = Barang::query()
            ->select(['id', 'nama_barang'])
            ->with('stock:id,barang_id,stock')
            ->where('id', $payload['barang_id'])
            ->first();

        if (!$barang || !$barang->stock) {
            return $this->error('Data barang atau stok tidak tersedia.', [], 404);
        }

        $existing = Keranjang::query()
            ->select(['id', 'jumlah'])
            ->where('mahasiswa_id', $user->id)
            ->where('barang_id', $payload['barang_id'])
            ->first();

        $newQty = $payload['jumlah'] + (int) ($existing?->jumlah ?? 0);
        if ($newQty > (int) $barang->stock->stock) {
            return $this->error('Jumlah melebihi stok yang tersedia.', [], 422);
        }

        if ($existing) {
            $existing->update(['jumlah' => $newQty]);
            return $this->success([
                'keranjang_id' => $existing->id,
                'jumlah' => $newQty,
            ], 'Jumlah barang di keranjang diperbarui.');
        }

        $item = Keranjang::create([
            'barang_id' => $payload['barang_id'],
            'mahasiswa_id' => $user->id,
            'jumlah' => $payload['jumlah'],
        ]);

        return $this->success([
            'keranjang_id' => $item->id,
            'jumlah' => $item->jumlah,
        ], 'Barang berhasil ditambahkan ke keranjang.', 201);
    }

    public function update(Request $request, int $id)
    {
        try {
            $payload = $request->validate([
                'jumlah' => ['required', 'integer', 'min:1'],
            ]);
        } catch (ValidationException $e) {
            return $this->error('Validasi gagal.', $e->errors());
        }

        $user = $request->user();

        $item = Keranjang::query()
            ->select(['id', 'barang_id', 'mahasiswa_id', 'jumlah'])
            ->where('id', $id)
            ->where('mahasiswa_id', $user->id)
            ->first();

        if (!$item) {
            return $this->error('Item keranjang tidak ditemukan.', [], 404);
        }

        $barang = Barang::query()
            ->select(['id'])
            ->with('stock:id,barang_id,stock')
            ->where('id', $item->barang_id)
            ->first();

        if (!$barang || !$barang->stock) {
            return $this->error('Stok barang tidak ditemukan.', [], 404);
        }

        if ($payload['jumlah'] > (int) $barang->stock->stock) {
            return $this->error('Jumlah melebihi stok yang tersedia.', [], 422);
        }

        $item->update(['jumlah' => $payload['jumlah']]);

        return $this->success([
            'keranjang_id' => $item->id,
            'jumlah' => $item->jumlah,
        ], 'Keranjang berhasil diperbarui.');
    }

    public function destroy(Request $request, int $id)
    {
        $user = $request->user();

        $item = Keranjang::query()
            ->select(['id', 'mahasiswa_id'])
            ->where('id', $id)
            ->where('mahasiswa_id', $user->id)
            ->first();

        if (!$item) {
            return $this->error('Item keranjang tidak ditemukan.', [], 404);
        }

        $item->delete();

        return $this->success(null, 'Item keranjang berhasil dihapus.');
    }
}
