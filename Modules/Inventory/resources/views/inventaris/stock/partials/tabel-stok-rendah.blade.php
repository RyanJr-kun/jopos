<div class="table-responsive p-0">
    <table class="table table-hover align-items-center mb-0">
        <thead class="bg-label-light">
            <tr>
                <th rowspan="2" width="5%" class="text-muted text-poppins text-center">No</th>
                <th rowspan="2" width="25%" class="text-muted text-poppins ">Product</th>
                <th rowspan="2" width="16%" class="text-muted text-poppins ">Supplier</th>
                <th colspan="2" width="6%" class="text-muted text-poppins text-center border border-light py-1">
                    Stock
                </th>

                <th rowspan="2" width="14%" class="text-muted text-poppins ">HPP</th>
                <th rowspan="2" width="14%" class="text-muted text-poppins ">Harga Jual</th>
                <th rowspan="2" width="15%" class="text-muted text-poppins text-center">Terjual
                </th>
                <th rowspan="2" width="5%" class="text-muted text-poppins "></th>
            </tr>
            <tr>
                <th class="text-center border py-1" width="3%">Tersedia</th>
                <th class="text-center border py-1" width="3%">Minimum</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($products as $key => $produk)
                <tr>
                    <td class="text-center">{{ ++$key }}</td>
                    <td>
                        <div class="d-flex align-items-center px-2 py-1">
                            <div>
                                <img src="{{ $produk->img_produk ? Storage::url($produk->img_produk) : asset('assets/img/produk.png') }}"
                                    class="rounded me-3 border" style="width: 50px; height: 50px; object-fit: cover;"
                                    alt="produk image">
                            </div>
                            <div class="d-flex flex-column justify-content-center">
                                <h6 class="mb-1 text-sm fw-bold text-dark">{{ $produk->name_product }}</h6>
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
                        <span class="badge bg-label-light">{{ $produk->total_stock }}</span>
                    </td>
                    <td class="text-center">
                        <span class="badge badge-sm bg-label-secondary">{{ $produk->stok_minimum }}</span>
                    </td>
                    <td class="text-sm font-weight-bold">Rp {{ number_format($produk->harga_beli, 0, ',', '.') }}</td>
                    <td class="text-sm font-weight-bold">Rp {{ number_format($produk->harga_jual, 0, ',', '.') }}</td>
                    <td>
                        @if ($produk->last_sale_date)
                            <span
                                class="text-sm">{{ \Carbon\Carbon::parse($produk->last_sale_date)->translatedFormat('d M Y') }}</span>
                        @else
                            <span class="text-xs text-muted fst-italic"> -</span>
                        @endif
                    </td>
                    <td class="align-middle">
                        <a href="{{ route('pembelian.create') }}" class="mb-0 me-3" data-bs-toggle="tooltip"
                            data-bs-placement="top" title="Beli Product Ini">
                            <i class="bx bx-cart-plus-fill bi-sm"></i>
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center py-4">
                        <p class="text-muted mb-0 text-sm fw-bolder">Produk Tidak di temukan</p>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="d-flex justify-content-center mt-4 custom-pagination">
    {{ $products->links() }}
</div>
