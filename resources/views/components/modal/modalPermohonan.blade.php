<div id="form-permohonan" tabindex="-1"
    class="fixed top-0 left-0 right-0 z-50 hidden h-[calc(100%-1rem)] max-h-full w-full items-center justify-center overflow-y-auto overflow-x-hidden p-4 md:inset-0">
    <div class="relative w-full max-w-4xl max-h-full">
        <div class="relative overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-2xl">
            <div class="flex items-center justify-between border-b border-gray-100 bg-slate-50 px-6 py-4">
                <div>
                    <h3 class="text-xl font-semibold text-slate-900">Form Permohonan</h3>
                    <p class="text-sm text-slate-500">Pastikan data kegiatan dan kontak sudah benar.</p>
                </div>
                <button type="button"
                    class="inline-flex h-9 w-9 items-center justify-center rounded-lg text-slate-400 transition-colors hover:bg-slate-100 hover:text-slate-900"
                    data-modal-hide="form-permohonan">
                    <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                    </svg>
                    <span class="sr-only">Close modal</span>
                </button>
            </div>
            <div class="p-6 space-y-5">
                <form action="{{ route('keranjang.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div>
                            <label for="unit_kerja" class="mb-2 block text-sm font-medium text-slate-700">
                                Unit Kerja
                            </label>
                            <div class="relative">
                                <div
                                    class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                                    <i class="fa-solid fa-building"></i>
                                </div>
                                <input type="text" id="unit_kerja" name="unit_kerja"
                                    value="{{ auth('ormawa')->user()->organisasi ?? '' }}" readonly
                                    placeholder="Unit kerja otomatis terisi"
                                    class="w-full rounded-lg border border-gray-300 bg-slate-50 py-2.5 pl-10 pr-4 text-sm text-slate-700 focus:border-blue-600 focus:ring-2 focus:ring-blue-100 focus:outline-none" />
                            </div>
                        </div>
                        <div>
                            <label for="nama_kegiatan" class="mb-2 block text-sm font-medium text-slate-700">
                                Nama Kegiatan
                            </label>
                            <div class="relative">
                                <div
                                    class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                                    <i class="fa-solid fa-clipboard-list"></i>
                                </div>
                                <input type="text" id="nama_kegiatan" name="nama_kegiatan"
                                    placeholder="Contoh: Seminar Proker"
                                    class="w-full rounded-lg border border-gray-300 bg-white py-2.5 pl-10 pr-4 text-sm text-slate-700 focus:border-blue-600 focus:ring-2 focus:ring-blue-100 focus:outline-none" />
                            </div>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                        <div>
                            <label for="hari_atau_tanggal" class="mb-2 block text-sm font-medium text-slate-700">
                                Hari atau Tanggal
                            </label>
                            <div class="relative">
                                <div
                                    class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                                    <i class="fa-solid fa-calendar-days"></i>
                                </div>
                                <input type="date" id="hari_atau_tanggal" name="hari_atau_tanggal"
                                    class="w-full rounded-lg border border-gray-300 bg-white py-2.5 pl-10 pr-4 text-sm text-slate-700 focus:border-blue-600 focus:ring-2 focus:ring-blue-100 focus:outline-none" />
                            </div>
                        </div>
                        <div>
                            <label for="waktu_mulai" class="mb-2 block text-sm font-medium text-slate-700">
                                Waktu Mulai
                            </label>
                            <div class="relative">
                                <div
                                    class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                                    <i class="fa-regular fa-clock"></i>
                                </div>
                                <input type="time" id="waktu_mulai" name="waktu_mulai" placeholder="Pilih jam mulai"
                                    class="w-full rounded-lg border border-gray-300 bg-white py-2.5 pl-10 pr-4 text-sm text-slate-700 focus:border-blue-600 focus:ring-2 focus:ring-blue-100 focus:outline-none" />
                            </div>
                        </div>
                        <div>
                            <label for="waktu_selesai" class="mb-2 block text-sm font-medium text-slate-700">
                                Waktu Selesai
                            </label>
                            <div class="relative">
                                <div
                                    class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                                    <i class="fa-regular fa-clock"></i>
                                </div>
                                <input type="time" id="waktu_selesai" name="waktu_selesai"
                                    placeholder="Pilih jam selesai"
                                    class="w-full rounded-lg border border-gray-300 bg-white py-2.5 pl-10 pr-4 text-sm text-slate-700 focus:border-blue-600 focus:ring-2 focus:ring-blue-100 focus:outline-none" />
                            </div>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div>
                            <label for="penanggung_jawab" class="mb-2 block text-sm font-medium text-slate-700">
                                Penanggung Jawab
                            </label>
                            <div class="relative">
                                <div
                                    class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                                    <i class="fa-solid fa-user"></i>
                                </div>
                                <input type="text" id="penanggung_jawab" name="penanggung_jawab"
                                    value="{{ auth('ormawa')->user()->name ?? '' }}" readonly
                                    placeholder="Nama penanggung jawab"
                                    class="w-full rounded-lg border border-gray-300 bg-slate-50 py-2.5 pl-10 pr-4 text-sm text-slate-700 focus:border-blue-600 focus:ring-2 focus:ring-blue-100 focus:outline-none" />
                            </div>
                        </div>
                        <div>
                            <label for="phone" class="mb-2 block text-sm font-medium text-slate-700">
                                Nomor Telepon / WhatsApp
                            </label>
                            <div class="relative">
                                <div
                                    class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                                    <i class="fa-solid fa-phone"></i>
                                </div>
                                <input type="text" id="phone" name="phone" placeholder="Contoh: 08xxxxxxxxxx"
                                    class="w-full rounded-lg border border-gray-300 bg-white py-2.5 pl-10 pr-4 text-sm text-slate-700 focus:border-blue-600 focus:ring-2 focus:ring-blue-100 focus:outline-none" />
                            </div>
                        </div>
                    </div>
                    <button type="submit"
                        class="mt-1 w-full rounded-lg bg-blue-600 px-5 py-3 text-sm font-semibold text-white transition-colors hover:bg-blue-700">
                        Simpan
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
