@extends('pages.ormawa.index')
@section('content')
    @if (session('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: '{{ session('success') }}',
            });
        </script>
    @endif
    <div class="max-w-screen-xl px-4 py-6 mx-auto">
        <div class="space-y-5">
            <div class="mb-6 text-2xl font-bold text-gray-800">
                Keranjang
            </div>

            <div class="max-w-screen-xl p-4 mx-auto bg-white rounded-xl border border-gray-100 shadow-sm">
                @forelse ($keranjang as $item)
                    <div
                        class="grid grid-cols-1 gap-4 rounded-xl border border-gray-100 bg-slate-50 p-4 shadow-sm sm:grid-cols-2 xl:grid-cols-3">
                        <div class="flex items-center gap-3 sm:col-span-2 xl:col-span-2">
                            @if ($item->barang)
                                @if ($item->barang->foto)
                                    <img src="{{ asset('storage/' . $item->barang->foto) }}"
                                        class="h-20 w-20 rounded-lg border border-gray-200 object-cover"
                                        alt="{{ $item->barang->nama_barang }}">
                                @else
                                    <div
                                        class="flex h-20 w-20 items-center justify-center rounded-lg bg-white border border-gray-200">
                                        <i class="fa-regular fa-image text-gray-300 text-3xl"></i>
                                    </div>
                                @endif
                                <div>
                                    <p class="text-xs font-medium text-blue-700">
                                        {{ $item->barang->kategori->nama_kategori }}</p>
                                    <p class="mt-1 text-sm font-semibold text-slate-900">{{ $item->barang->nama_barang }}
                                    </p>
                                    <p class="text-sm text-slate-500">Jumlah pinjam: {{ $item->jumlah }}</p>
                                </div>
                            @else
                                <p class="text-sm text-slate-500">Barang tidak tersedia</p>
                            @endif
                        </div>
                        <div class="flex items-center justify-end">
                            <form action="{{ route('keranjang.destroy', $item->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="inline-flex items-center rounded-lg border border-red-100 bg-red-50 px-3 py-2 text-sm font-medium text-red-600 transition-colors hover:bg-red-50 hover:border-red-200">
                                    <i class="fa-solid fa-trash-can mr-2"></i>Hapus
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <p class="text-center text-gray-500">Keranjang kosong</p>
                @endforelse

                @if ($keranjang->isNotEmpty())
                    <hr class="my-5" />
                    <button data-modal-target="form-permohonan" data-modal-toggle="form-permohonan"
                        class="w-full rounded-lg bg-blue-600 px-4 py-3 text-sm font-semibold text-white transition-colors hover:bg-blue-700"
                        type="button">
                        Form Permohonan
                    </button>
                    @include('components.modal.modalPermohonan')
                @endif

                <div class="pt-4">
                    {{ $keranjang->links('components.pagination.blue') }}
                </div>
            </div>
        </div>
    </div>
@endsection
