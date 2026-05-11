<div class="table-responsive p-0 mt-3">
    <table class="table table-hover align-items-center justify-content-start mb-0" id="tableData">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th>Nama</th>
                <th class="text-center">Jumlah Product</th>
                <th class="text-center">status</th>
                <th></th>
            </tr>
        </thead>
        <tbody id="isiTable">
            @foreach ($brands as $key => $brand)
                <tr id="brand-row-{{ $brand->slug }}">
                    <td>{{ ++$key }}</td>
                    <td>
                        <div class="d-flex align-items-center">
                            @if ($brand->img_brand)
                                <img src="{{ Storage::url($brand->img_brand) }}" class="avatar avatar-sm me-3"
                                    alt="{{ $brand->name }}">
                            @else
                                <img src="{{ asset('assets/img/produk.png') }}" class="avatar avatar-sm me-3"
                                    alt="Gambar produk default">
                            @endif
                            <h6 class="mb-0 text-sm">{{ $brand->name }}</h6>
                        </div>
                    </td>
                    <td class="align-middle text-center">
                        <small>{{ $brand->products_count }}</small>
                    </td>
                    <td class="align-middle text-center text-sm">
                        @if ($brand->status)
                            <span class="badge bg-label-success">Aktif</span>
                        @else
                            <span class="badge bg-label-secondary">Tidak Aktif</span>
                        @endif
                    </td>

                    <td class="align-middle">

                        <a href="#" class="text-dark fw-bold px-3 text-xs" data-bs-toggle="modal"
                            data-bs-target="#editModal" data-url="{{ route('brand.getjson', $brand->slug) }}"
                            data-update-url="{{ route('brand.update', $brand->slug) }}" title="Edit brand">
                            <i class="bx bx-edit text-dark text-sm opacity-10"></i>
                        </a>
                        <a href="#" class="text-dark delete-btn" data-bs-toggle="modal"
                            data-bs-target="#deleteConfirmationModal" data-brand-slug="{{ $brand->slug }}"
                            data-brand-name="{{ $brand->name }}" title="Hapus Unit">
                            <i class="bx bx-trash"></i>
                        </a>
                    </td>
                </tr>
            @endforeach
            @if ($brands->isEmpty())
                <tr id="brand-row-empty">
                    <td colspan="5" class="text-center py-4">
                        <p class="text-dark text-sm fw-bold mb-0">Belum ada data brand.</p>
                    </td>
                </tr>
            @endif
        </tbody>
    </table>
    <div class="my-3 ms-3">{{ $brands->onEachSide(1)->links() }}</div>
</div>
