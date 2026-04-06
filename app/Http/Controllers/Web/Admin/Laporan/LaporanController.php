<?php

namespace App\Http\Controllers\Web\Admin\Laporan;

use App\Http\Controllers\Controller;
use App\Models\Pengembalian;
use App\Support\PaginationPerPage;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Pagination\LengthAwarePaginator;

class LaporanController extends Controller
{
    public function index()
    {
        $paginator = Pengembalian::with(['mahasiswa', 'permohonan.barang.kategori', 'permohonan.barang.satuan'])
            ->orderByDesc('id')
            ->paginate(PaginationPerPage::resolve());

        $laporan = new LengthAwarePaginator(
            collect($paginator->items())->groupBy('permohonans_id'),
            $paginator->total(),
            $paginator->perPage(),
            $paginator->currentPage(),
            [
                'path' => request()->url(),
                'query' => request()->query(),
            ]
        );

        return view('pages.admin.laporan.index', compact('laporan'));
    }

    public function exportPdf()
    {
        $laporan = Pengembalian::all()->groupBy('permohonans_id');
        $pdf = Pdf::loadView('pages.admin.laporan.pdf', compact('laporan'))->setPaper('A4', 'portrait');
        return $pdf->download('laporan_pengembalian.pdf');
    }
}
