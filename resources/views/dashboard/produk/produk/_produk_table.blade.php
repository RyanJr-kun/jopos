<div class="table-responsive p-0 mt-3">
    <table class="table table-hover align-items-center pb-3" id="tableData">
        <thead>
            <tr class="table-secondary">
                <th class="text-uppercase text-dark text-xs font-weight-bolder">Produk</th>
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
            @forelse ($produk as $produks)
                <tr>
                    <td>
                        <div title="gambar & nama produk" class="d-flex px-2 py-1">
                            <div>
                                @if ($produks->img_produk)
                                    <img src="{{ asset('storage/' . $produks->img_produk) }}"
                                        class="avatar avatar-lg me-3" alt="{{ $produks->nama_produk }}">
                                @else
                                    <img src="{{ asset('assets/img/produk.png') }}" class="avatar avatar-lg me-3"
                                        alt="Gambar produk default">
                                @endif
                            </div>
                            <div class="d-flex flex-column justify-content-start">
                                <h6 class="mb-0 text-sm">{{ $produks->nama_produk }}</h6>
                                <p title="SKU" class="text-xs fw-bold mb-0">SKU : {{ $produks->sku }}
                                </p>
                                <p title="Barcode" class="text-xs fw-bold mb-0">Barcode : {{ $produks->barcode }}
                                </p>
                            </div>
                        </div>
                    </td>

                    <td>
                        <p title="kategori produk" class="text-xs text-dark fw-bold mb-0 ">
                            {{ $produks->kategori_produk->nama }}</p>
                    </td>
                    <td>
                        <p title="nama brand/merek poduk" class="text-xs text-dark fw-bold mb-0 ">
                            {{ $produks->brand->nama }}</p>
                    </td>
                    <td>
                        <p title="harga jual" class="text-xs text-dark fw-bold mb-0">{{ $produks->harga_formatted }}</p>
                    </td>
                    <td>
                        <p title="jenis unit" class="text-xs text-dark fw-bold mb-0">{{ $produks->unit->nama }}</p>
                    </td>
                    <td>
                        <span title="Jumlah Barang" class="text-dark text-xs fw-bold ">{{ $produks->qty }}</span>
                    </td>

                    <td>
                        <div title="foto & nama user" class="d-flex align-items-center px-2 py-1">
                            @if ($produks->user->img_user)
                                <img src="{{ asset('storage/' . $produks->user->img_user) }}"
                                    class="avatar avatar-sm me-3" alt="user_img">
                            @else
                                <img src="{{ asset('assets/img/user.webp') }}" class="avatar avatar-sm me-3"
                                    alt="Gambar produk default">
                            @endif
                            <h6 class="mb-0 text-sm">{{ $produks->user->nama }}</h6>
                        </div>
                    </td>

                    <td class="align-middle pe-3">
                        <a href="{{ route('produk.show', $produks->slug) }}" class="text-dark" data-toggle="tooltip"
                            data-original-title="Detail produk">
                            <i class="fa fa-eye text-dark text-sm opacity-10"></i>
                        </a>
                        <a href="{{ route('produk.edit', $produks->slug) }}" class="text-dark mx-3"
                            data-toggle="tooltip" data-original-title="Edit produk">
                            <i class="fa fa-pen-to-square text-dark text-sm opacity-10"></i>
                        </a>
                        <a href="#" class="text-dark delete-product-btn" data-bs-toggle="modal"
                            data-bs-target="#deleteConfirmationModal" data-product-slug="{{ $produks->slug }}"
                            data-product-name="{{ $produks->nama_produk }}" title="Hapus produk">
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
