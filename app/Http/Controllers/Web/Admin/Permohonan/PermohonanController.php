<?php

namespace App\Http\Controllers\Web\Admin\Permohonan;

use App\Http\Controllers\Controller;
use App\Models\Permohonan;
use App\Models\Stock;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;

class PermohonanController extends Controller
{
    public function index()
    {
        $kegiatanPaginator = Permohonan::selectRaw('nama_kegiatan, MAX(id) as latest_id')
            ->groupBy('nama_kegiatan')
            ->orderByDesc('latest_id')
            ->paginate(10);

        $kegiatanNames = collect($kegiatanPaginator->items())
            ->pluck('nama_kegiatan')
            ->values();

        $groupedPermohonan = Permohonan::with(['mahasiswa', 'barang.kategori', 'barang.satuan'])
            ->whereIn('nama_kegiatan', $kegiatanNames)
            ->orderByDesc('id')
            ->get()
            ->groupBy('nama_kegiatan');

        $orderedGroups = $kegiatanNames->mapWithKeys(function ($namaKegiatan) use ($groupedPermohonan) {
            return [$namaKegiatan => $groupedPermohonan->get($namaKegiatan, collect())];
        });

        $dataPermohonan = new LengthAwarePaginator(
            $orderedGroups,
            $kegiatanPaginator->total(),
            $kegiatanPaginator->perPage(),
            $kegiatanPaginator->currentPage(),
            [
                'path' => request()->url(),
                'query' => request()->query(),
            ]
        );

        return view("pages.admin.permohonan.index", compact('dataPermohonan'));
    }

    public function update(Request $request, $permohonanId)
    {
        // Ambil permohonan berdasarkan ID
        $permohonan = Permohonan::findOrFail($permohonanId);

        // Cari semua permohonan dengan nama_kegiatan yang sama
        $permohonanList = Permohonan::where('nama_kegiatan', $permohonan->nama_kegiatan)->get();

        // Ambil status dari request
        $status = $request->input('status');

        if ($status == 'Disetujui') {
            // Ambil semua barang_id yang terkait dengan mahasiswa ini
            $barangIds = $permohonanList->pluck('barang_id')->unique();

            foreach ($barangIds as $barangId) {
                // Cari stok barang
                $stock = Stock::where('barang_id', $barangId)->first();

                // Hitung total jumlah peminjaman untuk barang ini
                $totalDipinjam = $permohonanList->where('barang_id', $barangId)->sum('jumlah');

                if ($stock && $stock->stock >= $totalDipinjam) {
                    // Kurangi stok berdasarkan jumlah total peminjaman
                    $stock->stock -= $totalDipinjam;
                    $stock->save();
                } else {
                    return redirect()->back()->with('error', 'Stok tidak mencukupi untuk barang yang dipinjam.');
                }
            }
        }

        // Update semua permohonan mahasiswa tersebut
        foreach ($permohonanList as $p) {
            $p->status = $status;
            $p->save();
        }

        return redirect()->back()->with('success', 'Berhasil memperbarui status permohonan.');
    }
}
