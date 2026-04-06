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

        <div class="space-y-4 rounded-lg mt-4">
            <div class="p-4 bg-white rounded-xl shadow-sm border border-gray-100 flex justify-between items-center">
                <p class="text-lg font-semibold">Kategori</p>
                <button type="button" data-modal-target="add" data-modal-toggle="add"
                    class="text-white bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-4 py-2 transition-colors">
                    <i class="fa-solid fa-plus"></i>
                </button>
                @include('components.modal.tambah-kategori')
            </div>

            <div class="relative overflow-x-auto bg-white shadow-sm border border-gray-200 sm:rounded-xl">
                <table class="w-full text-sm text-left rtl:text-right text-gray-500">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3">No</th>
                            <th scope="col" class="px-6 py-3">Nama Kategori</th>
                            <th scope="col" class="px-6 py-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($kategori as $data)
                            <tr class="odd:bg-white even:bg-gray-50 border-b border-gray-200">
                                <td class="px-6 py-4">
                                    {{ $loop->iteration }}
                                </td>
                                <td class="px-6 py-4">
                                    {{ $data->nama_kategori }}
                                </td>
                                <td class="px-6 py-4 flex items-center gap-2">
                                    {{-- Button Modal Edit --}}
                                    <button type="button" data-modal-target="edit{{ $data->id }}"
                                        data-modal-toggle="edit{{ $data->id }}"
                                        class="px-2 py-1 bg-yellow-500 rounded-lg text-white cursor-pointer">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </button>
                                    {{-- End Button Modal Edit --}}

                                    {{-- Modal Edit --}}
                                    @include('components.modal.edit-kategori', ['data' => $data])
                                    {{-- End Edit --}}

                                    <form id="delete-form-{{ $data->id }}"
                                        action="{{ route('kategori.destroy', $data->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" onclick="confirmDelete({{ $data->id }})"
                                            class="px-2 py-1 bg-red-500 rounded-lg text-white cursor-pointer">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="pt-2">
                {{ $kategori->links('components.pagination.blue') }}
            </div>
        </div>
    </div>

    <script>
        function confirmDelete(id) {
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Data yang dihapus tidak dapat dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-form-' + id).submit();
                }
            })
        }
    </script>
@endsection
