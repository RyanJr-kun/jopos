<div class="table-responsive p-0 mt-3 d-none d-md-block">
    <table class="table table-hover align-items-center justify-content-start mb-0" id="tableData">
        <thead class="bg-label-light">
            <tr>
                <th width="5%">No</th>
                <th>Nama</th>
                <th>slug</th>
                <th class="text-center">JML Product</th>
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
                                <img src="{{ Storage::url($brand->img_brand) }}" class="avatar avatar-lg me-3"
                                    alt="{{ $brand->name }}">
                            @else
                                <img src="{{ asset('assets/img/produk.png') }}" class="avatar avatar-lg me-3"
                                    alt="Gambar produk default">
                            @endif
                            <h6 class="mb-0 text-sm">{{ $brand->name }}</h6>
                        </div>
                    </td>
                    <td>{{ $brand->slug }}</td>
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

                    <td class="text-center">
                        <a href="#" class="action-btn text-secondary" data-bs-toggle="modal"
                            data-bs-target="#editModal" data-url="{{ route('brand.getjson', $brand->slug) }}"
                            data-update-url="{{ route('brand.update', $brand->slug) }}" title="Edit brand">
                            <i class="bx bx-edit"></i>
                        </a>
                        <a href="#" class="action-btn text-danger" data-bs-toggle="modal"
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
        <div class="card mb-3 shadow-none border" id="brand-card-{{ $brand->slug }}">
            <div class="card-body p-3 d-flex">
                <div class="flex-shrink-0 me-3">
                    @if ($brand->img_brand)
                        <img src="{{ Storage::url($brand->img_brand) }}" class="rounded shadow-sm"
                            alt="{{ $brand->name }}" style="width: 85px; height: 85px; object-fit: cover;">
                    @else
                        <img src="{{ asset('assets/img/produk.png') }}" class="rounded shadow-sm"
                            alt="Gambar produk default" style="width: 85px; height: 85px; object-fit: cover;">
                    @endif
                </div>
                <div class="d-flex flex-column justify-content-between w-100">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="mb-0 fw-bold text-dark">{{ $brand->name }}</h6>
                            <small class="text-muted d-block mt-1" style="font-size: 0.75rem;">
                                Total Produk: <span class="fw-semibold text-dark">{{ $brand->products_count }}</span>
                            </small>
                        </div>
                        <div>
                            @if ($brand->status)
                                <span class="badge bg-label-success">Aktif</span>
                            @else
                                <span class="badge bg-label-secondary">Tidak Aktif</span>
                            @endif
                        </div>
                    </div>
                    <div class="d-flex justify-content-end mt-2">
                        <a href="#" class="me-2" data-bs-toggle="modal" data-bs-target="#editModal"
                            data-url="{{ route('brand.getjson', $brand->slug) }}"
                            data-update-url="{{ route('brand.update', $brand->slug) }}" title="Edit brand">
                            <div
                                class="avatar avatar-xs d-flex align-items-center justify-content-center bg-label-primary rounded">
                                <i class="bx bx-edit fs-6"></i>
                            </div>
                        </a>
                        <a href="#" data-bs-toggle="modal" data-bs-target="#deleteConfirmationModal"
                            data-brand-slug="{{ $brand->slug }}" data-brand-name="{{ $brand->name }}"
                            title="Hapus Brand">
                            <div
                                class="avatar avatar-xs d-flex align-items-center justify-content-center bg-label-danger rounded">
                                <i class="bx bx-trash fs-6"></i>
                            </div>
                        </a>
                    </div>

                </div>
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
