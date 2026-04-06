<?php

namespace App\Http\Controllers\Web\Admin\Pengembalian;

use App\Http\Controllers\Controller;
use App\Models\Pengembalian;
use App\Models\Stock;
use App\Support\PaginationPerPage;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PengembalianController extends Controller
{
    public function index()
    {
        $paginator = Pengembalian::with(['permohonan.barang.kategori', 'permohonan.barang.satuan'])
            ->orderByDesc('id')
            ->paginate(PaginationPerPage::resolve());

        $dataPengembalian = new LengthAwarePaginator(
            collect($paginator->items())->groupBy('permohonans_id'),
            $paginator->total(),
            $paginator->perPage(),
            $paginator->currentPage(),
            [
                'path' => request()->url(),
                'query' => request()->query(),
            ]
        );

        return view('pages.admin.pengembalian.index', compact('dataPengembalian'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'status_pengembalian' => 'required|in:Diterima,Ditolak,Menunggu',
        ]);

        DB::transaction(function () use ($request, $id) {
            $targetPengembalian = Pengembalian::with('permohonan')->findOrFail($id);
            $permohonan = $targetPengembalian->permohonan;

            if (!$permohonan) {
                throw new \RuntimeException('Data permohonan tidak ditemukan untuk pengembalian ini.');
            }

            // Update semua pengembalian pada kegiatan dan mahasiswa yang sama.
            $pengembalianList = Pengembalian::whereHas('permohonan', function ($query) use ($permohonan) {
                $query->where('nama_kegiatan', $permohonan->nama_kegiatan)
                    ->where('mahasiswa_id', $permohonan->mahasiswa_id);
            })->get();

            foreach ($pengembalianList as $item) {
                $item->status_pengembalian = $request->status_pengembalian;
                $item->save();

                // Jika status pengembalian adalah "Diterima", update stok barang
                if ($request->status_pengembalian === 'Diterima') {
                    $permohonan = $item->permohonan;

                    if ($permohonan && $permohonan->barang) { // Pastikan barang ada
                        $barang = $permohonan->barang; // Karena relasi belongsTo(), cukup ambil satu

                        $stock = Stock::where('barang_id', $barang->id)->first();

                        if ($stock) {
                            $stock->stock += $permohonan->jumlah; // Update stok
                            $stock->save();
                        }
                    }
                }
            }
        });

        return redirect()->back()->with('success', 'Semua status pengembalian dan stok barang berhasil diperbarui.');
    }
}
