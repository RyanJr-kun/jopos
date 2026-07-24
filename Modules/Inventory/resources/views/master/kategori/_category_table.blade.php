<div class="table-responsive p-0 my-3 d-none d-md-block">
    <table class="table table-hover align-items-center justify-content-start mb-0" id="tableData">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="20%">
                    {{ request('type', 'utama') === 'utama' ? 'Kategori Utama' : 'Sub Kategori' }}
                </th>
                <th width="20%">Slug</th>
                <th width="15%">
                    {{ request('type') === 'utama' ? 'Sub Kategori' : 'Kategori Utama' }}
                </th>
                <th width="10%" class="text-center">Jml Produk</th>
                <th width="10%" class="text-center">Status</th>
                <th width="10%"></th>
            </tr>
        </thead>
        <tbody id="isiTable">
            @forelse ($kategoris as $key => $kategori)
                <tr id="kategori-row-{{ $kategori->slug }}">
                    <td>{{ ++$key }}</td>
                    {{-- Kolom 1: Gambar & Nama Kategori --}}
                    <td>
                        <div title="image & Nama Kategori" class="d-flex align-items-center">
                            @if ($kategori->img_kategori)
                                <img src="{{ Storage::url($kategori->img_kategori) }}" class="avatar avatar-sm me-3"
                                    alt="{{ $kategori->name }}">
                            @else
                                <img src="{{ asset('assets/img/produk.png') }}" class="avatar avatar-sm me-3"
                                    alt="Gambar produk default">
                            @endif
                            <div>
                                <p class="mb-0 fw-bold">{{ $kategori->name }}</p>
                                {{-- Label kecil di bawah nama untuk memperjelas --}}
                                @if ($kategori->parent_id)
                                    <small class="text-muted">Sub Kategori</small>
                                @else
                                    <small class="text-muted">Kategori Utama</small>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td>
                        <p class="text-xs text-dark fw-bold mb-0">{{ $kategori->slug }}</p>
                    </td>
                    <td>
                        @if (request('type') === 'utama')
                            {{-- TAMPILAN UNTUK KATEGORI UTAMA: Menampilkan daftar anak (Sub) --}}
                            <div class="d-flex flex-wrap gap-1">
                                @forelse ($kategori->children->take(10) as $sub)
                                    {{-- Tampilkan 3 saja --}}
                                    <span class="badge rounded-pill bg-label-secondary text-xs">
                                        #{{ $sub->name }}
                                    </span>
                                @empty
                                    <span class="text-muted text-xs">Tidak ada sub</span>
                                @endforelse

                                @if ($kategori->children->count() > 10)
                                    <span class="text-xs text-muted">... +{{ $kategori->children->count() - 3 }}
                                        lainnya</span>
                                @endif
                            </div>
                        @else
                            {{-- TAMPILAN STANDAR: Menampilkan Parent jika dia adalah sub --}}
                            @if ($kategori->parent)
                                <span class="badge bg-label-primary">{{ $kategori->parent->name }}</span>
                            @else
                                <span class="text-muted text-xs">—</span>
                            @endif
                        @endif
                    </td>
                    <td class="text-center">{{ $kategori->products_count }}</td>
                    <td class="text-center text-sm">
                        @if ($kategori->status)
                            <span class="badge bg-label-success">Aktif</span>
                        @else
                            <span class="badge bg-label-secondary">Tidak Aktif</span>
                        @endif
                    </td>
                    <td>
                        <div class="d-flex align-items-center justify-content-center gap-2">
                            @can('edit-kategoriproduk')
                                <a href="#" class="action-btn text-secondary" data-bs-toggle="modal"
                                    data-bs-target="#editModal"
                                    data-url="{{ route('kategoriproduk.getjson', $kategori->slug) }}"
                                    data-update-url="{{ route('kategoriproduk.update', $kategori->slug) }}"
                                    title="Edit kategori">
                                    <i class="bx bx-edit"></i>
                                </a>
                            @endcan
                            @can('delete-kategoriproduk')
                                <a href="#" class="action-btn text-danger" data-bs-toggle="modal"
                                    data-bs-target="#deleteConfirmationModal" data-kategori-slug="{{ $kategori->slug }}"
                                    data-kategori-name="{{ $kategori->name }}" title="Hapus kategori">
                                    <i class="bx bx-trash"></i>
                                </a>
                            @endcan
                        </div>
                    </td>
                </tr>
            @empty
                <tr id="kategori-row-empty">
                    <td colspan="7" class="text-center py-4">
                        <p class="text-dark text-sm fw-bold mb-0">Belum ada data kategori.</p>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
    <div class="my-3 ms-3">{{ $kategoris->onEachSide(2)->links() }}</div>
</div>

<div class="d-block d-md-none px-3 pb-3">
    @forelse ($kategoris as $key => $kategori)
        <div class="card mb-3 shadow-none border kategori-mobile-card" id="kategori-card-{{ $kategori->slug }}">
            <div class="card-body p-3">

                {{-- ── Header: Gambar + Nama + Badge Tipe ── --}}
                <div class="d-flex align-items-start justify-content-between mb-2">
                    <div class="d-flex align-items-center gap-2">
                        @if ($kategori->img_kategori)
                            <img src="{{ Storage::url($kategori->img_kategori) }}" class="avatar avatar-sm rounded"
                                alt="{{ $kategori->name }}">
                        @else
                            <img src="{{ asset('assets/img/produk.png') }}" class="avatar avatar-sm rounded"
                                alt="Gambar produk default">
                        @endif
                        <div>
                            <h6 class="mb-0 text-sm fw-bold">{{ $kategori->name }}</h6>
                            @if ($kategori->parent_id)
                                <small class="text-muted">Sub Kategori</small>
                            @else
                                <small class="text-primary">Kategori Utama</small>
                            @endif
                        </div>
                    </div>

                    {{-- Status Badge --}}
                    @if ($kategori->status)
                        <span class="badge bg-label-success">Aktif</span>
                    @else
                        <span class="badge bg-label-secondary">Tidak Aktif</span>
                    @endif
                </div>
                <hr class="my-2">
                <div class="row g-2 text-xs">
                    <div class="col-12">
                        @if (request('type') === 'utama')
                            <span class="text-muted d-block mb-1">Sub Kategori:</span>
                            @php
                                $allChildren = $kategori->children;
                                $visibleChildren = $allChildren->take(3);
                                $hiddenChildren = $allChildren->slice(3);
                                $hiddenCount = $hiddenChildren->count();

                                // Bangun konten popover (plain text, aman dari XSS)
                                $popoverContent = $hiddenChildren
                                    ->pluck('name')
                                    ->map(fn($n) => '#' . $n)
                                    ->implode(', ');
                            @endphp

                            <div class="d-flex flex-wrap align-items-center gap-1">
                                @forelse ($visibleChildren as $sub)
                                    <span class="badge rounded-pill bg-label-secondary text-xs">
                                        #{{ $sub->name }}
                                    </span>
                                @empty
                                    <span class="text-muted text-xs">Tidak ada sub</span>
                                @endforelse

                                @if ($hiddenCount > 0)
                                    <button type="button"
                                        class="badge rounded-pill bg-label-primary text-xs border-0 popover-sub-trigger"
                                        data-bs-toggle="popover" data-bs-placement="top" data-bs-trigger="focus"
                                        data-bs-title="Sub Kategori Lainnya" data-bs-content="{{ $popoverContent }}"
                                        aria-label="Lihat {{ $hiddenCount }} sub kategori lainnya">
                                        +{{ $hiddenCount }} lainnya
                                    </button>
                                @endif
                            </div>
                        @else
                            {{-- Sub Kategori → tampilkan Parent --}}
                            <span class="text-muted">Kategori Utama:</span>
                            @if ($kategori->parent)
                                <span class="badge bg-label-primary ms-1">{{ $kategori->parent->name }}</span>
                            @else
                                <span class="text-muted text-xs ms-1">—</span>
                            @endif
                        @endif
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-3">
                    @can('edit-kategoriproduk')
                        <a href="#" class="text-dark fw-bold text-xs d-flex align-items-center gap-1"
                            data-bs-toggle="modal" data-bs-target="#editModal"
                            data-url="{{ route('kategoriproduk.getjson', $kategori->slug) }}"
                            data-update-url="{{ route('kategoriproduk.update', $kategori->slug) }}" title="Edit kategori">
                            <div
                                class="avatar avatar-xs d-flex align-items-center justify-content-center bg-label-primary rounded">
                                <i class="bx bx-edit fs-6"></i>
                            </div>
                        </a>
                    @endcan
                    @can('delete-kategoriproduk')
                        <a href="#" class="text-danger fw-bold text-xs d-flex align-items-center gap-1 delete-btn"
                            data-bs-toggle="modal" data-bs-target="#deleteConfirmationModal"
                            data-kategori-slug="{{ $kategori->slug }}" data-kategori-name="{{ $kategori->name }}"
                            title="Hapus kategori">
                            <div
                                class="avatar avatar-xs d-flex align-items-center justify-content-center bg-label-danger rounded">
                                <i class="bx bx-trash fs-6"></i>
                            </div>
                        </a>
                    @endcan
                </div>

            </div>
        </div>
    @empty
        <div class="card border shadow-none">
            <div class="card-body text-center py-4">
                <p class="text-dark text-sm fw-bold mb-0">Belum ada data kategori.</p>
            </div>
        </div>
    @endforelse
    <div class="mt-2">{{ $kategoris->onEachSide(2)->links() }}</div>
</div>
