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
        @if (session('success'))
            <script>
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: '{{ session('success') }}',
                });
            </script>
        @endif
        <div class="space-y-5">
            <div class="mb-6 text-2xl font-bold text-gray-800">
                Beranda
            </div>

            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 md:p-5">
                <form action="{{ route('beranda') }}" method="get" class="flex flex-col gap-3 sm:flex-row">
                    <input type="text" name="search" id="search" placeholder="Ketik nama barang disini..."
                        class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-blue-600 focus:ring-2 focus:ring-blue-100 focus:outline-none sm:max-w-md"
                        value="{{ request()->query('search') }}">
                    <button type="submit"
                        class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-medium text-white transition-colors hover:bg-blue-700">
                        <i class="fa-solid fa-magnifying-glass mr-2"></i>Cari
                    </button>
                </form>


                @if ($dataBarang->isEmpty())
                    <p class="mt-6 text-center text-gray-500">Tidak ada barang yang tersedia saat ini.</p>
                @else
                    <div id="card-section" class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-3 animate-card mt-4">
                        @foreach ($dataBarang as $data)
                            <div
                                class="relative w-full overflow-hidden rounded-xl border border-gray-100 bg-white shadow-sm transition hover:shadow-md">
                                <div class="flex justify-center w-full bg-slate-50 p-4">
                                    @if ($data->foto)
                                        <img src="{{ asset('storage/' . $data->foto) }}"
                                            class="h-48 w-full object-contain zoom-image" alt="{{ $data->nama_barang }}" />
                                    @else
                                        <i class="fa-regular fa-image text-gray-400 text-6xl"></i>
                                    @endif
                                </div>
                                <div class="space-y-3 p-4">
                                    <div class="flex flex-wrap gap-2">
                                        <span
                                            class="bg-blue-50 text-blue-700 text-xs font-medium px-2.5 py-1 rounded-full border border-blue-100">
                                            {{ $data->kategori->nama_kategori ?? 'Tidak ada kategori' }}
                                        </span>
                                        <span
                                            class="bg-slate-100 text-slate-700 text-xs font-medium px-2.5 py-1 rounded-full border border-slate-200">
                                            Sisa {{ $data->stock->stock ?? 'Tidak ada stock' }}
                                        </span>
                                    </div>
                                    <div>
                                        <p class="text-base font-semibold text-slate-900">
                                            {{ Str::limit($data->nama_barang, 50) }}</p>
                                    </div>
                                </div>

                                @if ($data->stock->stock > 0)
                                    <!-- Jika stok masih ada, tetap bisa diklik -->
                                    <a href="{{ route('beranda.show', ['nama_barang' => $data->nama_barang]) }}"
                                        class="absolute inset-0"></a>
                                @else
                                    <!-- Jika stok habis, tampilkan overlay dengan teks "Stok Habis" -->
                                    <div
                                        class="absolute inset-0 flex items-center justify-center rounded-xl bg-white/75 backdrop-blur-sm">
                                        <p
                                            class="rounded-full bg-red-50 px-4 py-2 text-sm font-semibold text-red-700 ring-1 ring-red-100">
                                            Stok Habis</p>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif

                <div class="mt-4">
                    {{ $dataBarang->links('components.pagination.blue') }}
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const cards = document.querySelectorAll('.animate-card');

            const observerOptions = {
                threshold: 0.1,
            };

            const observer = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('in-view');
                    } else {
                        entry.target.classList.remove('in-view');
                    }
                });
            }, observerOptions);

            cards.forEach(card => {
                observer.observe(card);
            });
        });
    </script>
@endsection
