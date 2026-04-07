<div id="permohonan{{ $mahasiswa_id }}" tabindex="-1" aria-hidden="true"
    class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
    <div class="relative p-4 w-full max-w-xl max-h-full">
        <div class="relative bg-white rounded-lg shadow-sm">
            <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t border-gray-200">
                <h3 class="text-xl font-semibold text-gray-900">
                    Detail Pengembalian
                </h3>
                <button type="button"
                    class="text-gray-400 cursor-pointer bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center"
                    data-modal-hide="permohonan{{ $mahasiswa_id }}">
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                    </svg>
                    <span class="sr-only">Close modal</span>
                </button>
            </div>
            <div class="p-4 md:p-5">
                @php
                    $firstItem = $data->first();
                    $isApproved = $firstItem->status === 'Disetujui';
                    $isAlreadyReturned = optional($firstItem->pengembalian)->status_pengembalian === 'Diterima';
                    $isRejected =
                        $firstItem->status === 'Ditolak' ||
                        optional($firstItem->pengembalian)->status_pengembalian === 'Ditolak';
                    $canSubmit = $isApproved && !$isAlreadyReturned && !$isRejected;
                @endphp

                @if (!$isApproved)
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-4">
                        <p class="text-sm text-yellow-800">
                            <strong>⚠️ Perhatian:</strong> Permohonan belum disetujui. Anda hanya dapat mengembalikan
                            barang setelah permohonan disetujui.
                        </p>
                    </div>
                @elseif ($isAlreadyReturned)
                    <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-4">
                        <p class="text-sm text-green-800">
                            <strong>✓ Informasi:</strong> Barang telah dikembalikan dan diterima.
                        </p>
                    </div>
                @elseif ($isRejected)
                    <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-4">
                        <p class="text-sm text-red-800">
                            <strong>✗ Informasi:</strong> Permohonan ditolak. Tidak dapat melakukan pengembalian untuk
                            permohonan yang ditolak.
                        </p>
                    </div>
                @endif

                <form action="{{ route('pengembalian.store', $data->first()->id) }}" method="POST"
                    enctype="multipart/form-data" {{ !$canSubmit ? 'id=disabledForm' : '' }}>
                    @csrf
                    <div class="max-w-screen-xl mx-auto">
                        <div class="space-y-3">
                            <div class="relative overflow-x-auto bg-gray-100 rounded-lg p-2">
                                <!-- Informasi Barang -->
                                @foreach ($data as $item)
                                    <div class="flex items-center gap-1">
                                        @if ($item->barang->foto)
                                            <img src="{{ asset('storage/' . $item->barang->foto) }}"
                                                class="h-12 w-12 rounded-lg border border-gray-200 object-cover"
                                                alt="{{ $item->barang->nama_barang }}">
                                        @else
                                            <div
                                                class="flex h-12 w-12 items-center justify-center rounded-lg border border-dashed border-gray-300 bg-gray-50 text-gray-300">
                                                <i class="fa-regular fa-image"></i>
                                            </div>
                                        @endif
                                        <div>
                                            <div class="flex gap-1">
                                                <p class="text-sm text-gray-900 font-semibold ms-4">
                                                    {{ $item->barang->nama_barang }}
                                                </p>
                                                <p
                                                    class="bg-blue-50 text-blue-700 text-xs font-medium me-2 px-2.5 py-0.5 rounded-full border border-blue-100">
                                                    Jumlah Pinjam {{ $data->first()->jumlah }}
                                                </p>
                                            </div>
                                            <p class="text-xs text-gray-500 mt-1 ms-4 flex gap-1 items-center">
                                                <span
                                                    class="bg-blue-50 text-blue-700 text-xs font-medium me-2 px-2.5 py-0.5 rounded-full border border-blue-100">
                                                    {{ $item->barang->kategori->nama_kategori ?? 'Kategori tidak tersedia' }}
                                                </span>
                                                <span
                                                    class="bg-slate-100 text-slate-700 text-xs font-medium me-2 px-2.5 py-0.5 rounded-full border border-slate-200">
                                                    {{ $item->barang->satuan->nama_satuan ?? 'Satuan tidak tersedia' }}
                                                </span>
                                                @php
                                                    $status =
                                                        $item->pengembalian->status_pengembalian ??
                                                        'Status tidak tersedia';
                                                @endphp

                                                @if ($status === 'Menunggu')
                                                    <span
                                                        class="bg-yellow-50 text-yellow-700 text-xs font-medium me-2 px-2.5 py-0.5 rounded-full border border-yellow-100">
                                                        Status:
                                                        {{ $item->pengembalian->status_pengembalian ?? 'Status tidak tersedia' }}
                                                    </span>
                                                @elseif ($status === 'Diterima')
                                                    <span
                                                        class="bg-emerald-50 text-emerald-700 text-xs font-medium me-2 px-2.5 py-0.5 rounded-full border border-emerald-100">
                                                        Status:
                                                        {{ $item->pengembalian->status_pengembalian ?? 'Status tidak tersedia' }}
                                                    </span>
                                                @elseif ($status === 'Ditolak')
                                                    <span
                                                        class="bg-red-50 text-red-700 text-xs font-medium me-2 px-2.5 py-0.5 rounded-full border border-red-100">
                                                        Status:
                                                        {{ $item->pengembalian->status_pengembalian ?? 'Status tidak tersedia' }}
                                                    </span>
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <div class="bg-gray-100 rounded-lg p-2 space-y-4">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                    <div class="border p-2 rounded-lg">
                                        <label for="bukti_foto"
                                            class="block mb-2 text-sm font-medium text-gray-600">Preview</label>
                                        <img id="preview"
                                            class="hidden h-44 w-full rounded-lg border border-gray-200 object-cover">
                                    </div>
                                    <div>
                                        <label for="bukti_foto"
                                            class="block mb-2 text-sm font-medium text-gray-600">Bukti
                                            Foto</label>
                                        <input type="file"
                                            class="w-full px-4 text-sm text-gray-900 border border-gray-300 rounded-lg focus:border-transparent"
                                            name="bukti_foto" id="bukti_foto" {{ !$canSubmit ? 'disabled' : '' }} />
                                    </div>
                                </div>
                            </div>
                        </div>
                        <button type="submit"
                            class="w-full mt-4 rounded-xl bg-blue-600 px-4 py-3 font-semibold text-white transition-colors hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed"
                            {{ !$canSubmit ? 'disabled' : '' }}>Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
    document.getElementById('bukti_foto').addEventListener('change', function(event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('preview').src = e.target.result;
                document.getElementById('preview').classList.remove('hidden');
            }
            reader.readAsDataURL(file);
        }
    });
</script>
