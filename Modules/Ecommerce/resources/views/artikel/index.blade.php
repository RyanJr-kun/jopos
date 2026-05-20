@extends('layouts/contentNavbarLayout')

@section('title', 'Manajemen Artikel')

@section('content')
    <div class="row g-3 align-items-stretch">

        {{-- Stat Card --}}
        <div class="col-12 col-md-4 col-xl-3">
            <div class="card h-100 border-0 shadow-sm" style="background: linear-gradient(135deg, #4d50eb 0%, #8592ff 100%);">
                <div class="card-body d-flex align-items-center">
                    <div class="avatar avatar-md me-3">
                        <span class="avatar-initial rounded bg-white text-primary shadow-sm">
                            <i class="bx bx-news fs-4"></i>
                        </span>
                    </div>
                    <div>
                        <p class="text-white mb-0 text-sm">Total Artikel</p>
                        <h3 class="text-white mb-0 fw-bold">
                            {{ method_exists($artikels, 'total') ? $artikels->total() : $artikels->count() }}
                        </h3>
                    </div>
                </div>
            </div>
        </div>

        {{-- Filter & Tombol Tambah --}}
        <div class="col-12 col-md-8 col-xl-9">
            <div class="card h-100 shadow-sm">
                <div class="card-body d-flex align-items-center">
                    <form method="GET" action="{{ route('artikel.index') }}" class="row g-2 align-items-center w-100 m-0">
                        <div class="col-md-4">
                            <input type="text" name="search" id="searchInput" class="form-control"
                                placeholder="Cari judul artikel..." value="{{ request('search') }}">
                        </div>
                        <div class="col-md-3">
                            <select name="status" id="statusFilter" class="form-select">
                                <option value="">Semua Status</option>
                                <option value="published" @selected(request('status') == 'published')>Published</option>
                                <option value="draft" @selected(request('status') == 'draft')>Draft</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <input type="text" name="kategori" id="kategoriFilter" class="form-control"
                                placeholder="Filter kategori..." value="{{ request('kategori') }}">
                        </div>
                        <div class="col-md-auto d-flex gap-2">
                            <button type="submit" class="btn btn-outline-primary">
                                <i class="bx bx-filter-alt me-1"></i>Filter
                            </button>
                            <a href="{{ route('artikel.create') }}" class="btn btn-outline-info">
                                <i class="bx bx-plus me-1"></i>Artikel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Tabel Artikel --}}
        <div class="col-12">
            <div class="card rounded-2 shadow-sm">
                <div class="card-header pb-0 px-3 pt-2">
                    <h5 class="mb-n1 fw-bolder">Daftar Artikel / Blog</h5>
                    <p class="text-sm mb-0">Kelola semua artikel dan konten blog Anda di sini.</p>
                </div>
                <div class="card-body px-0 pt-0 pb-2">
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible mx-3 mt-3" role="alert">
                            <i class="bx bx-check-circle me-2"></i>{{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="px-3" style="width:60px">#</th>
                                    <th class="px-3">Thumbnail</th>
                                    <th class="px-3">Judul Artikel</th>
                                    <th class="px-3">Penulis</th>
                                    <th class="px-3">Kategori</th>
                                    <th class="px-3">Status</th>
                                    <th class="px-3">Tanggal</th>
                                    <th class="px-3 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($artikels as $i => $artikel)
                                    <tr>
                                        <td class="px-3 text-muted">
                                            {{ $artikels->firstItem() + $i }}
                                        </td>
                                        <td class="px-3">
                                            <img src="{{ $artikel->thumbnail_url }}" alt="thumbnail"
                                                class="rounded-2 object-fit-cover" style="width:54px;height:40px;"
                                                onerror="this.src='{{ asset('assets/img/no-image.png') }}'">
                                        </td>
                                        <td class="px-3">
                                            <div class="fw-semibold text-dark" style="max-width:280px">
                                                {{ Str::limit($artikel->judul_artikel, 60) }}
                                            </div>
                                            <small class="text-muted">/{{ $artikel->slug }}</small>
                                        </td>
                                        <td class="px-3">
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="avatar avatar-xs">
                                                    <span class="avatar-initial rounded-circle bg-label-primary">
                                                        {{ strtoupper(substr($artikel->user?->name ?? '-', 0, 1)) }}
                                                    </span>
                                                </div>
                                                <small>{{ $artikel->user?->name ?? '-' }}</small>
                                            </div>
                                        </td>
                                        <td class="px-3">
                                            @if (!empty($artikel->kategori))
                                                <div class="d-flex flex-wrap gap-1">
                                                    @foreach (array_slice($artikel->kategori, 0, 3) as $kat)
                                                        <span class="badge bg-label-secondary">{{ $kat }}</span>
                                                    @endforeach
                                                    @if (count($artikel->kategori) > 3)
                                                        <span class="badge bg-label-secondary">
                                                            +{{ count($artikel->kategori) - 3 }}
                                                        </span>
                                                    @endif
                                                </div>
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                        <td class="px-3">
                                            @if ($artikel->status === 'published')
                                                <span class="badge bg-label-success">Published</span>
                                            @else
                                                <span class="badge bg-label-warning">Draft</span>
                                            @endif
                                        </td>
                                        <td class="px-3">
                                            <small class="text-muted">
                                                {{ $artikel->created_at->format('d M Y') }}
                                            </small>
                                        </td>
                                        <td class="px-3 text-center">
                                            <div class="d-flex justify-content-center gap-1">

                                                @can('view-artikel')
                                                    <a href="{{ route('artikel.show', $artikel) }}"
                                                        class="action-btn text-info" title="Detail">
                                                        <i class="bx bx-show"></i>
                                                    </a>
                                                @endcan

                                                @can('edit-artikel')
                                                    <a href="{{ route('artikel.edit', $artikel) }}"
                                                        class="action-btn text-secondary" title="Edit">
                                                        <i class="bx bx-edit"></i>
                                                    </a>
                                                @endcan

                                                @can('delete-artikel')
                                                    <button type="button" class="action-btn text-danger"
                                                        data-artikel-id="{{ $artikel->id }}"
                                                        data-artikel-judul="{{ $artikel->judul_artikel }}"
                                                        data-bs-toggle="modal" data-bs-target="#deleteModal" title="Hapus">
                                                        <i class="bx bx-trash"></i>
                                                    </button>
                                                @endcan

                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8">
                                            <div class="d-flex flex-column align-items-center justify-content-center py-1">
                                                <i class="bx bx-file-blank fs-1 d-block mb-2"></i>
                                                <p>Belum ada artikel. <a href="{{ route('artikel.create') }}">Buat
                                                        sekarang</a>.</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if ($artikels->hasPages())
                        <div class="px-3 pt-3">
                            {{ $artikels->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Konfirmasi Hapus --}}
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body text-center mt-3 mx-n5">
                    <i class="bx bx-trash fa-2x text-danger mb-3" style="font-size:2.5rem"></i>
                    <p class="mb-1">Apakah Anda yakin ingin menghapus artikel ini?</p>
                    <h6 class="mt-2 fw-bold" id="artikelJudulToDelete"></h6>
                    <div class="mt-4">
                        <form id="deleteArtikelForm" method="POST" action="#">
                            @method('delete')
                            @csrf
                            <button type="submit" class="btn btn-danger btn-sm">Ya, Hapus</button>
                            <button type="button" class="btn btn-outline-secondary btn-sm ms-2"
                                data-bs-dismiss="modal">Batal</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('page-script')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const deleteModal = document.getElementById('deleteModal');
            if (deleteModal) {
                deleteModal.addEventListener('show.bs.modal', function(event) {
                    const button = event.relatedTarget;
                    const artikelId = button.getAttribute('data-artikel-id');
                    const artikelJudul = button.getAttribute('data-artikel-judul');
                    document.getElementById('artikelJudulToDelete').textContent = artikelJudul;
                    document.getElementById('deleteArtikelForm').action =
                        `{{ url('artikel') }}/${artikelId}`;
                });
            }
        });
    </script>
@endsection
