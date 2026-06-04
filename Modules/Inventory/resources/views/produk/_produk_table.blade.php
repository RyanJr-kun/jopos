<div class="d-none d-md-block">
    <div class="table-responsive text-nowrap mt-3">
        <table class="table table-hover align-items-center mb-0" id="tableData">
            <thead>
                <tr>
                    <th width="5%" class="text-center">No</th>
                    <th>Produk</th>
                    <th class="text-center">HPP</th>
                    <th class="text-center">Harga Jual</th>
                    <th class="text-center">Total Stok</th>
                    <th>Pembuat</th>
                    <th></th>
                </tr>
            </thead>
            <tbody id="isiTable" class="table-border-bottom-0">
                @forelse ($produk as $key => $products)
                    <tr>
                        <td class="text-center">{{ ++$key }} </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="me-3">
                                    @if ($products->primaryImage)
                                        <img src="{{ Storage::url($products->primaryImage->path) }}" class="rounded"
                                            style="width: 90px; height: 90px; object-fit: cover;"
                                            alt="{{ $products->name_product }}">
                                    @else
                                        <img src="{{ asset('assets/img/produk.png') }}" class="rounded"
                                            style="width: 90px; height: 90px; object-fit: cover;" alt="Default">
                                    @endif
                                </div>
                                <div class="d-flex flex-column">
                                    <span class="fw-bold text-dark text-wrap"
                                        style="max-width: 250px;">{{ Str::limit(strip_tags($products->name_product), 30) ?: '-' }}</span>
                                    <small class="text-muted">SKU:
                                        {{ Str::limit(strip_tags($products->sku), 20) ?: '-' }}</small>
                                    <div class="flex-inlane mt-1">
                                        <span class="badge bg-label-primary"
                                            style="width: fit-content;">{{ $products->category->name ?? '-' }}</span>
                                        <span class="badge bg-label-secondary"
                                            style="width: fit-content;">{{ $products->brand->name ?? '-' }}</span>
                                        @if ($products->wajib_seri)
                                            <span class="badge bg-label-warning px-2 py-1 mt-1"
                                                style="font-size: 0.65rem; width: fit-content;"><i
                                                    class="bx bx-barcode-reader me-1"></i>SN</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <p class="link link-danger fw-bold text-center"
                                title="Harga Beli Per {{ $products->unit->name ?? 'Unit' }}" data-bs-toggle="tooltip"
                                data-bs-target="up">Rp
                                {{ number_format($products->harga_beli, 0, ',', '.') }}</p>

                        </td>
                        <td>
                            <p class="link link-success fw-bold text-center"
                                title="Harga Jual Per {{ $products->unit->name ?? 'Unit' }}" data-bs-toggle="tooltip"
                                data-bs-target="up">Rp
                                {{ number_format($products->harga_jual, 0, ',', '.') }}</p>

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
                        <td>
                            <div class="dropdown">
                                <button class="btn p-0" type="button" id="cardOpt{{ $products->id }}"
                                    data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="bx bx-dots-vertical-rounded fs-4"></i>
                                </button>
                                <div class="dropdown-menu dropdown-menu-end"
                                    aria-labelledby="cardOpt{{ $products->id }}">
                                    <a class="dropdown-item text-info"
                                        href="{{ route('produk.show', $products->slug) }}"><i
                                            class="bx bx-show me-2"></i> Detail</a>
                                    <a class="dropdown-item text-secondary"
                                        href="{{ route('produk.edit', $products->slug) }}"><i
                                            class="bx bx-edit-alt me-2"></i>
                                        Edit</a>
                                    <button type="button" class="dropdown-item text-danger" data-bs-toggle="modal"
                                        data-bs-target="#deleteConfirmationModal"
                                        data-product-slug="{{ $products->slug }}"><i class="bx bx-trash me-2"></i>
                                        Hapus</button>
                                </div>
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
    @forelse ($produk as $key =>  $products)
        <div class="card border mb-3 shadow-sm">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div class="badge bg-label-secondary">No. {{ ++$key }}</div>
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
                <div class="d-flex align-items-center">
                    {{-- Gambar --}}
                    <div class="me-3">
                        @if ($products->primaryImage)
                            <img src="{{ Storage::url($products->primaryImage->path) }}" class="rounded rounded-2"
                                style="width: 80px; height: 80px; object-fit: cover;"
                                alt="{{ $products->name_product }}">
                        @else
                            <img src="{{ asset('assets/img/produk.png') }}" class="rounded"
                                style="width: 80px; height: 80px; object-fit: cover;" alt="Default">
                        @endif
                    </div>
                    {{-- Judul & SKU --}}
                    <div class="d-flex flex-column">
                        <span class="fw-bold text-dark text-wrap"
                            style="font-size: 0.95rem;">{{ Str::limit(strip_tags($products->name_product), 30) ?: '-' }}</span>
                        <small class="text-muted" style="font-size: 0.75rem;">SKU :
                            {{ Str::limit(strip_tags($products->sku), 20) ?: '-' }}</small>
                        <div class="flex-inlane mt-1">
                            <span class="badge bg-label-primary px-2 py-1"
                                style="font-size: 0.65rem;">{{ $products->category->name ?? '-' }}</span>
                            <span class="badge bg-label-secondary px-2 py-1"
                                style="font-size: 0.65rem;">{{ $products->brand->name ?? '-' }}</span>
                        </div>
                    </div>
                </div>

                {{-- Informasi Detail (Badges & Harga) --}}
                <div class="row g-2 mt-2">
                    <div class="col-6 text-start">
                        <small class="text-muted d-block mb-1" style="font-size: 0.7rem;">HPP</small>
                        <span class="text-danger fw-bold" style="font-size: 1rem;">Rp
                            {{ number_format($products->harga_beli, 0, ',', '.') }}</span>
                        <small class="text-muted" style="font-size: 0.7rem;"> /
                            {{ $products->unit->name ?? 'Unit' }}</small>
                    </div>
                    <div class="col-6 text-start">
                        <small class="text-muted d-block mb-1" style="font-size: 0.7rem;">Harga Jual</small>
                        <span class="text-success fw-bold" style="font-size: 1rem;">Rp
                            {{ number_format($products->harga_jual, 0, ',', '.') }}</span>
                        <small class="text-muted" style="font-size: 0.7rem;"> /
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
<div class="d-flex justify-content-center mt-3">
    {{ $produk->links() }}
</div>

<script>
    // Inisialisasi tooltip Bootstrap jika belum ada
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    })
</script>
