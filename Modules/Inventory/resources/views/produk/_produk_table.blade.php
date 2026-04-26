<div class="table-responsive p-0 mt-3">
    <table class="table table-hover align-items-center pb-3" id="tableData">
        <thead>
            <tr class="table-secondary">
                <th class="text-uppercase text-dark text-xs font-weight-bolder">Product</th>
                <th class="text-uppercase text-dark text-xs font-weight-bolder ps-2">Kategori</th>
                <th class="text-uppercase text-dark text-xs font-weight-bolder ps-2">Brand</th>
                <th class="text-uppercase text-dark text-xs font-weight-bolder ps-2">Harga Jual</th>
                <th class="text-uppercase text-dark text-xs font-weight-bolder ps-2">Unit</th>
                <th class="text-uppercase text-dark text-xs font-weight-bolder ps-2">Qty</th>
                <th class="text-uppercase text-dark text-xs font-weight-bolder">Pembuat</th>
                <th class="text-dark"></th>
            </tr>
        </thead>
        <tbody id="isiTable">
            @forelse ($produk as $products)
                <tr>
                    <td>
                        <div title="gambar & name produk" class="d-flex px-2 py-1">
                            <div>
                                @if ($products->img_produk)
                                    <img src="{{ asset('storage/' . $products->img_produk) }}"
                                        class="avatar avatar-lg me-3" alt="{{ $products->name_product }}">
                                @else
                                    <img src="{{ asset('assets/img/produk.png') }}" class="avatar avatar-lg me-3"
                                        alt="Gambar produk default">
                                @endif
                            </div>
                            <div class="d-flex flex-column justify-content-start">
                                <h6 class="mb-0 text-sm">{{ $products->name_product }}</h6>
                                <p title="SKU" class="text-xs fw-bold mb-0">SKU : {{ $products->sku }}
                                </p>
                                <p title="Barcode" class="text-xs fw-bold mb-0">Barcode : {{ $products->barcode }}
                                </p>
                            </div>
                        </div>
                    </td>

                    <td>
                        <p title="kategori produk" class="text-xs text-dark fw-bold mb-0 ">
                            {{ $products->category->name }}</p>
                    </td>
                    <td>
                        <p title="name brand/merek poduk" class="text-xs text-dark fw-bold mb-0 ">
                            {{ $products->brand->name }}</p>
                    </td>
                    <td>
                        <p title="harga jual" class="text-xs text-dark fw-bold mb-0">{{ $products->harga_formatted }}
                        </p>
                    </td>
                    <td>
                        <p title="type unit" class="text-xs text-dark fw-bold mb-0">{{ $products->unit->name }}</p>
                    </td>
                    <td>
                        <span title="Jumlah Barang" class="text-dark text-xs fw-bold ">{{ $products->qty }}</span>
                    </td>

                    <td>
                        <div title="foto & name user" class="d-flex align-items-center px-2 py-1">
                            @if ($products->user->avatar)
                                <img src="{{ asset('storage/' . $products->user->avatar) }}"
                                    class="avatar avatar-sm me-3" alt="user_img">
                            @else
                                <img src="{{ asset('assets/img/user.webp') }}" class="avatar avatar-sm me-3"
                                    alt="Gambar produk default">
                            @endif
                            <h6 class="mb-0 text-sm">{{ $products->user->name }}</h6>
                        </div>
                    </td>

                    <td class="align-middle pe-3">
                        <a href="{{ route('produk.show', $products->slug) }}" class="text-dark" data-toggle="tooltip"
                            data-original-title="Detail produk">
                            <i class="bx bx-eye text-dark text-sm opacity-10"></i>
                        </a>
                        <a href="{{ route('produk.edit', $products->slug) }}" class="text-dark mx-3"
                            data-toggle="tooltip" data-original-title="Edit produk">
                            <i class="bx bx-edit text-dark text-sm opacity-10"></i>
                        </a>
                        <a href="#" class="text-dark delete-product-btn" data-bs-toggle="modal"
                            data-bs-target="#deleteConfirmationModal" data-product-slug="{{ $products->slug }}"
                            data-product-name="{{ $products->name_product }}" title="Hapus produk">
                            <i class="bx bx-trash text-dark text-sm opacity-10"></i>
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center py-4">
                        <p class="text-dark text-sm fw-bold mb-0">Data tidak ditemukan.</p>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
    <div class="my-3 ms-3">{{ $produk->links() }}</div>
</div>
