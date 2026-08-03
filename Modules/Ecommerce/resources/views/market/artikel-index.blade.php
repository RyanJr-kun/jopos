@extends('layouts/commonMaster')
@section('title', 'Blog & Artikel')

@section('page-style')
    @vite(['resources/assets/vendor/scss/pages/page-artikel.scss'])
@endsection

@section('layoutContent')
    <x-market-header :kategoris="$kategoris"></x-market-header>

    <!-- Hero Section -->
    <section class="artikel-hero">
        <div class="container">
            <h1>Blog & Artikel</h1>
            <p class="mb-4">Temukan informasi terbaru, tips, dan panduan seputar teknologi.</p>

            <form action="{{ route('market.artikel') }}" method="GET" class="artikel-search-form">
                @if (request('kategori'))
                    <input type="hidden" name="kategori" value="{{ request('kategori') }}">
                @endif
                <input type="text" name="search" class="artikel-search-input" placeholder="Cari artikel..."
                    value="{{ request('search') }}">
                <button type="submit" class="artikel-search-btn">
                    <i class="bx bx-search"></i>
                </button>
            </form>
        </div>
    </section>

    <div class="container py-5">

        <!-- Category Filter Pills -->
        @if ($semuaKategori->isNotEmpty())
            <div class="artikel-filters mb-4">
                <a href="{{ route('market.artikel', array_filter(['search' => request('search')])) }}"
                    class="artikel-filter-pill {{ !request('kategori') ? 'active' : '' }}">
                    Semua Kategori
                </a>
                @foreach ($semuaKategori as $kat)
                    <a href="{{ route('market.artikel', array_filter(['kategori' => $kat, 'search' => request('search')])) }}"
                        class="artikel-filter-pill {{ request('kategori') == $kat ? 'active' : '' }}">
                        {{ $kat }}
                    </a>
                @endforeach
            </div>
        @endif

        <!-- Featured Article (if on page 1 and no search/filter active) -->
        @if ($artikelUnggulan && !request('kategori') && !request('search') && $artikels->currentPage() == 1)
            <a href="{{ route('market.artikel.show', $artikelUnggulan->slug) }}" class="artikel-featured-card shadow-sm">
                <img src="{{ $artikelUnggulan->thumbnailUrl }}" alt="{{ $artikelUnggulan->judul_artikel }}"
                    class="artikel-featured-img" fetchpriority="high">
                <div class="artikel-featured-overlay"></div>
                <div class="artikel-featured-content">
                    <div class="artikel-card-badges">
                        @if ($artikelUnggulan->kategori)
                            @foreach ($artikelUnggulan->kategori as $kat)
                                <span class="artikel-badge bg-primary text-white border-0">{{ $kat }}</span>
                            @endforeach
                        @endif
                    </div>
                    <h2 class="artikel-featured-title">{{ $artikelUnggulan->judul_artikel }}</h2>
                    <p class="artikel-featured-excerpt">{{ Str::limit(strip_tags($artikelUnggulan->isi_artikel), 150) }}
                    </p>
                    <div class="artikel-author">
                        @php
                            $initials = collect(explode(' ', $artikelUnggulan->user->name ?? 'A'))
                                ->map(fn($n) => substr($n, 0, 1))
                                ->take(2)
                                ->join('');
                        @endphp
                        <div class="artikel-author-avatar">{{ $initials }}</div>
                        <div class="artikel-author-info">
                            <span
                                class="artikel-author-name text-white">{{ $artikelUnggulan->user->name ?? 'Anonim' }}</span>
                            <span class="artikel-date text-light">
                                <i class="bx bx-calendar-alt me-1"></i>
                                {{ $artikelUnggulan->created_at->translatedFormat('d M Y') }}
                                <span class="mx-1">•</span>
                                <i class="bx bx-time-five me-1"></i>
                                {{ ceil(str_word_count(strip_tags($artikelUnggulan->isi_artikel)) / 200) }} mnt baca
                            </span>
                        </div>
                    </div>
                </div>
            </a>
        @endif

        <!-- Article Grid -->
        @if ($artikels->count() > 0)
            <div class="row g-4">
                @foreach ($artikels as $artikel)
                    <div class="col-12 col-md-6 col-lg-4">
                        <a href="{{ route('market.artikel.show', $artikel->slug) }}" class="artikel-card">
                            <div class="artikel-card-img-wrapper">
                                <img src="{{ $artikel->thumbnailUrl }}" alt="{{ $artikel->judul_artikel }}"
                                    class="artikel-card-img" width="400" height="225" loading="lazy">
                            </div>
                            <div class="artikel-card-body">
                                <div class="artikel-card-badges">
                                    @if ($artikel->kategori)
                                        @foreach ($artikel->kategori as $kat)
                                            <span class="artikel-badge">{{ $kat }}</span>
                                        @endforeach
                                    @endif
                                </div>
                                <h3 class="artikel-card-title">{{ $artikel->judul_artikel }}</h3>
                                <p class="artikel-card-excerpt">{{ Str::limit(strip_tags($artikel->isi_artikel), 120) }}
                                </p>
                                <div class="artikel-card-footer">
                                    <div class="artikel-author">
                                        @php
                                            $initials = collect(explode(' ', $artikel->user->name ?? 'A'))
                                                ->map(fn($n) => substr($n, 0, 1))
                                                ->take(2)
                                                ->join('');
                                        @endphp
                                        <div class="artikel-author-avatar">{{ $initials }}</div>
                                        <div class="artikel-author-info">
                                            <span class="artikel-author-name">{{ $artikel->user->name ?? 'Anonim' }}</span>
                                            <time class="artikel-date"
                                                datetime="{{ $artikel->created_at->toIso8601String() }}">
                                                {{ $artikel->created_at->translatedFormat('d M Y') }}
                                            </time>
                                        </div>
                                    </div>
                                    <div class="artikel-read-time">
                                        <i class="bx bx-time-five"></i>
                                        {{ ceil(str_word_count(strip_tags($artikel->isi_artikel)) / 200) }} mnt
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="artikel-pagination">
                {{ $artikels->links('pagination::bootstrap-5') }}
            </div>
        @else
            <div class="text-center py-5">
                <i class="bx bx-news text-muted" style="font-size: 4rem;"></i>
                <h3 class="mt-3">Tidak ada artikel ditemukan</h3>
                <p class="text-muted">Coba cari dengan kata kunci lain atau pilih kategori yang berbeda.</p>
                <a href="{{ route('market.artikel') }}" class="btn btn-primary mt-3">Reset Filter</a>
            </div>
        @endif

    </div>

    <x-market-footer></x-market-footer>
@endsection
