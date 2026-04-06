@extends('pages.admin.index')
@section('content')
    <div class="p-4 sm:ml-64 mt-5">
        @if (session('success'))
            <script>
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: '{{ session('success') }}',
                });
            </script>
        @endif

        @if (session('error'))
            <script>
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: '{{ session('error') }}',
                });
            </script>
        @endif

        <div class="space-y-4 rounded-lg mt-4">
            <div class="p-4 bg-white rounded-xl shadow-sm border border-gray-100 flex items-center">
                <p class="text-lg font-semibold">Verifikasi Pengembalian</p>
            </div>

            <div class="relative overflow-x-auto bg-white shadow-sm border border-gray-200 sm:rounded-xl">
                <table class="w-full text-sm text-left rtl:text-right text-gray-500">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3">No</th>
                            <th scope="col" class="px-6 py-3">Unit Kerja</th>
                            <th scope="col" class="px-6 py-3">Nama Kegiatan</th>
                            <th scope="col" class="px-6 py-3">Hari atau Tanggal</th>
                            <th scope="col" class="px-6 py-3">Waktu Mulai</th>
                            <th scope="col" class="px-6 py-3">Waktu Selesai</th>
                            <th scope="col" class="px-6 py-3">Status</th>
                            <th scope="col" class="px-6 py-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($dataPengembalian as $mahasiswa_id => $data)
                            <tr class="odd:bg-white even:bg-gray-50 border-b border-gray-200">
                                <td class="px-6 py-4">{{ $loop->iteration }}</td>
                                <td class="px-6 py-4">{{ $data->first()->permohonan->unit_kerja }}</td>
                                <td class="px-6 py-4">{{ $data->first()->permohonan->nama_kegiatan }}</td>
                                <td class="px-6 py-4">
                                    {{ \Carbon\Carbon::parse($data->first()->permohonan->hari_atau_tanggal)->translatedFormat('l, d F Y') }}
                                </td>
                                <td class="px-6 py-4">
                                    {{ \Carbon\Carbon::parse($data->first()->permohonan->waktu_mulai)->format('H:i') }} WIB
                                </td>
                                <td class="px-6 py-4">
                                    {{ \Carbon\Carbon::parse($data->first()->permohonan->waktu_selesai)->format('H:i') }}
                                    WIB
                                </td>
                                <td class="px-6 py-4">
                                    <span
                                        class="ui-badge {{ $data->first()->status_pengembalian == 'Diterima' ? 'ui-badge-success' : ($data->first()->status_pengembalian == 'Ditolak' ? 'ui-badge-danger' : 'ui-badge-warning') }}">
                                        {{ $data->first()->status_pengembalian ?? 'Menunggu' }}
                                    </span>
                                </td>
                                <td class="flex items-center gap-2 px-6 py-4">
                                    <button type="button" data-modal-target="permohonan{{ $mahasiswa_id }}"
                                        data-modal-toggle="permohonan{{ $mahasiswa_id }}" class="ui-btn ui-btn-primary">
                                        <i class="fa-solid fa-eye"></i>
                                    </button>

                                    @include('components.modal.verifikasi-pengembalian', [
                                        'data' => $data,
                                    ])
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="pt-2">
                {{ $dataPengembalian->links('components.pagination.blue') }}
            </div>
        </div>
    </div>
@endsection
