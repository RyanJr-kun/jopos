@extends('layouts/contentNavbarLayout')

@section('title', 'Detail Artikel: ' . $artikel->judul_artikel)

@section('content')
    <div class="container-fluid p-0">
        <div class="row g-3">

            {{-- Konten Utama --}}
            <div class="col-12 col-xl-8">
                <div class="card rounded-2 shadow-sm">
                    <div class="card-header pb-0 px-4 pt-3 d-flex align-items-start justify-content-between gap-2">
                        <div>
                            <h5 class="mb-1 fw-bolder">{{ $artikel->judul_artikel }}</h5>
                            <small class="text-muted">
                                <i class="bx bx-link me-1"></i>Slug: <code>{{ $artikel->slug }}</code>
                            </small>
                        </div>
                        <div class="d-flex gap-2 flex-shrink-0">
                            @can('edit-artikel')
                                <a href="{{ route('artikel.edit', $artikel) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bx bx-edit me-1"></i>Edit
                                </a>
                            @endcan
                            <a href="{{ route('artikel.index') }}" class="btn btn-sm btn-outline-secondary">
                                <i class="bx bx-arrow-back me-1"></i>Kembali
                            </a>
                        </div>
                    </div>

                    {{-- Thumbnail --}}
                    @if ($artikel->thumbnail)
                        <div class="px-4 pt-3">
                            <img src="{{ $artikel->thumbnail_url }}" alt="{{ $artikel->judul_artikel }}"
                                class="img-fluid rounded-2 w-100 object-fit-cover" style="max-height:320px;">
                        </div>
                    @endif

                    <div class="card-body px-4">
                        <div class="prose" style="line-height:1.8; font-size:0.95rem;">
                            {!! $artikel->isi_artikel !!}
                        </div>
                    </div>
                </div>
            </div>

            {{-- Sidebar --}}
            <div class="col-12 col-xl-4">

                {{-- Status & Meta --}}
                <div class="card rounded-2 shadow-sm mb-3">
                    <div class="card-header pb-0 pt-3 px-3">
                        <h6 class="mb-0 fw-bolder">
                            <i class="bx bx-info-circle me-2 text-info"></i>Informasi Artikel
                        </h6>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm table-borderless mb-0">
                            <tbody>
                                <tr>
                                    <td class="text-muted ps-0" style="width:45%">Status</td>
                                    <td>
                                        @if ($artikel->status === 'published')
                                            <span class="badge bg-label-success">
                                                <i class="bx bx-globe me-1"></i>Published
                                            </span>
                                        @else
                                            <span class="badge bg-label-warning">
                                                <i class="bx bx-edit me-1"></i>Draft
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-muted ps-0">Penulis</td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="avatar avatar-xs">
                                                <span class="avatar-initial rounded-circle bg-label-primary">
                                                    {{ strtoupper(substr($artikel->user?->name ?? '-', 0, 1)) }}
                                                </span>
                                            </div>
                                            <small>{{ $artikel->user?->name ?? '-' }}</small>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-muted ps-0">Dibuat</td>
                                    <td><small>{{ $artikel->created_at->format('d M Y, H:i') }}</small></td>
                                </tr>
                                <tr>
                                    <td class="text-muted ps-0">Diperbarui</td>
                                    <td><small>{{ $artikel->updated_at->format('d M Y, H:i') }}</small></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Kategori --}}
                <div class="card rounded-2 shadow-sm mb-3">
                    <div class="card-header pb-0 pt-3 px-3">
                        <h6 class="mb-0 fw-bolder">
                            <i class="bx bx-tag me-2 text-info"></i>Kategori
                        </h6>
                    </div>
                    <div class="card-body">
                        @if (!empty($artikel->kategori))
                            <div class="d-flex flex-wrap gap-2">
                                @foreach ($artikel->kategori as $kat)
                                    <span class="badge bg-label-primary">{{ $kat }}</span>
                                @endforeach
                            </div>
                        @else
                            <p class="text-muted mb-0">Tidak ada kategori.</p>
                        @endif
                    </div>
                </div>

                {{-- Aksi --}}
                @can('delete-artikel')
                    <div class="card rounded-2 shadow-sm border-danger">
                        <div class="card-header pb-0 pt-3 px-3">
                            <h6 class="mb-0 fw-bolder text-danger">
                                <i class="bx bx-trash me-2"></i>Zona Berbahaya
                            </h6>
                        </div>
                        <div class="card-body">
                            <p class="text-muted small mb-3">
                                Menghapus artikel ini akan menghapus seluruh konten dan thumbnail secara permanen.
                            </p>
                            <form action="{{ route('artikel.destroy', $artikel) }}" method="POST"
                                onsubmit="return confirm('Yakin ingin menghapus artikel ini? Tindakan ini tidak bisa dibatalkan.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm w-100">
                                    <i class="bx bx-trash me-1"></i>Hapus Artikel Ini
                                </button>
                            </form>
                        </div>
                    </div>
                @endcan

            </div>
        </div>
    </div>
@endsection
