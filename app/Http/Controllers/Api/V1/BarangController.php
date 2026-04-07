<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Barang;
use Illuminate\Http\Request;

class BarangController extends ApiController
{
    public function index(Request $request)
    {
        $search = trim((string) $request->query('search', ''));
        $perPage = max(1, min((int) $request->query('per_page', 10), 50));

        $query = Barang::query()
            ->select(['id', 'nama_barang', 'foto', 'kategori_id', 'satuan_id', 'created_at', 'updated_at'])
            ->with([
                'kategori:id,nama_kategori',
                'satuan:id,nama_satuan',
                'stock:id,barang_id,stock,status_barang',
            ]);

        if ($search !== '') {
            $normalizedSearch = strtolower($search);
            $tokens = collect(preg_split('/\s+/', $normalizedSearch) ?: [])
                ->filter(fn($token) => $token !== '')
                ->take(3)
                ->values();

            $query->where(function ($builder) use ($normalizedSearch, $tokens) {
                $builder->whereRaw('LOWER(nama_barang) LIKE ?', [$normalizedSearch . '%'])
                    ->orWhereRaw('LOWER(nama_barang) LIKE ?', ['% ' . $normalizedSearch . '%'])
                    ->orWhereRaw('LOWER(nama_barang) LIKE ?', ['%' . $normalizedSearch . '%']);

                foreach ($tokens as $token) {
                    $builder->orWhereRaw('LOWER(nama_barang) LIKE ?', [$token . '%'])
                        ->orWhereRaw('LOWER(nama_barang) LIKE ?', ['% ' . $token . '%']);
                }
            });

            $query->orderByRaw('CASE WHEN LOWER(nama_barang) LIKE ? THEN 0 ELSE 1 END', [$normalizedSearch . '%'])
                ->orderByRaw('CASE WHEN LOWER(nama_barang) LIKE ? THEN 0 ELSE 1 END', ['% ' . $normalizedSearch . '%']);
        }

        $paginator = $query
            ->orderByDesc('id')
            ->paginate($perPage)
            ->appends($request->query());

        $items = collect($paginator->items())->map(function (Barang $barang) use ($request) {
            return [
                'id' => $barang->id,
                'nama_barang' => $barang->nama_barang,
                'foto' => $barang->foto,
                'foto_url' => $this->storageUrl($request, $barang->foto),
                'kategori' => $barang->kategori,
                'satuan' => $barang->satuan,
                'stock' => $barang->stock,
                'updated_at' => optional($barang->updated_at)->toIso8601String(),
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
        ], 'Data barang berhasil diambil.');
    }

    public function show(Request $request, int $id)
    {
        $barang = Barang::query()
            ->select(['id', 'nama_barang', 'foto', 'kategori_id', 'satuan_id', 'created_at', 'updated_at'])
            ->with([
                'kategori:id,nama_kategori',
                'satuan:id,nama_satuan',
                'stock:id,barang_id,stock,status_barang',
            ])
            ->where('id', $id)
            ->first();

        if (!$barang) {
            return $this->error('Barang tidak ditemukan.', [], 404);
        }

        return $this->success([
            'id' => $barang->id,
            'nama_barang' => $barang->nama_barang,
            'foto' => $barang->foto,
            'foto_url' => $this->storageUrl($request, $barang->foto),
            'kategori' => $barang->kategori,
            'satuan' => $barang->satuan,
            'stock' => $barang->stock,
            'created_at' => optional($barang->created_at)->toIso8601String(),
            'updated_at' => optional($barang->updated_at)->toIso8601String(),
        ], 'Detail barang berhasil diambil.');
    }

    private function storageUrl(Request $request, ?string $path): ?string
    {
        if (!$path) {
            return null;
        }

        return rtrim($request->getSchemeAndHttpHost(), '/') . '/storage/' . ltrim($path, '/');
    }
}
