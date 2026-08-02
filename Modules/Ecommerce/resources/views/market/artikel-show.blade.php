@extends('layouts/commonMaster')
@section('title', $artikel->judul_artikel . ' - Blog')

@section('vendor-style')
<style>
  :root {
    --artikel-primary: #2563eb;
    --artikel-dark: #1e40af;
    --artikel-light: #dbeafe;
    --artikel-gradient: linear-gradient(135deg, #1e3a8a, #2563eb);
  }

  .artikel-show-hero {
    position: relative;
    width: 100%;
    height: 60vh;
    min-height: 400px;
    background-size: cover;
    background-position: center;
    display: flex;
    align-items: flex-end;
  }

  .artikel-show-hero::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(to top, rgba(0,0,0,0.9) 0%, rgba(0,0,0,0.3) 50%, rgba(0,0,0,0.1) 100%);
  }

  .artikel-show-header-content {
    position: relative;
    z-index: 1;
    width: 100%;
    padding-bottom: 40px;
  }

  .artikel-breadcrumb {
    color: rgba(255, 255, 255, 0.8);
    font-size: 0.875rem;
    margin-bottom: 15px;
  }
  
  .artikel-breadcrumb a {
    color: rgba(255, 255, 255, 0.8);
    text-decoration: none;
    transition: color 0.2s;
  }
  
  .artikel-breadcrumb a:hover {
    color: white;
  }

  .artikel-show-title {
    color: white;
    font-size: 2.5rem;
    font-weight: 800;
    line-height: 1.2;
    margin-bottom: 20px;
    text-shadow: 0 2px 4px rgba(0,0,0,0.5);
  }

  @media (max-width: 768px) {
    .artikel-show-title {
      font-size: 1.8rem;
    }
  }

  .artikel-author-bar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 20px;
    padding: 20px 0;
    border-bottom: 1px solid #e5e7eb;
    margin-bottom: 40px;
  }

  .artikel-author-info-wrap {
    display: flex;
    align-items: center;
    gap: 15px;
  }

  .artikel-author-avatar-lg {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    background: var(--artikel-primary);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
    font-weight: 600;
  }

  .artikel-author-details {
    display: flex;
    flex-direction: column;
  }

  .artikel-author-name-lg {
    font-weight: 700;
    color: #111827;
    font-size: 1rem;
  }

  .artikel-meta-lg {
    color: #6b7280;
    font-size: 0.875rem;
    display: flex;
    align-items: center;
    gap: 10px;
  }

  .artikel-badges-wrap {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
  }

  .artikel-badge-show {
    background: var(--artikel-light);
    color: var(--artikel-dark);
    font-size: 0.75rem;
    padding: 6px 12px;
    border-radius: 20px;
    font-weight: 600;
    text-decoration: none;
    transition: background 0.2s;
  }

  .artikel-badge-show:hover {
    background: var(--artikel-primary);
    color: white;
  }

  /* Prose / Typography */
  .artikel-content-wrapper {
    max-width: 740px;
    margin: 0 auto;
  }

  .artikel-prose {
    font-size: 1.125rem;
    line-height: 1.8;
    color: #374151;
  }

  .artikel-prose p {
    margin-bottom: 1.5em;
  }

  .artikel-prose h1, 
  .artikel-prose h2, 
  .artikel-prose h3, 
  .artikel-prose h4, 
  .artikel-prose h5, 
  .artikel-prose h6 {
    color: #111827;
    font-weight: 700;
    margin-top: 2em;
    margin-bottom: 1em;
    line-height: 1.3;
  }

  .artikel-prose h2 { font-size: 1.875rem; }
  .artikel-prose h3 { font-size: 1.5rem; }
  .artikel-prose h4 { font-size: 1.25rem; }

  .artikel-prose a {
    color: var(--artikel-primary);
    text-decoration: none;
    border-bottom: 1px solid transparent;
    transition: border-color 0.2s;
  }

  .artikel-prose a:hover {
    border-bottom-color: var(--artikel-primary);
  }

  .artikel-prose blockquote {
    border-left: 4px solid var(--artikel-primary);
    padding-left: 1.5em;
    font-style: italic;
    color: #4b5563;
    background: #f9fafb;
    padding: 1.5em;
    border-radius: 0 8px 8px 0;
    margin: 2em 0;
  }

  .artikel-prose img {
    max-width: 100%;
    height: auto;
    border-radius: 8px;
    margin: 2em 0;
  }

  .artikel-prose ul, 
  .artikel-prose ol {
    margin-bottom: 1.5em;
    padding-left: 1.5em;
  }

  .artikel-prose li {
    margin-bottom: 0.5em;
  }

  .artikel-prose pre {
    background: #1f2937;
    color: #e5e7eb;
    padding: 1.5em;
    border-radius: 8px;
    overflow-x: auto;
    margin: 2em 0;
  }

  .artikel-prose code {
    background: #f3f4f6;
    color: #ef4444;
    padding: 0.2em 0.4em;
    border-radius: 4px;
    font-size: 0.875em;
  }

  .artikel-prose pre code {
    background: transparent;
    color: inherit;
    padding: 0;
  }
  
  .artikel-prose hr {
    border-color: #e5e7eb;
    margin: 3em 0;
  }

  /* Share Section */
  .artikel-share-section {
    margin: 40px 0;
    padding: 20px;
    background: #f9fafb;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: space-between;
  }
  
  .artikel-share-title {
    font-weight: 600;
    color: #374151;
    margin: 0;
  }
  
  .artikel-share-buttons {
    display: flex;
    gap: 10px;
  }
  
  .btn-share {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 8px 16px;
    border-radius: 20px;
    font-weight: 500;
    font-size: 0.875rem;
    transition: all 0.2s;
    border: none;
    cursor: pointer;
  }
  
  .btn-share-link {
    background: white;
    color: #4b5563;
    border: 1px solid #d1d5db;
  }
  
  .btn-share-link:hover {
    background: #f3f4f6;
  }
  
  .btn-share-wa {
    background: #25D366;
    color: white;
    text-decoration: none;
  }
  
  .btn-share-wa:hover {
    background: #128C7E;
    color: white;
  }

  /* Related Articles (Reuse cards from index) */
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
  
  .artikel-card-title {
    font-size: 1.125rem;
    font-weight: 700;
    margin-bottom: 10px;
    color: #1f2937;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    line-height: 1.4;
  }
  
  .artikel-related-section {
    background: #f9fafb;
    padding: 60px 0;
    margin-top: 40px;
  }
</style>
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
            $initials = collect(explode(' ', $artikel->user->name ?? 'A'))->map(fn($n) => substr($n, 0, 1))->take(2)->join('');
          @endphp
          <div class="artikel-author-avatar-lg">{{ $initials }}</div>
          <div class="artikel-author-details">
            <span class="artikel-author-name-lg">{{ $artikel->user->name ?? 'Anonim' }}</span>
            <div class="artikel-meta-lg">
              <time datetime="{{ $artikel->created_at->toIso8601String() }}">{{ $artikel->created_at->translatedFormat('d M Y') }}</time>
              <span>•</span>
              <span>{{ ceil(str_word_count(strip_tags($artikel->isi_artikel)) / 200) }} mnt baca</span>
            </div>
          </div>
        </div>
        
        @if($artikel->kategori)
        <div class="artikel-badges-wrap">
          @foreach($artikel->kategori as $kat)
            <a href="{{ route('market.artikel', ['kategori' => $kat]) }}" class="artikel-badge-show">{{ $kat }}</a>
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
          <a href="https://wa.me/?text={{ urlencode($artikel->judul_artikel . ' - ' . url()->current()) }}" target="_blank" class="btn-share btn-share-wa">
            <i class="bx bxl-whatsapp"></i> WhatsApp
          </a>
        </div>
      </div>
    </div>
  </main>

  <!-- Related Articles -->
  @if($artikelTerkait->isNotEmpty())
  <section class="artikel-related-section">
    <div class="container">
      <h3 class="fw-bold mb-4">Artikel Terkait</h3>
      <div class="row g-4">
        @foreach($artikelTerkait as $terkait)
        <div class="col-12 col-md-4">
          <a href="{{ route('market.artikel.show', $terkait->slug) }}" class="artikel-card">
            <div class="artikel-card-img-wrapper">
              <img src="{{ $terkait->thumbnailUrl }}" alt="{{ $terkait->judul_artikel }}" class="artikel-card-img" width="400" height="225" loading="lazy">
            </div>
            <div class="artikel-card-body">
              <h4 class="artikel-card-title">{{ $terkait->judul_artikel }}</h4>
              <p class="text-muted small mb-0">{{ Str::limit(strip_tags($terkait->isi_artikel), 80) }}</p>
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
