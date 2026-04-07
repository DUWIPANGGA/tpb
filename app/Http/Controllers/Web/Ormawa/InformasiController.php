<?php

namespace App\Http\Controllers\Web\Ormawa;

use App\Http\Controllers\Controller;
use App\Models\Keranjang;
use App\Models\Permohonan;
use Illuminate\Http\Request;

class InformasiController extends Controller
{
    public function index()
    {
        $dataKeranjang = collect();
        $notifikasiKeranjang = 0;

        if (auth()->check()) {
            $dataKeranjang = Keranjang::where('mahasiswa_id', auth()->id())
                ->with('barang')
                ->get();

            // Hitung jumlah total item di keranjang
            $notifikasiKeranjang = $dataKeranjang->count();
        }

        return view("pages.ormawa.informasi.index", compact('dataKeranjang', 'notifikasiKeranjang'));
    }

    public function json()
    {
        $permohonan = Permohonan::where('mahasiswa_id', auth('ormawa')->id())
            ->with('pengembalian')
            ->get();

        if ($permohonan->isEmpty()) {
            return response()->json([]);
        }

        // Group by date and consolidate units
        $eventsByDate = $permohonan->groupBy(function ($item) {
            return $item->hari_atau_tanggal;
        })->map(function ($itemsOnDate) {
            // For same date, group by unit_kerja
            $unitGrouped = $itemsOnDate->groupBy('unit_kerja');
            $unitList = $unitGrouped->keys()->implode(', ');

            // Get overall status (Ditolak > Menunggu > Disetujui for color priority)
            $status = $itemsOnDate->first()->status;
            $statusPengembalian = $itemsOnDate->first()->pengembalian->status_pengembalian ?? 'Belum dikembalikan';

            // Color logic
            if ($status == 'Disetujui' && $statusPengembalian == 'Diterima') {
                $color = '#0000ff'; // Biru
            } elseif ($status == 'Disetujui') {
                $color = '#008000'; // Hijau
            } elseif ($status == 'Ditolak') {
                $color = '#FF0000'; // Merah
            } else {
                $color = '#808080'; // Abu-abu
            }

            return [
                'title' => $unitList,
                'description' => $unitList,
                'status' => $status,
                'start' => $itemsOnDate->first()->hari_atau_tanggal,
                'color' => $color,
                'status_pengembalian' => $statusPengembalian,
            ];
        });

        return response()->json($eventsByDate->values());
    }
}
