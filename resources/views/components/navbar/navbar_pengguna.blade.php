<nav class="fixed top-0 z-50 w-full border-b border-gray-200 bg-white shadow-sm">
    <div class="max-w-screen-xl flex flex-wrap items-center justify-between mx-auto px-4 py-3 lg:px-5">
        <a href="/" class="flex items-center space-x-3 rtl:space-x-reverse">
            <img src="{{ asset('image/logo/polindra.png') }}" class="h-10 me-3" alt="Logo Polindra" />
            <div class="hidden flex-col md:flex">
                <p class="text-lg font-semibold tracking-tight text-slate-900">Tracking</p>
                <p class="text-sm text-slate-500">Peminjaman Barang</p>
            </div>
        </a>
        <div class="flex items-center md:order-2 gap-3">
            @if (Auth::guard('ormawa')->check())
                <div>
                    <button id="dropdownNotificationButton" data-dropdown-toggle="dropdownNotification"
                        class="relative me-4 inline-flex items-center text-sm font-medium text-center text-slate-500 transition-colors hover:text-slate-900 focus:outline-none"
                        type="button" data-dropdown-placement="bottom">
                        <i class="fa-solid fa-cart-shopping text-2xl"></i>
                        <div
                            class="absolute -top-1 -right-1 flex h-5 min-w-5 items-center justify-center rounded-full border border-white bg-blue-600 px-1 text-[11px] font-semibold text-white shadow-sm">
                            <p>
                                {{ $dataKeranjang->count() ?? 0 }}
                            </p>
                        </div>
                    </button>

                    <!-- Dropdown keranjang -->
                    <div id="dropdownNotification"
                        class="z-20 hidden w-72 max-w-xs divide-y divide-gray-100 rounded-xl border border-gray-100 bg-white shadow-lg"
                        aria-labelledby="dropdownNotificationButton">
                        <div
                            class="block rounded-t-xl border-b border-gray-100 bg-gray-50 px-4 py-3 text-center text-sm font-semibold text-slate-700">
                            Keranjang
                        </div>
                        <div class="divide-y divide-gray-100">
                            <div class="divide-y divide-gray-100">
                                @if ($dataKeranjang->isNotEmpty())
                                    @foreach ($dataKeranjang as $item)
                                        <div class="flex items-center px-4 py-3">
                                            <div class="flex-1">
                                                <h4 class="text-sm font-medium text-slate-900">
                                                    {{ $item->barang->nama_barang }}</h4>
                                                <p class="text-sm text-slate-500">Jumlah:
                                                    {{ $item->jumlah }}
                                                </p>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <p class="px-4 py-3 text-sm text-slate-500">Keranjang Anda kosong.
                                    </p>
                                @endif
                            </div>
                        </div>

                        <a href="{{ route('keranjang') }}"
                            class="block rounded-b-xl bg-gray-50 py-3 text-sm font-semibold text-center text-slate-700 transition-colors hover:bg-slate-100 hover:text-slate-900">
                            <div class="inline-flex items-center">
                                <i class="fa-solid fa-arrow-right mr-2 text-slate-500"></i>
                                Lihat Semua
                            </div>
                        </a>
                    </div>
                </div>
            @endif

            @if (Auth::guard('ormawa')->check())
                <button type="button"
                    class="flex text-sm rounded-full md:me-0 ring-1 ring-gray-200 focus:ring-4 focus:ring-gray-200"
                    id="user-menu-button" aria-expanded="false" data-dropdown-toggle="user-dropdown"
                    data-dropdown-placement="bottom">
                    <span class="sr-only">Open user menu</span>
                    <img class="w-8 h-8 rounded-full"
                        src="https://ui-avatars.com/api/?name={{ urlencode(auth('ormawa')->user()->name) }}&background=random&color=fff"
                        alt="user photo">
                </button>

                <!-- Dropdown menu -->
                <div class="z-50 hidden my-4 w-44 list-none divide-y divide-gray-100 rounded-xl bg-white shadow-lg"
                    id="user-dropdown">
                    <div class="px-4 py-3">
                        <span
                            class="block text-sm font-semibold text-slate-900">{{ Auth::guard('ormawa')->user()->name }}</span>
                        <span
                            class="block truncate text-sm text-slate-500">{{ Auth::guard('ormawa')->user()->organisasi ?? '-' }}</span>
                    </div>
                    <ul class="py-2" aria-labelledby="user-menu-button">
                        {{-- <li>
                        <a href=""
                            class="block px-4 py-2 text-sm text-slate-700 transition-colors hover:bg-slate-50 hover:text-slate-900">Profile</a>
                    </li> --}}
                        <li>
                            <a href="{{ route('ormawa.index') }}"
                                class="block px-4 py-2 text-sm text-slate-700 transition-colors hover:bg-slate-50 hover:text-slate-900">
                                Keluar
                            </a>
                        </li>
                    </ul>
                </div>
            @endif

            <button data-collapse-toggle="navbar-user" type="button"
                class="inline-flex h-10 w-10 items-center justify-center rounded-lg text-sm text-slate-500 transition-colors hover:bg-slate-100 focus:outline-none focus:ring-2 focus:ring-gray-200 md:hidden"
                aria-controls="navbar-user" aria-expanded="false">
                <span class="sr-only">Open main menu</span>
                <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                    viewBox="0 0 17 14">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M1 1h15M1 7h15M1 13h15" />
                </svg>
            </button>
        </div>
        <div class="items-center justify-between hidden w-full md:flex md:w-auto md:order-1" id="navbar-user">
            <ul
                class="flex flex-col font-medium p-4 md:p-0 mt-4 gap-2 border border-gray-100 rounded-xl bg-gray-50 md:space-x-8 rtl:space-x-reverse md:flex-row md:mt-0 md:border-0 md:bg-transparent">
                <li>
                    <a href="{{ route('beranda') }}"
                        class="block rounded-lg px-3 py-2 text-slate-700 transition-colors hover:bg-slate-100 hover:text-slate-900 md:border-0 md:p-0 {{ Route::is('beranda') ? 'bg-blue-50 text-blue-700 ring-1 ring-blue-100 font-semibold' : '' }}"
                        aria-current="page">Beranda</a>
                </li>
                <li class="relative">
                    <button id="dropdownNavbarLink" data-dropdown-toggle="informasiDropdown"
                        class="flex w-full items-center justify-between rounded-lg px-3 py-2 text-slate-700 transition-colors hover:bg-slate-100 hover:text-slate-900 md:border-0 md:p-0 focus:outline-none
                        {{ request()->is('informasi/*') ? 'bg-blue-50 text-blue-700 ring-1 ring-blue-100 font-semibold' : '' }}">
                        Informasi dan Layanan
                        <svg class="w-2.5 h-2.5 ms-2.5 transition-transform duration-300"
                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="m1 1 4 4 4-4" />
                        </svg>
                    </button>

                    <!-- Dropdown Menu -->
                    <div id="informasiDropdown"
                        class="hidden absolute z-10 w-64 rounded-xl border border-gray-100 bg-white shadow-lg">
                        <ul class="py-2 text-sm text-slate-700" aria-labelledby="dropdownNavbarLink">
                            <li>
                                <a href="{{ route('tracking') }}"
                                    class="block px-4 py-2 transition-colors hover:bg-slate-50 hover:text-slate-900
                                    {{ request()->routeIs('tracking') ? 'bg-blue-50 text-blue-700 font-semibold' : '' }}">
                                    Tracking
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('pengembalian') }}"
                                    class="block px-4 py-2 transition-colors hover:bg-slate-50 hover:text-slate-900
                                    {{ request()->routeIs('pengembalian') ? 'bg-blue-50 text-blue-700 font-semibold' : '' }}">
                                    Pengembalian
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
                <li>
                    <a href="{{ route('riwayat') }}"
                        class="block rounded-lg px-3 py-2 text-slate-700 transition-colors hover:bg-slate-100 hover:text-slate-900 md:border-0 md:p-0 {{ Route::is('riwayat') ? 'bg-blue-50 text-blue-700 ring-1 ring-blue-100 font-semibold' : '' }}"
                        aria-current="page">Riwayat</a>
                </li>
            </ul>
        </div>
    </div>
</nav>
