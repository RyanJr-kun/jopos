<div class="table-responsive p-0 mt-3 d-none d-md-block">
    <table class="table table-hover align-items-center justify-content-start mb-0" id="tableData">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th>Nama</th>
                <th class="text-center">Jumlah Product</th>
                <th class="text-center">Status</th>
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
                            data-brand-name="{{ $brand->name }}" title="Hapus Brand">
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
</div>

<div class="d-block d-md-none mt-3 px-3" id="isiCard">
    @foreach ($brands as $key => $brand)
        <div class="card mb-3 shadow-sm border" id="brand-card-{{ $brand->slug }}">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-center">

                    <div class="d-flex align-items-center">
                        @if ($brand->img_brand)
                            <img src="{{ Storage::url($brand->img_brand) }}" class="avatar avatar-md rounded me-3"
                                alt="{{ $brand->name }}" style="object-fit: cover;">
                        @else
                            <img src="{{ asset('assets/img/produk.png') }}" class="avatar avatar-md rounded me-3"
                                alt="Gambar produk default">
                        @endif
                        <div>
                            <h6 class="mb-0 fw-bold text-dark">{{ $brand->name }}</h6>
                            <small class="text-muted d-block mt-1">Total Produk: <span
                                    class="fw-semibold text-dark">{{ $brand->products_count }}</span></small>
                        </div>
                    </div>

                    <div>
                        @if ($brand->status)
                            <span class="badge bg-label-success">Aktif</span>
                        @else
                            <span class="badge bg-label-secondary">Tidak Aktif</span>
                        @endif
                    </div>

                </div>
            </div>

            <div class="card-footer bg-transparent border-top p-2 d-flex justify-content-end align-items-center gap-4">
                <a href="#" class="text-dark fw-bold d-flex align-items-center" data-bs-toggle="modal"
                    data-bs-target="#editModal" data-url="{{ route('brand.getjson', $brand->slug) }}"
                    data-update-url="{{ route('brand.update', $brand->slug) }}" title="Edit brand">
                    <i class="bx bx-edit fs-5 text-primary"></i> <span
                        class="ms-1 text-sm fw-normal text-muted">Edit</span>
                </a>
                <a href="#" class="text-dark delete-btn d-flex align-items-center" data-bs-toggle="modal"
                    data-bs-target="#deleteConfirmationModal" data-brand-slug="{{ $brand->slug }}"
                    data-brand-name="{{ $brand->name }}" title="Hapus Brand">
                    <i class="bx bx-trash fs-5 text-danger"></i> <span
                        class="ms-1 text-sm fw-normal text-muted">Hapus</span>
                </a>
            </div>
        </div>
    @endforeach

    @if ($brands->isEmpty())
        <div class="text-center py-4 bg-lighter rounded border border-dashed mb-3" id="brand-card-empty">
            <i class="bx bx-folder-open display-4 text-muted mb-2"></i>
            <p class="text-dark text-sm fw-bold mb-0">Belum ada data brand.</p>
        </div>
    @endif
</div>

<div class="my-3 ms-3 pb-3 d-flex justify-content-center">
    {{ $brands->onEachSide(1)->links() }}
</div>
