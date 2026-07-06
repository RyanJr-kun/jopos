<div class="d-md-none d-block">
    @php $noMobile = 1; @endphp
    @forelse ($products as $produk)
        @if ($produk->variants && $produk->variants->isNotEmpty())
            {{-- PRODUK DENGAN VARIAN --}}
            @foreach ($produk->variants as $variant)
                @php
                    $varStok = $produk->stocks->where('product_variant_id', $variant->id)->sum('qty') ?? 0;
                    $imgUrl = $variant->img_variant
                        ? Storage::url($variant->img_variant)
                        : ($produk->primaryImage && $produk->primaryImage->path
                            ? Storage::url($produk->primaryImage->path)
                            : asset('assets/img/produk.png'));
                @endphp

                @if ($varStok <= $produk->stok_minimum)
                    <div class="card shadow-md product-card m-3">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="me-3">
                                        <img src="{{ $imgUrl }}" class="rounded border"
                                            style="width: 70px; height: 70px; object-fit: cover;"
                                            alt="{{ $produk->name_product }}">
                                    </div>
                                    <div class="d-flex flex-column justify-content-center">
                                        <h6 class="mb-0 text-sm fw-bold text-dark text-poppins">
                                            {{ Str::limit(strip_tags($produk->name_product), 25) ?: '-' }}
                                        </h6>
                                        <small class="text-xs text-muted mb-0 mt-1">
                                            <span
                                                class="badge bg-label-info px-1 py-0 me-1">{{ $variant->label }}</span>
                                            {{ $variant->sku ?? ($produk->sku ?? 'Tidak ada SKU') }}
                                        </small>
                                    </div>
                                </div>
                                <div class="d-flex flex-column align-items-end text-end">
                                    <span class="text-xs text-muted mb-2">No. {{ $noMobile++ }}</span>
                                    <a href="{{ route('pembelian.create') }}"
                                        class="btn btn-sm btn-icon btn-outline-primary" data-bs-toggle="tooltip"
                                        data-bs-placement="top" title="Beli Product Ini">
                                        <i class="bx bx-cart-plus-fill bi-sm"></i>
                                    </a>
                                </div>
                            </div>

                            <hr class="my-2 text-muted">

                            <div class="row g-2 mt-1">
                                <div class="col-12">
                                    <small class="text-xs text-muted d-block mb-1">Supplier</small>
                                    @if ($pemasok = $produk->latestPurchaseItem?->pembelian?->pemasok)
                                        <div class="d-flex align-items-center">
                                            <span class="text-sm fw-bold me-2">{{ $pemasok->name }}</span>
                                            <span class="badge bg-label-secondary text-xs">{{ $pemasok->kontak }}</span>
                                        </div>
                                    @else
                                        <span class="text-xs text-muted fst-italic">Belum ada riwayat</span>
                                    @endif
                                </div>
                                <div class="col-6 mt-2">
                                    <small class="text-xs text-muted d-block mb-1">Stok (Tersedia / Min)</small>
                                    <span class="badge bg-label-danger me-1">{{ $varStok }}</span> /
                                    <span
                                        class="badge badge-sm bg-label-secondary ms-1">{{ $produk->stok_minimum }}</span>
                                </div>
                                <div class="col-6 mt-2">
                                    <small class="text-xs text-muted d-block mb-1">Terakhir Terjual</small>
                                    @if ($produk->last_sale_date)
                                        <span
                                            class="text-sm fw-bold">{{ \Carbon\Carbon::parse($produk->last_sale_date)->translatedFormat('d M Y') }}</span>
                                    @else
                                        <span class="text-xs text-muted fst-italic">-</span>
                                    @endif
                                </div>
                                <div class="col-6 mt-3">
                                    <small class="text-xs text-muted d-block mb-1">HPP</small>
                                    <span class="text-sm fw-bold">Rp
                                        {{ number_format($produk->harga_beli, 0, ',', '.') }}</span>
                                </div>
                                <div class="col-6 mt-3">
                                    <small class="text-xs text-muted d-block mb-1">Harga Jual</small>
                                    <span class="text-sm fw-bold text-primary">Rp
                                        {{ number_format($produk->harga_jual, 0, ',', '.') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            @endforeach
        @else
            {{-- PRODUK TANPA VARIAN --}}
            @php
                $currentStock = $produk->stocks->whereNull('product_variant_id')->sum('qty') ?? 0;
                $imgUrl =
                    $produk->primaryImage && $produk->primaryImage->path
                        ? Storage::url($produk->primaryImage->path)
                        : asset('assets/img/produk.png');
            @endphp

            @if ($currentStock <= $produk->stok_minimum)
                <div class="card shadow-md product-card m-3">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div class="d-flex align-items-center">
                                <div class="me-3">
                                    <img src="{{ $imgUrl }}" class="rounded border"
                                        style="width: 70px; height: 70px; object-fit: cover;"
                                        alt="{{ $produk->name_product }}">
                                </div>
                                <div class="d-flex flex-column justify-content-center">
                                    <h6 class="mb-0 text-sm fw-bold text-dark text-poppins">
                                        {{ Str::limit(strip_tags($produk->name_product), 25) ?: '-' }}
                                    </h6>
                                    <small
                                        class="text-xs text-muted mb-0">{{ $produk->sku ?? 'Tidak ada SKU' }}</small>
                                </div>
                            </div>
                            <div class="d-flex flex-column align-items-end text-end">
                                <span class="text-xs text-muted mb-2">No. {{ $noMobile++ }}</span>
                                <a href="{{ route('pembelian.create') }}"
                                    class="btn btn-sm btn-icon btn-outline-primary" data-bs-toggle="tooltip"
                                    data-bs-placement="top" title="Beli Product Ini">
                                    <i class="bx bx-cart-plus-fill bi-sm"></i>
                                </a>
                            </div>
                        </div>
                        <hr class="my-2 text-muted">
                        <div class="row g-2 mt-1">
                            <div class="col-12">
                                <small class="text-xs text-muted d-block mb-1">Supplier</small>
                                @if ($pemasok = $produk->latestPurchaseItem?->pembelian?->pemasok)
                                    <div class="d-flex align-items-center">
                                        <span class="text-sm fw-bold me-2">{{ $pemasok->name }}</span>
                                        <span class="badge bg-label-secondary text-xs">{{ $pemasok->kontak }}</span>
                                    </div>
                                @else
                                    <span class="text-xs text-muted fst-italic">Belum ada riwayat</span>
                                @endif
                            </div>
                            <div class="col-6 mt-2">
                                <small class="text-xs text-muted d-block mb-1">Stok (Tersedia / Min)</small>
                                <span class="badge bg-label-danger me-1">{{ $currentStock }}</span> /
                                <span class="badge badge-sm bg-label-secondary ms-1">{{ $produk->stok_minimum }}</span>
                            </div>
                            <div class="col-6 mt-2">
                                <small class="text-xs text-muted d-block mb-1">Terakhir Terjual</small>
                                @if ($produk->last_sale_date)
                                    <span
                                        class="text-sm fw-bold">{{ \Carbon\Carbon::parse($produk->last_sale_date)->translatedFormat('d M Y') }}</span>
                                @else
                                    <span class="text-xs text-muted fst-italic">-</span>
                                @endif
                            </div>
                            <div class="col-6 mt-3">
                                <small class="text-xs text-muted d-block mb-1">HPP</small>
                                <span class="text-sm fw-bold">Rp
                                    {{ number_format($produk->harga_beli, 0, ',', '.') }}</span>
                            </div>
                            <div class="col-6 mt-3">
                                <small class="text-xs text-muted d-block mb-1">Harga Jual</small>
                                <span class="text-sm fw-bold text-primary">Rp
                                    {{ number_format($produk->harga_jual, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        @endif
    @empty
        <div class="card border-0 shadow-none bg-label-light m-3">
            <div class="card-body text-center py-5">
                <i class="bx bx-box bx-lg text-muted mb-3"></i>
                <p class="text-muted mb-0 text-sm fw-bolder">Produk Tidak Ditemukan</p>
            </div>
        </div>
    @endforelse
</div>


<div class="table-responsive p-0 d-none d-md-block">
    <table class="table table-hover align-items-center mb-0">
        <thead class="bg-label-light">
            <tr>
                <th rowspan="2" width="5%" class="text-muted text-poppins text-center">No</th>
                <th rowspan="2" width="25%" class="text-muted text-poppins ">Product</th>
                <th rowspan="2" width="16%" class="text-muted text-poppins ">Supplier</th>
                <th colspan="2" width="6%"
                    class="text-muted text-poppins text-center border border-light py-1">
                    Stock
                </th>
                <th rowspan="2" width="14%" class="text-muted text-poppins ">HPP</th>
                <th rowspan="2" width="14%" class="text-muted text-poppins ">Harga Jual</th>
                <th rowspan="2" width="15%" class="text-muted text-poppins text-center">Terjual</th>
                <th rowspan="2" width="5%" class="text-muted text-poppins "></th>
            </tr>
            <tr>
                <th class="text-center border py-1" width="3%">Tersedia</th>
                <th class="text-center border py-1" width="3%">Minimum</th>
            </tr>
        </thead>
        <tbody>
            @php $noDesktop = 1; @endphp
            @forelse ($products as $produk)
                @if ($produk->variants && $produk->variants->isNotEmpty())
                    {{-- PRODUK DENGAN VARIAN --}}
                    @foreach ($produk->variants as $variant)
                        @php
                            $varStok = $produk->stocks->where('product_variant_id', $variant->id)->sum('qty') ?? 0;
                            $imgUrl = $variant->img_variant
                                ? Storage::url($variant->img_variant)
                                : ($produk->primaryImage && $produk->primaryImage->path
                                    ? Storage::url($produk->primaryImage->path)
                                    : asset('assets/img/produk.png'));
                        @endphp

                        @if ($varStok <= $produk->stok_minimum)
                            <tr>
                                <td class="text-center">{{ $noDesktop++ }}</td>
                                <td>
                                    <div class="d-flex align-items-center px-2 py-1">
                                        <div class="me-3">
                                            <img src="{{ $imgUrl }}" class="rounded"
                                                style="width: 50px; height: 50px; object-fit: cover;"
                                                alt="{{ $produk->name_product }}">
                                        </div>
                                        <div class="d-flex flex-column justify-content-center">
                                            <h6 class="mb-1 text-sm fw-bold text-dark">
                                                {{ Str::limit(strip_tags($produk->name_product), 15) ?: '-' }}
                                            </h6>
                                            <small class="text-xs text-muted mb-0">
                                                <span
                                                    class="badge bg-label-info px-1 py-0 me-1">{{ $variant->label }}</span>
                                                {{ $variant->sku ?? ($produk->sku ?? '-') }}
                                            </small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if ($pemasok = $produk->latestPurchaseItem?->pembelian?->pemasok)
                                        <div class="d-flex flex-column justify-content-start">
                                            <p class="text-sm font-weight-bold mb-0">{{ $pemasok->name }}</p>
                                            <p class="text-xs text-muted mb-0">{{ $pemasok->kontak }}</p>
                                        </div>
                                    @else
                                        <span class="text-xs text-muted fst-italic">Belum ada riwayat</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-label-danger">{{ $varStok }}</span>
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-sm bg-label-secondary">{{ $produk->stok_minimum }}</span>
                                </td>
                                <td class="text-sm font-weight-bold">Rp
                                    {{ number_format($produk->harga_beli, 0, ',', '.') }}</td>
                                <td class="text-sm font-weight-bold">Rp
                                    {{ number_format($produk->harga_jual, 0, ',', '.') }}</td>
                                <td class="text-center">
                                    @if ($produk->last_sale_date)
                                        <span
                                            class="text-sm">{{ \Carbon\Carbon::parse($produk->last_sale_date)->translatedFormat('d M Y') }}</span>
                                    @else
                                        <span class="text-xs text-muted fst-italic">-</span>
                                    @endif
                                </td>
                                <td class="align-middle">
                                    <a href="{{ route('pembelian.create') }}" class="mb-0 me-3"
                                        data-bs-toggle="tooltip" data-bs-placement="top" title="Beli Product Ini">
                                        <i class="bx bx-cart-plus-fill bi-sm"></i>
                                    </a>
                                </td>
                            </tr>
                        @endif
                    @endforeach
                @else
                    {{-- PRODUK TANPA VARIAN --}}
                    @php
                        $currentStock = $produk->stocks->whereNull('product_variant_id')->sum('qty') ?? 0;
                        $imgUrl =
                            $produk->primaryImage && $produk->primaryImage->path
                                ? Storage::url($produk->primaryImage->path)
                                : asset('assets/img/produk.png');
                    @endphp

                    @if ($currentStock <= $produk->stok_minimum)
                        <tr>
                            <td class="text-center">{{ $noDesktop++ }}</td>
                            <td>
                                <div class="d-flex align-items-center px-2 py-1">
                                    <div class="me-3">
                                        <img src="{{ $imgUrl }}" class="rounded"
                                            style="width: 50px; height: 50px; object-fit: cover;"
                                            alt="{{ $produk->name_product }}">
                                    </div>
                                    <div class="d-flex flex-column justify-content-center">
                                        <h6 class="mb-1 text-sm fw-bold text-dark">
                                            {{ Str::limit(strip_tags($produk->name_product), 15) ?: '-' }}
                                        </h6>
                                        <small class="text-xs text-muted mb-0">{{ $produk->sku ?? '-' }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if ($pemasok = $produk->latestPurchaseItem?->pembelian?->pemasok)
                                    <div class="d-flex flex-column justify-content-start">
                                        <p class="text-sm font-weight-bold mb-0">{{ $pemasok->name }}</p>
                                        <p class="text-xs text-muted mb-0">{{ $pemasok->kontak }}</p>
                                    </div>
                                @else
                                    <span class="text-xs text-muted fst-italic">Belum ada riwayat</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="badge bg-label-danger">{{ $currentStock }}</span>
                            </td>
                            <td class="text-center">
                                <span class="badge badge-sm bg-label-secondary">{{ $produk->stok_minimum }}</span>
                            </td>
                            <td class="text-sm font-weight-bold">Rp
                                {{ number_format($produk->harga_beli, 0, ',', '.') }}</td>
                            <td class="text-sm font-weight-bold">Rp
                                {{ number_format($produk->harga_jual, 0, ',', '.') }}</td>
                            <td class="text-center">
                                @if ($produk->last_sale_date)
                                    <span
                                        class="text-sm">{{ \Carbon\Carbon::parse($produk->last_sale_date)->translatedFormat('d M Y') }}</span>
                                @else
                                    <span class="text-xs text-muted fst-italic">-</span>
                                @endif
                            </td>
                            <td class="align-middle">
                                <a href="{{ route('pembelian.create') }}" class="mb-0 me-3" data-bs-toggle="tooltip"
                                    data-bs-placement="top" title="Beli Product Ini">
                                    <i class="bx bx-cart-plus-fill bi-sm"></i>
                                </a>
                            </td>
                        </tr>
                    @endif
                @endif
            @empty
                <tr>
                    <td colspan="9" class="text-center py-4">
                        <p class="text-muted mb-0 text-sm fw-bolder">Produk Tidak Ditemukan</p>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="d-flex justify-content-center mt-4 custom-pagination">
    {{ $products->links() }}
</div>
