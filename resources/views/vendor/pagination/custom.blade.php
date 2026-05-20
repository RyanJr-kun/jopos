@if ($paginator->hasPages())
    <nav aria-label="Page navigation">
        <ul class="pagination gap-1">

            {{-- First Page Link --}}
            {{-- <li class="page-item first {{ $paginator->onFirstPage() ? 'disabled' : '' }}">
                <a class="page-link rounded-3" href="{{ $paginator->url(1) }}" aria-label="@lang('pagination.first')"
                    style="border-radius: 0.5rem !important;"><i class="icon-base bx bx-chevrons-left icon-sm"></i></a>
            </li> --}}

            {{-- Previous Page Link --}}
            <li class="page-item prev {{ $paginator->onFirstPage() ? 'disabled' : '' }}">
                <a class="page-link rounded-3" href="{{ $paginator->previousPageUrl() }}" rel="prev"
                    aria-label="@lang('pagination.previous')" style="border-radius: 0.5rem !important;"><i
                        class="icon-base bx bx-chevron-left icon-sm"></i></a>
            </li>

            {{-- ========================================== --}}
            {{-- LOGIKA CUSTOM PAGINATION ANGKA             --}}
            {{-- ========================================== --}}
            @php
                $currentPage = $paginator->currentPage();
                $lastPage = $paginator->lastPage();
            @endphp

            @if ($lastPage < 5)
                {{-- Jika total halaman di bawah 5, tampilkan semua angka normal --}}
                @for ($i = 1; $i <= $lastPage; $i++)
                    @if ($i == $currentPage)
                        <li class="page-item active" aria-current="page"><span class="page-link rounded-3"
                                style="border-radius: 0.5rem !important;">{{ $i }}</span></li>
                    @else
                        <li class="page-item"><a class="page-link rounded-3" href="{{ $paginator->url($i) }}"
                                style="border-radius: 0.5rem !important;">{{ $i }}</a></li>
                    @endif
                @endfor
            @else
                {{-- Jika total halaman 5 atau lebih, paksa sistem memotong dengan (...) --}}

                {{-- 1. Halaman Pertama Selalu Muncul --}}
                @if ($currentPage == 1)
                    <li class="page-item active" aria-current="page"><span class="page-link rounded-3"
                            style="border-radius: 0.5rem !important;">1</span></li>
                @else
                    <li class="page-item"><a class="page-link rounded-3" href="{{ $paginator->url(1) }}"
                            style="border-radius: 0.5rem !important;">1</a></li>
                @endif

                {{-- 2. Titik-titik Kiri --}}
                @if ($currentPage > 3)
                    <li class="page-item disabled"><span class="page-link rounded-3"
                            style="border-radius: 0.5rem !important;">...</span></li>
                @endif

                {{-- 3. Batasan Angka Tengah yang Dinamis --}}
                @php
                    $start = max(2, $currentPage - 1);
                    $end = min($lastPage - 1, $currentPage + 1);

                    // Kondisi jembatan saat berada di halaman awal
                    if ($currentPage <= 2) {
                        $end = min($lastPage - 1, 3);
                    }
                    // Kondisi jembatan saat mendekati halaman akhir
                    if ($currentPage >= $lastPage - 1) {
                        $start = max(2, $lastPage - 2);
                    }
                @endphp

                @for ($i = $start; $i <= $end; $i++)
                    @if ($i == $currentPage)
                        <li class="page-item active" aria-current="page"><span class="page-link rounded-3"
                                style="border-radius: 0.5rem !important;">{{ $i }}</span></li>
                    @else
                        <li class="page-item"><a class="page-link rounded-3" href="{{ $paginator->url($i) }}"
                                style="border-radius: 0.5rem !important;">{{ $i }}</a></li>
                    @endif
                @endfor

                {{-- 4. Titik-titik Kanan --}}
                @if ($currentPage < $lastPage - 2)
                    <li class="page-item disabled"><span class="page-link rounded-3"
                            style="border-radius: 0.5rem !important;">...</span></li>
                @endif

                {{-- 5. Halaman Terakhir Selalu Muncul --}}
                @if ($currentPage == $lastPage)
                    <li class="page-item active" aria-current="page"><span class="page-link rounded-3"
                            style="border-radius: 0.5rem !important;">{{ $lastPage }}</span></li>
                @else
                    <li class="page-item"><a class="page-link rounded-3" href="{{ $paginator->url($lastPage) }}"
                            style="border-radius: 0.5rem !important;">{{ $lastPage }}</a></li>
                @endif
            @endif
            {{-- ========================================== --}}

            {{-- Next Page Link --}}
            <li class="page-item next {{ $paginator->hasMorePages() ? '' : 'disabled' }}">
                <a class="page-link rounded-3" href="{{ $paginator->nextPageUrl() }}" rel="next"
                    aria-label="@lang('pagination.next')" style="border-radius: 0.5rem !important;"><i
                        class="icon-base bx bx-chevron-right icon-sm"></i></a>
            </li>

            {{-- Last Page Link --}}
            {{-- <li class="page-item last {{ $paginator->hasMorePages() ? '' : 'disabled' }}">
                <a class="page-link rounded-3" href="{{ $paginator->url($paginator->lastPage()) }}"
                    aria-label="@lang('pagination.last')" style="border-radius: 0.5rem !important;"><i
                        class="icon-base bx bx-chevrons-right icon-sm"></i></a>
            </li> --}}
        </ul>
    </nav>
@endif
