@extends('pages.ormawa.index')
@section('content')
    <style>
        .image {
            width: 100%;
            height: 300px;
            object-fit: cover;
            transition: transform 0.3s ease;
        }
    </style>
    <div class="max-w-screen-xl px-4 py-6 mx-auto space-y-3">
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

        <div class="mb-6 text-2xl font-bold text-gray-800">
            Pengembalian
        </div>

        <div class="relative overflow-x-auto bg-white shadow-sm border border-gray-200 sm:rounded-xl">
            <table class="w-full text-sm text-left rtl:text-right text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3">No</th>
                        <th scope="col" class="px-6 py-3">Nama Mahasiswa</th>
                        <th scope="col" class="px-6 py-3">Unit Kerja</th>
                        <th scope="col" class="px-6 py-3">Nama Kegiatan</th>
                        <th scope="col" class="px-6 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($dataPermohonan as $mahasiswa_id => $data)
                        <tr class="odd:bg-white even:bg-gray-50 border-b border-gray-200">
                            <td class="px-6 py-4">
                                {{ $loop->iteration }}
                            </td>
                            <td class="px-6 py-4">
                                {{ $data->first()->mahasiswa->name }}
                            </td>
                            <td class="px-6 py-4">{{ $data->first()->unit_kerja }}</td>
                            <td class="px-6 py-4">{{ $data->first()->nama_kegiatan }}</td>
                            <td class="flex items-center gap-2 px-6 py-4">
                                <button type="button" data-modal-target="permohonan{{ $mahasiswa_id }}"
                                    data-modal-toggle="permohonan{{ $mahasiswa_id }}"
                                    class="flex items-center rounded-lg bg-blue-600 px-3 py-2 text-sm font-medium text-white transition-colors hover:bg-blue-700">
                                    <i class="fa-solid fa-eye"></i>
                                </button>

                                @include('components.modal.pengembalian', [
                                    'data' => $data,
                                ])
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
