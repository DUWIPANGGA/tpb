@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination Navigation" class="flex justify-center">
        <ul class="inline-flex items-center gap-1 rounded-xl border border-blue-100 bg-white p-1 shadow-sm">
            @if ($paginator->onFirstPage())
                <li>
                    <span
                        class="inline-flex h-9 w-9 cursor-not-allowed items-center justify-center rounded-lg text-slate-300">
                        <i class="fa-solid fa-chevron-left text-xs"></i>
                    </span>
                </li>
            @else
                <li>
                    <a href="{{ $paginator->previousPageUrl() }}"
                        class="inline-flex h-9 w-9 items-center justify-center rounded-lg text-slate-500 transition-colors hover:bg-blue-50 hover:text-blue-700"
                        aria-label="Previous">
                        <i class="fa-solid fa-chevron-left text-xs"></i>
                    </a>
                </li>
            @endif

            @foreach ($elements as $element)
                @if (is_string($element))
                    <li>
                        <span
                            class="inline-flex h-9 min-w-9 items-center justify-center px-2 text-sm text-slate-400">{{ $element }}</span>
                    </li>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li>
                                <span aria-current="page"
                                    class="inline-flex h-9 min-w-9 items-center justify-center rounded-lg bg-blue-600 px-3 text-sm font-semibold text-white shadow-sm">
                                    {{ $page }}
                                </span>
                            </li>
                        @else
                            <li>
                                <a href="{{ $url }}"
                                    class="inline-flex h-9 min-w-9 items-center justify-center rounded-lg px-3 text-sm font-medium text-slate-600 transition-colors hover:bg-blue-50 hover:text-blue-700"
                                    aria-label="Go to page {{ $page }}">
                                    {{ $page }}
                                </a>
                            </li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            @if ($paginator->hasMorePages())
                <li>
                    <a href="{{ $paginator->nextPageUrl() }}"
                        class="inline-flex h-9 w-9 items-center justify-center rounded-lg text-slate-500 transition-colors hover:bg-blue-50 hover:text-blue-700"
                        aria-label="Next">
                        <i class="fa-solid fa-chevron-right text-xs"></i>
                    </a>
                </li>
            @else
                <li>
                    <span
                        class="inline-flex h-9 w-9 cursor-not-allowed items-center justify-center rounded-lg text-slate-300">
                        <i class="fa-solid fa-chevron-right text-xs"></i>
                    </span>
                </li>
            @endif
        </ul>
    </nav>
@endif
