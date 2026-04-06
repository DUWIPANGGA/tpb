<?php

namespace App\Http\Controllers\Web\Ormawa;

use App\Http\Controllers\Controller;
use App\Models\Keranjang;
use App\Models\Permohonan;

class RiwayatController extends Controller
{
    public function index()
    {
        $dataKeranjang = collect();
        $notifikasiKeranjang = 0;
        $dataPermohonan = collect();

        if (auth('ormawa')->check()) {
            $dataKeranjang = Keranjang::where('mahasiswa_id', auth('ormawa')->id())
                ->with('barang')
                ->get();

            // Hitung jumlah total item di keranjang
            $notifikasiKeranjang = $dataKeranjang->count();

            // Ambil data Permohonan dan Pengembalian berdasarkan mahasiswa_id
            $dataPermohonan = Permohonan::where('mahasiswa_id', auth('ormawa')->id())
                ->with('barang', 'pengembalian')
                ->get();
        }

        return view("pages.ormawa.riwayat.index", compact("dataKeranjang", "notifikasiKeranjang", "dataPermohonan"));
    }
}
