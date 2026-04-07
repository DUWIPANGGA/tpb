@extends('pages.ormawa.index')
@section('content')
    <div class="max-w-screen-xl p-4 mx-auto space-y-3">
        <div class="mb-6 text-2xl font-bold text-gray-800">
            Riwayat
        </div>

        <div class="relative overflow-x-auto bg-white shadow-sm border border-gray-200 sm:rounded-xl">
            <table class="w-full text-sm text-left rtl:text-right text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3">No</th>
                        <th scope="col" class="px-6 py-3">Nama Mahasiswa</th>
                        <th scope="col" class="px-6 py-3">Unit kerja</th>
                        <th scope="col" class="px-6 py-3">Nama Kegiatan</th>
                        <th scope="col" class="px-6 py-3">Nama Barang</th>
                        <th scope="col" class="px-6 py-3">Waktu Mulai</th>
                        <th scope="col" class="px-6 py-3">Waktu Selesai</th>
                        <th scope="col" class="px-6 py-3">Status Permohonan</th>
                        <th scope="col" class="px-6 py-3">Status Pengembalian</th>
                        <th scope="col" class="px-6 py-3">Detail</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($dataPermohonan as $index => $item)
                        <tr class="odd:bg-white even:bg-gray-50 border-b border-gray-200">
                            <td class="px-6 py-4">{{ $dataPermohonan->firstItem() + $loop->index }}</td>
                            <td class="px-6 py-4">
                                {{ optional(optional($item->pengembalian)->mahasiswa)->name ?? auth('ormawa')->user()->name }}
                            </td>
                            <td class="px-6 py-4">{{ $item->unit_kerja }}</td>
                            <td class="px-6 py-4">{{ $item->nama_kegiatan }}</td>
                            <td class="px-6 py-4">{{ $item->barang->nama_barang ?? 'Tidak tersedia' }}</td>
                            <td class="px-6 py-4">{{ \Carbon\Carbon::parse($item->waktu_mulai)->format('H:i') }} WIB</td>
                            <td class="px-6 py-4">{{ \Carbon\Carbon::parse($item->waktu_selesai)->format('H:i') }} WIB</td>
                            <td class="px-6 py-4">
                                <span
                                    class="ui-badge
                                    {{ $item->status == 'Disetujui'
                                        ? 'ui-badge-success'
                                        : ($item->status == 'Ditolak'
                                            ? 'ui-badge-danger'
                                            : 'ui-badge-warning') }}">
                                    {{ $item->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span
                                    class="ui-badge
                                    {{ optional($item->pengembalian)->status_pengembalian == 'Diterima'
                                        ? 'ui-badge-success'
                                        : (optional($item->pengembalian)->status_pengembalian == 'Menunggu'
                                            ? 'ui-badge-warning'
                                            : 'ui-badge-danger') }}">
                                    {{ optional($item->pengembalian)->status_pengembalian ?? 'Belum dikembalikan' }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <button type="button" data-modal-target="riwayat-detail-{{ $item->id }}"
                                    data-modal-toggle="riwayat-detail-{{ $item->id }}"
                                    class="ui-btn ui-btn-primary !h-8 !px-3 !text-xs">
                                    <i class="fa-solid fa-circle-info mr-1.5"></i>Detail
                                </button>

                                <div id="riwayat-detail-{{ $item->id }}" tabindex="-1" aria-hidden="true"
                                    class="fixed left-0 right-0 top-0 z-50 hidden h-[calc(100%-1rem)] max-h-full w-full items-center justify-center overflow-y-auto overflow-x-hidden p-4 md:inset-0">
                                    <div class="relative max-h-full w-full max-w-2xl">
                                        <div class="relative rounded-xl bg-white shadow">
                                            <div class="flex items-center justify-between border-b px-5 py-4">
                                                <h3 class="text-lg font-semibold text-slate-900">Detail Riwayat</h3>
                                                <button type="button"
                                                    class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 transition-colors hover:bg-slate-100 hover:text-slate-900"
                                                    data-modal-hide="riwayat-detail-{{ $item->id }}">
                                                    <i class="fa-solid fa-xmark"></i>
                                                </button>
                                            </div>
                                            <div class="space-y-4 p-5 text-sm text-slate-700">
                                                <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
                                                    <p><span class="font-semibold text-slate-900">Nama Kegiatan:</span>
                                                        {{ $item->nama_kegiatan }}</p>
                                                    <p><span class="font-semibold text-slate-900">Unit Kerja:</span>
                                                        {{ $item->unit_kerja }}</p>
                                                    <p><span class="font-semibold text-slate-900">Barang:</span>
                                                        {{ $item->barang->nama_barang ?? '-' }}</p>
                                                    <p><span class="font-semibold text-slate-900">Jumlah:</span>
                                                        {{ $item->jumlah }}</p>
                                                    <p><span class="font-semibold text-slate-900">Waktu Mulai:</span>
                                                        {{ \Carbon\Carbon::parse($item->waktu_mulai)->format('H:i') }} WIB
                                                    </p>
                                                    <p><span class="font-semibold text-slate-900">Waktu Selesai:</span>
                                                        {{ \Carbon\Carbon::parse($item->waktu_selesai)->format('H:i') }}
                                                        WIB</p>
                                                </div>

                                                @if (optional($item->pengembalian)->bukti_foto)
                                                    <div>
                                                        <p class="mb-2 font-semibold text-slate-900">Bukti Pengembalian</p>
                                                        <img src="{{ asset('storage/' . $item->pengembalian->bukti_foto) }}"
                                                            alt="Bukti Pengembalian"
                                                            class="h-52 w-full rounded-lg border border-gray-200 object-cover">
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="px-6 py-4 text-center text-gray-500">
                                Tidak ada riwayat permohonan dan pengembalian.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $dataPermohonan->links('components.pagination.blue') }}
        </div>
    </div>
@endsection
