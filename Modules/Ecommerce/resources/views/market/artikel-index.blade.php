@extends('layouts/commonMaster')
@section('title', 'Blog & Artikel')

@section('vendor-style')
<style>
  :root {
    --artikel-primary: #2563eb;
    --artikel-dark: #1e40af;
    --artikel-light: #dbeafe;
    --artikel-gradient: linear-gradient(135deg, #1e3a8a, #2563eb);
    --artikel-accent: #3b82f6;
  }

  .artikel-hero {
    background: var(--artikel-gradient);
    padding: 60px 0;
    color: white;
    text-align: center;
    position: relative;
    overflow: hidden;
  }
  
  .artikel-hero h1 {
    font-size: 2.5rem;
    font-weight: 700;
    margin-bottom: 20px;
    color: white;
  }
  
  .artikel-search-form {
    max-width: 500px;
    margin: 0 auto;
    position: relative;
  }
  
  .artikel-search-input {
    width: 100%;
    padding: 12px 20px;
    padding-right: 50px;
    border-radius: 30px;
    border: none;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    outline: none;
  }
  
  .artikel-search-btn {
    position: absolute;
    right: 5px;
    top: 50%;
    transform: translateY(-50%);
    background: var(--artikel-primary);
    color: white;
    border: none;
    border-radius: 50%;
    width: 36px;
    height: 36px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: background 0.3s ease;
  }
  
  .artikel-search-btn:hover {
    background: var(--artikel-dark);
  }

  .artikel-filters {
    display: flex;
    gap: 10px;
    overflow-x: auto;
    padding: 20px 0;
    scrollbar-width: none;
    -ms-overflow-style: none;
  }
  
  .artikel-filters::-webkit-scrollbar {
    display: none;
  }
  
  .artikel-filter-pill {
    padding: 8px 16px;
    background: #f3f4f6;
    color: #4b5563;
    border-radius: 20px;
    font-size: 0.875rem;
    font-weight: 500;
    text-decoration: none;
    white-space: nowrap;
    transition: all 0.3s ease;
  }
  
  .artikel-filter-pill:hover,
  .artikel-filter-pill.active {
    background: var(--artikel-primary);
    color: white;
    box-shadow: 0 4px 6px -1px rgba(37, 99, 235, 0.3);
  }

  .artikel-card {
    border-radius: 16px;
    overflow: hidden;
    background: white;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
    height: 100%;
    display: flex;
    flex-direction: column;
    text-decoration: none !important;
    color: inherit;
  }
  
  .artikel-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
  }
  
  .artikel-card-img-wrapper {
    position: relative;
    aspect-ratio: 16/9;
    overflow: hidden;
  }
  
  .artikel-card-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.5s ease;
  }
  
  .artikel-card:hover .artikel-card-img {
    transform: scale(1.05);
  }
  
  .artikel-card-body {
    padding: 20px;
    display: flex;
    flex-direction: column;
    flex-grow: 1;
  }
  
  .artikel-card-badges {
    display: flex;
    gap: 5px;
    margin-bottom: 10px;
    flex-wrap: wrap;
  }
  
  .artikel-badge {
    background: var(--artikel-light);
    color: var(--artikel-dark);
    font-size: 0.75rem;
    padding: 4px 8px;
    border-radius: 4px;
    font-weight: 600;
  }
  
  .artikel-card-title {
    font-size: 1.25rem;
    font-weight: 700;
    margin-bottom: 10px;
    color: #1f2937;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    line-height: 1.4;
  }
  
  .artikel-card-excerpt {
    font-size: 0.875rem;
    color: #6b7280;
    margin-bottom: 20px;
    flex-grow: 1;
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
  }
  
  .artikel-card-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-top: auto;
    border-top: 1px solid #f3f4f6;
    padding-top: 15px;
  }
  
  .artikel-author {
    display: flex;
    align-items: center;
    gap: 10px;
  }
  
  .artikel-author-avatar {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: var(--artikel-primary);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.75rem;
    font-weight: 600;
  }
  
  .artikel-author-info {
    display: flex;
    flex-direction: column;
  }
  
  .artikel-author-name {
    font-size: 0.875rem;
    font-weight: 600;
    color: #374151;
  }
  
  .artikel-date {
    font-size: 0.75rem;
    color: #9ca3af;
  }

  .artikel-read-time {
    font-size: 0.75rem;
    color: #6b7280;
    display: flex;
    align-items: center;
    gap: 4px;
  }

  /* Featured Card */
  .artikel-featured-card {
    position: relative;
    border-radius: 16px;
    overflow: hidden;
    margin-bottom: 40px;
    aspect-ratio: 21/9;
    display: flex;
    align-items: flex-end;
    text-decoration: none !important;
    color: white;
  }

  @media (max-width: 768px) {
    .artikel-featured-card {
      aspect-ratio: 16/9;
    }
  }

  .artikel-featured-img {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
    z-index: 1;
  }

  .artikel-featured-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(to top, rgba(0,0,0,0.8) 0%, rgba(0,0,0,0) 100%);
    z-index: 2;
  }

  .artikel-featured-content {
    position: relative;
    z-index: 3;
    padding: 40px;
    width: 100%;
    max-width: 800px;
  }

  .artikel-featured-title {
    font-size: 2rem;
    font-weight: 700;
    margin: 10px 0;
    color: white;
    text-shadow: 0 2px 4px rgba(0,0,0,0.5);
  }

  .artikel-featured-excerpt {
    font-size: 1rem;
    color: #e5e7eb;
    margin-bottom: 20px;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
  }

  .artikel-pagination {
    margin-top: 40px;
    display: flex;
    justify-content: center;
  }

  .artikel-pagination .pagination {
    --bs-pagination-active-bg: var(--artikel-primary);
    --bs-pagination-active-border-color: var(--artikel-primary);
    --bs-pagination-color: var(--artikel-dark);
  }
</style>
@endsection

@section('layoutContent')
  <x-market-header :kategoris="$kategoris"></x-market-header>
  
  <!-- Hero Section -->
  <section class="artikel-hero">
    <div class="container">
      <h1>Blog & Artikel</h1>
      <p class="mb-4">Temukan informasi terbaru, tips, dan panduan seputar teknologi.</p>
      
      <form action="{{ route('market.artikel') }}" method="GET" class="artikel-search-form">
        @if(request('kategori'))
          <input type="hidden" name="kategori" value="{{ request('kategori') }}">
        @endif
        <input type="text" name="search" class="artikel-search-input" placeholder="Cari artikel..." value="{{ request('search') }}">
        <button type="submit" class="artikel-search-btn">
          <i class="bx bx-search"></i>
        </button>
      </form>
    </div>
  </section>

  <div class="container py-5">
    
    <!-- Category Filter Pills -->
    @if($semuaKategori->isNotEmpty())
    <div class="artikel-filters mb-4">
      <a href="{{ route('market.artikel', array_filter(['search' => request('search')])) }}" 
         class="artikel-filter-pill {{ !request('kategori') ? 'active' : '' }}">
        Semua Kategori
      </a>
      @foreach($semuaKategori as $kat)
        <a href="{{ route('market.artikel', array_filter(['kategori' => $kat, 'search' => request('search')])) }}" 
           class="artikel-filter-pill {{ request('kategori') == $kat ? 'active' : '' }}">
          {{ $kat }}
        </a>
      @endforeach
    </div>
    @endif

    <!-- Featured Article (if on page 1 and no search/filter active) -->
    @if($artikelUnggulan && !request('kategori') && !request('search') && $artikels->currentPage() == 1)
      <a href="{{ route('market.artikel.show', $artikelUnggulan->slug) }}" class="artikel-featured-card shadow-sm">
        <img src="{{ $artikelUnggulan->thumbnailUrl }}" alt="{{ $artikelUnggulan->judul_artikel }}" class="artikel-featured-img" fetchpriority="high">
        <div class="artikel-featured-overlay"></div>
        <div class="artikel-featured-content">
          <div class="artikel-card-badges">
            @if($artikelUnggulan->kategori)
              @foreach($artikelUnggulan->kategori as $kat)
                <span class="artikel-badge bg-primary text-white border-0">{{ $kat }}</span>
              @endforeach
            @endif
          </div>
          <h2 class="artikel-featured-title">{{ $artikelUnggulan->judul_artikel }}</h2>
          <p class="artikel-featured-excerpt">{{ Str::limit(strip_tags($artikelUnggulan->isi_artikel), 150) }}</p>
          <div class="artikel-author">
            @php
              $initials = collect(explode(' ', $artikelUnggulan->user->name ?? 'A'))->map(fn($n) => substr($n, 0, 1))->take(2)->join('');
            @endphp
            <div class="artikel-author-avatar">{{ $initials }}</div>
            <div class="artikel-author-info">
              <span class="artikel-author-name text-white">{{ $artikelUnggulan->user->name ?? 'Anonim' }}</span>
              <span class="artikel-date text-light">
                <i class="bx bx-calendar-alt me-1"></i> {{ $artikelUnggulan->created_at->translatedFormat('d M Y') }}
                <span class="mx-1">•</span>
                <i class="bx bx-time-five me-1"></i> {{ ceil(str_word_count(strip_tags($artikelUnggulan->isi_artikel)) / 200) }} mnt baca
              </span>
            </div>
          </div>
        </div>
      </a>
    @endif

    <!-- Article Grid -->
    @if($artikels->count() > 0)
      <div class="row g-4">
        @foreach($artikels as $artikel)
          <div class="col-12 col-md-6 col-lg-4">
            <a href="{{ route('market.artikel.show', $artikel->slug) }}" class="artikel-card">
              <div class="artikel-card-img-wrapper">
                <img src="{{ $artikel->thumbnailUrl }}" alt="{{ $artikel->judul_artikel }}" class="artikel-card-img" width="400" height="225" loading="lazy">
              </div>
              <div class="artikel-card-body">
                <div class="artikel-card-badges">
                  @if($artikel->kategori)
                    @foreach($artikel->kategori as $kat)
                      <span class="artikel-badge">{{ $kat }}</span>
                    @endforeach
                  @endif
                </div>
                <h3 class="artikel-card-title">{{ $artikel->judul_artikel }}</h3>
                <p class="artikel-card-excerpt">{{ Str::limit(strip_tags($artikel->isi_artikel), 120) }}</p>
                <div class="artikel-card-footer">
                  <div class="artikel-author">
                    @php
                      $initials = collect(explode(' ', $artikel->user->name ?? 'A'))->map(fn($n) => substr($n, 0, 1))->take(2)->join('');
                    @endphp
                    <div class="artikel-author-avatar">{{ $initials }}</div>
                    <div class="artikel-author-info">
                      <span class="artikel-author-name">{{ $artikel->user->name ?? 'Anonim' }}</span>
                      <time class="artikel-date" datetime="{{ $artikel->created_at->toIso8601String() }}">
                        {{ $artikel->created_at->translatedFormat('d M Y') }}
                      </time>
                    </div>
                  </div>
                  <div class="artikel-read-time">
                    <i class="bx bx-time-five"></i> {{ ceil(str_word_count(strip_tags($artikel->isi_artikel)) / 200) }} mnt
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
