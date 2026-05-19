<div class="d-none d-md-block">
    <div class="table-responsive text-nowrap mt-3">
        <table class="table table-hover align-items-center mb-0" id="tableData">
            <thead class="table-light">
                <tr>
                    <th>Produk</th>
                    <th>Kategori & Brand</th>
                    <th>Harga Jual</th>
                    <th class="text-center">Total Stok</th>
                    <th>Pembuat</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody id="isiTable" class="table-border-bottom-0">
                @forelse ($produk as $products)
                    <tr>
                        {{-- Kolom 1: Info Produk & Gambar Utama --}}
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="me-3">
                                    @if ($products->primaryImage)
                                        <img src="{{ Storage::url($products->primaryImage->path) }}" class="rounded"
                                            style="width: 50px; height: 50px; object-fit: cover;"
                                            alt="{{ $products->name_product }}">
                                    @else
                                        <img src="{{ asset('assets/img/produk.png') }}" class="rounded"
                                            style="width: 50px; height: 50px; object-fit: cover;" alt="Default">
                                    @endif
                                </div>
                                <div class="d-flex flex-column">
                                    <span class="fw-bold text-dark text-wrap"
                                        style="max-width: 250px;">{{ $products->name_product }}</span>
                                    <small class="text-muted">SKU: {{ $products->sku }}</small>
                                    @if ($products->wajib_seri)
                                        <span class="badge bg-label-warning px-2 py-1 mt-1"
                                            style="font-size: 0.65rem; width: fit-content;"><i
                                                class="bx bx-barcode-reader me-1"></i>Wajib Seri</span>
                                    @endif
                                </div>
                            </div>
                        </td>
                        {{-- Kolom 2: Kategori & Brand --}}
                        <td>
                            <div class="d-flex flex-column gap-1">
                                <span class="badge bg-label-primary"
                                    style="width: fit-content;">{{ $products->category->name ?? '-' }}</span>
                                <span class="badge bg-label-secondary"
                                    style="width: fit-content;">{{ $products->brand->name ?? '-' }}</span>
                            </div>
                        </td>
                        {{-- Kolom 3: Harga Jual --}}
                        <td>
                            <span class="text-dark fw-bold">Rp
                                {{ number_format($products->harga_jual, 0, ',', '.') }}</span><br>
                            <small class="text-muted">/ {{ $products->unit->name ?? 'Unit' }}</small>
                        </td>
                        {{-- Kolom 4: Total Stok --}}
                        <td class="text-center">
                            @php $totalStok = $products->stocks->sum('qty') ?? 0; @endphp
                            <span
                                class="badge {{ $totalStok <= $products->stok_minimum ? 'bg-danger' : 'bg-success' }}">{{ $totalStok }}</span>
                        </td>
                        {{-- Kolom 5: Pembuat --}}
                        <td>
                            <div class="d-flex align-items-center">
                                <img src="{{ $products->user && $products->user->avatar ? Storage::url($products->user->avatar) : asset('assets/img/user.webp') }}"
                                    class="rounded-circle me-2" style="width: 30px; height: 30px; object-fit: cover;"
                                    alt="user">
                                <small class="text-dark fw-bold">{{ $products->user->name ?? 'Sistem' }}</small>
                            </div>
                        </td>
                        {{-- Kolom 6: Aksi --}}
                        <td class="text-center">
                            <div class="d-flex justify-content-center gap-2">
                                <a href="{{ route('produk.show', $products->slug) }}" class="action-btn text-info"
                                    data-bs-toggle="tooltip" title="Detail"><i class="bx bx-show"></i></a>
                                <a href="{{ route('produk.edit', $products->slug) }}" class="action-btn text-secondary"
                                    data-bs-toggle="tooltip" title="Edit"><i class="bx bx-edit-alt"></i></a>
                                <button type="button" class="action-btn text-danger bg-transparent border-0"
                                    data-bs-toggle="modal" data-bs-target="#deleteConfirmationModal"
                                    data-product-slug="{{ $products->slug }}" title="Hapus"><i
                                        class="bx bx-trash"></i></button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <div class="d-flex flex-column align-items-center">
                                <i class="bx bx-box text-muted mb-2" style="font-size: 3rem;"></i>
                                <h6 class="text-muted">Data produk tidak ditemukan.</h6>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- TAMPILAN MOBILE: Menampilkan Card (Hanya muncul di layar kecil) --}}
<div class="d-block d-md-none px-3 mt-3">
    @forelse ($produk as $products)
        <div class="card border mb-3 shadow-none">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div class="d-flex align-items-center">
                        {{-- Gambar --}}
                        <div class="me-3">
                            @if ($products->primaryImage)
                                <img src="{{ Storage::url($products->primaryImage->path) }}" class="rounded"
                                    style="width: 50px; height: 50px; object-fit: cover;"
                                    alt="{{ $products->name_product }}">
                            @else
                                <img src="{{ asset('assets/img/produk.png') }}" class="rounded"
                                    style="width: 50px; height: 50px; object-fit: cover;" alt="Default">
                            @endif
                        </div>
                        {{-- Judul & SKU --}}
                        <div class="d-flex flex-column">
                            <span class="fw-bold text-dark text-wrap"
                                style="font-size: 0.95rem;">{{ $products->name_product }}</span>
                            <small class="text-muted" style="font-size: 0.75rem;">SKU: {{ $products->sku }}</small>
                        </div>
                    </div>
                    {{-- Dropdown Action Minified --}}
                    <div class="dropdown">
                        <button class="btn p-0" type="button" id="cardOpt{{ $products->id }}"
                            data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="bx bx-dots-vertical-rounded fs-4"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-end" aria-labelledby="cardOpt{{ $products->id }}">
                            <a class="dropdown-item text-info" href="{{ route('produk.show', $products->slug) }}"><i
                                    class="bx bx-show me-2"></i> Detail</a>
                            <a class="dropdown-item text-secondary"
                                href="{{ route('produk.edit', $products->slug) }}"><i class="bx bx-edit-alt me-2"></i>
                                Edit</a>
                            <button type="button" class="dropdown-item text-danger" data-bs-toggle="modal"
                                data-bs-target="#deleteConfirmationModal"
                                data-product-slug="{{ $products->slug }}"><i class="bx bx-trash me-2"></i>
                                Hapus</button>
                        </div>
                    </div>
                </div>

                {{-- Informasi Detail (Badges & Harga) --}}
                <div class="row g-2 mt-2">
                    <div class="col-6">
                        <small class="text-muted d-block mb-1" style="font-size: 0.7rem;">KATEGORI & BRAND</small>
                        <div class="d-flex flex-wrap gap-1">
                            <span class="badge bg-label-primary px-2 py-1"
                                style="font-size: 0.65rem;">{{ $products->category->name ?? '-' }}</span>
                            <span class="badge bg-label-secondary px-2 py-1"
                                style="font-size: 0.65rem;">{{ $products->brand->name ?? '-' }}</span>
                        </div>
                    </div>
                    <div class="col-6 text-end">
                        <small class="text-muted d-block mb-1" style="font-size: 0.7rem;">HARGA</small>
                        <span class="text-dark fw-bold" style="font-size: 0.9rem;">Rp
                            {{ number_format($products->harga_jual, 0, ',', '.') }}</span>
                        <small class="text-muted d-block" style="font-size: 0.7rem;">/
                            {{ $products->unit->name ?? 'Unit' }}</small>
                    </div>
                </div>

                {{-- Stok & Wajib Seri --}}
                <div class="d-flex justify-content-between align-items-center mt-3 pt-2 border-top">
                    <div class="d-flex align-items-center gap-2">
                        @php $totalStok = $products->stocks->sum('qty') ?? 0; @endphp
                        <span
                            class="badge {{ $totalStok <= $products->stok_minimum ? 'bg-danger' : 'bg-success' }}">Stok:
                            {{ $totalStok }}</span>

                        @if ($products->wajib_seri)
                            <span class="badge bg-label-warning px-2 py-1" style="font-size: 0.65rem;"><i
                                    class="bx bx-barcode-reader me-1"></i>Seri</span>
                        @endif
                    </div>
                    {{-- Pembuat --}}
                    <div class="d-flex align-items-center">
                        <img src="{{ $products->user && $products->user->avatar ? Storage::url($products->user->avatar) : asset('assets/img/user.webp') }}"
                            class="rounded-circle me-1" style="width: 20px; height: 20px; object-fit: cover;"
                            alt="user">
                        <small class="text-muted"
                            style="font-size: 0.7rem;">{{ $products->user->name ?? 'Sistem' }}</small>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="text-center py-5">
            <i class="bx bx-box text-muted mb-2" style="font-size: 3rem;"></i>
            <h6 class="text-muted">Data produk tidak ditemukan.</h6>
        </div>
    @endforelse
</div>

{{-- Pagination (Satu untuk Keduanya) --}}
<div class="my-3 mx-3 d-flex justify-content-end">
    {{ $produk->links() }}
</div>

<script>
    // Inisialisasi tooltip Bootstrap jika belum ada
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    })
</script>
