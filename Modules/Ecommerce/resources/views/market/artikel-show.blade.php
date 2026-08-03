@extends('layouts/commonMaster')
@section('title', $artikel->judul_artikel . ' - Blog')

@section('page-style')
    @vite(['resources/assets/vendor/scss/pages/page-artikel.scss'])
@endsection

@section('layoutContent')
    <x-market-header :kategoris="$kategoris"></x-market-header>

    <!-- Hero Header -->
    <header class="artikel-show-hero" style="background-image: url('{{ $artikel->thumbnailUrl }}');">
        <div class="container artikel-show-header-content">
            <nav class="artikel-breadcrumb" aria-label="breadcrumb">
                <a href="{{ route('market.home') }}">Beranda</a> &gt;
                <a href="{{ route('market.artikel') }}">Artikel</a> &gt;
                <span>{{ Str::limit($artikel->judul_artikel, 40) }}</span>
            </nav>
            <h1 class="artikel-show-title">{{ $artikel->judul_artikel }}</h1>
        </div>
    </header>

    <!-- Content Area -->
    <main class="container py-4">
        <div class="artikel-content-wrapper">

            <!-- Author Info Bar -->
            <div class="artikel-author-bar">
                <div class="artikel-author-info-wrap">
                    @php
                        $initials = collect(explode(' ', $artikel->user->name ?? 'A'))
                            ->map(fn($n) => substr($n, 0, 1))
                            ->take(2)
                            ->join('');
                    @endphp
                    <div class="artikel-author-avatar-lg">{{ $initials }}</div>
                    <div class="artikel-author-details">
                        <span class="artikel-author-name-lg">{{ $artikel->user->name ?? 'Anonim' }}</span>
                        <div class="artikel-meta-lg">
                            <time
                                datetime="{{ $artikel->created_at->toIso8601String() }}">{{ $artikel->created_at->translatedFormat('d M Y') }}</time>
                            <span>•</span>
                            <span>{{ ceil(str_word_count(strip_tags($artikel->isi_artikel)) / 200) }} mnt baca</span>
                        </div>
                    </div>
                </div>

                @if ($artikel->kategori)
                    <div class="artikel-badges-wrap">
                        @foreach ($artikel->kategori as $kat)
                            <a href="{{ route('market.artikel', ['kategori' => $kat]) }}"
                                class="artikel-badge-show">{{ $kat }}</a>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Article Content -->
            <article class="artikel-prose">
                {!! $artikel->isi_artikel !!}
            </article>

            <!-- Share Section -->
            <div class="artikel-share-section">
                <h5 class="artikel-share-title">Bagikan Artikel Ini</h5>
                <div class="artikel-share-buttons">
                    <button type="button" class="btn-share btn-share-link" onclick="copyArticleLink()">
                        <i class="bx bx-link"></i> Salin Tautan
                    </button>
                    <a href="https://wa.me/?text={{ urlencode($artikel->judul_artikel . ' - ' . url()->current()) }}"
                        target="_blank" class="btn-share btn-share-wa">
                        <i class="bx bxl-whatsapp"></i> WhatsApp
                    </a>
                </div>
            </div>
        </div>
    </main>

    <!-- Related Articles -->
    @if ($artikelTerkait->isNotEmpty())
        <section class="artikel-related-section">
            <div class="container">
                <h3 class="fw-bold mb-4">Artikel Terkait</h3>
                <div class="row g-4">
                    @foreach ($artikelTerkait as $terkait)
                        <div class="col-12 col-md-4">
                            <a href="{{ route('market.artikel.show', $terkait->slug) }}" class="artikel-card">
                                <div class="artikel-card-img-wrapper">
                                    <img src="{{ $terkait->thumbnailUrl }}" alt="{{ $terkait->judul_artikel }}"
                                        class="artikel-card-img" width="400" height="225" loading="lazy">
                                </div>
                                <div class="artikel-card-body">
                                    <h4 class="artikel-card-title">{{ $terkait->judul_artikel }}</h4>
                                    <p class="text-muted small mb-0">
                                        {{ Str::limit(strip_tags($terkait->isi_artikel), 80) }}</p>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <x-market-footer></x-market-footer>
@endsection

@section('page-script')
    <script>
        function copyArticleLink() {
            navigator.clipboard.writeText(window.location.href).then(() => {
                const btn = document.querySelector('.btn-share-link');
                const originalText = btn.innerHTML;
                btn.innerHTML = '<i class="bx bx-check"></i> Tersalin!';
                setTimeout(() => {
                    btn.innerHTML = originalText;
                }, 2000);
            }).catch(err => {
                console.error('Failed to copy link: ', err);
            });
        }
    </script>
@endsection
