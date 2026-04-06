@extends('pages.ormawa.index')
@section('content')
    <style>
        .image {
            width: 100%;
            height: 300px;
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        .animate-card {
            transform: translateY(50px);
            opacity: 0;
            transition: transform 0.5s cubic-bezier(0.25, 0.8, 0.25, 1), opacity 0.5s;
        }

        .animate-card.in-view {
            transform: translateY(0);
            opacity: 1;
        }
    </style>
    <div class="max-w-screen-xl px-4 py-6 mx-auto">
        @if (session('error'))
            <script>
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: '{{ session('error') }}',
                });
            </script>
        @endif
        <div class="space-y-5">
            <div class="mb-6 text-2xl font-bold text-gray-800">
                Detail Barang
            </div>

            <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-3">
                <div class="rounded-xl border border-gray-100 bg-white p-4 shadow-sm xl:col-span-1">
                    <div class="flex justify-center">
                        @if ($barang->foto)
                            <img src="{{ asset('storage/' . $barang->foto) }}"
                                class="image w-full rounded-lg border border-gray-200 object-contain"
                                alt="{{ $barang->nama_barang }}" />
                        @else
                            <i class="fa-regular fa-image text-gray-300 text-6xl"></i>
                        @endif
                    </div>
                </div>

                <div class="rounded-xl border border-gray-100 bg-white p-4 shadow-sm xl:col-span-1">
                    <div class="space-y-4">
                        <div>
                            <p class="text-2xl font-semibold text-slate-900">{{ $barang->nama_barang }}</p>
                            <p class="mt-1 text-sm text-slate-500">Informasi barang dan stok tersedia.</p>
                        </div>
                        <div class="flex flex-wrap gap-2">
                            <span
                                class="rounded-full border border-blue-100 bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-700">{{ $barang->kategori->nama_kategori }}</span>
                            <span
                                class="rounded-full border border-slate-200 bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700">{{ $barang->satuan->nama_satuan }}</span>
                            <span
                                class="rounded-full border border-emerald-100 bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700">Sisa
                                {{ $barang->stock->stock }}</span>
                        </div>
                    </div>
                </div>

                <div class="rounded-xl border border-gray-100 bg-white p-4 shadow-sm xl:col-span-1">
                    <form
                        action="{{ route('beranda.store', ['nama_barang' => $barang->nama_barang, 'barangId' => $barang->id]) }}"
                        method="POST">
                        @csrf
                        <input type="text" name="barang_id" id="barang_id" value="{{ $barang->id }}" hidden>
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-slate-600">Jumlah Pinjam</p>
                            </div>
                            <div class="flex items-center rounded-xl border border-blue-100 bg-blue-50">
                                <button type="button" class="p-2 text-blue-700" onclick="decrement()" id="btn-minus"
                                    {{ $barang->stock->stock == 0 ? 'disabled' : '' }}>
                                    <i class="fa-solid fa-minus"></i>
                                </button>
                                <input type="number" name="jumlah" id="jumlah"
                                    class="w-16 bg-transparent text-center font-semibold text-slate-900 outline-none"
                                    value="1" min="1" max="{{ $barang->stock->stock }}" readonly
                                    {{ $barang->stock->stock == 0 ? 'disabled' : '' }}>
                                <button type="button" class="p-2 text-blue-700" onclick="increment()" id="btn-plus"
                                    {{ $barang->stock->stock == 0 ? 'disabled' : '' }}>
                                    <i class="fa-solid fa-plus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="mt-6">
                            <button type="submit" id="btn-tambah"
                                class="w-full rounded-lg bg-blue-600 px-4 py-3 text-sm font-semibold text-white transition-colors hover:bg-blue-700"
                                {{ $barang->stock->stock == 0 ? 'disabled' : '' }}>
                                Tambah ke Keranjang
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            let input = document.getElementById("jumlah");
            let btnPlus = document.getElementById("btn-plus");
            let btnMinus = document.getElementById("btn-minus");
            let btnTambah = document.getElementById("btn-tambah");

            let maxValue = parseInt(input.getAttribute("max"));

            if (maxValue === 0) {
                btnPlus.disabled = true;
                btnMinus.disabled = true;
                input.disabled = true;
                btnTambah.disabled = true;
            }

            function increment() {
                let currentValue = parseInt(input.value);
                if (currentValue < maxValue) {
                    input.value = currentValue + 1;
                }
            }

            function decrement() {
                let currentValue = parseInt(input.value);
                if (currentValue > 1) {
                    input.value = currentValue - 1;
                }
            }

            btnPlus.addEventListener("click", increment);
            btnMinus.addEventListener("click", decrement);
        });
    </script>
@endsection
