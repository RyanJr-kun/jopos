{{-- resources/views/dashboard/penjualan/_pelanggan_table.blade.php --}}
<div class="table-responsive p-0 d-none d-md-block">
    <table class="table table-hover align-items-center justify-content-start mb-0" id="tableData">
        <thead>
            <tr class="table-secondary">
                <th class="text-center">No</th>
                <th class="">Nama</th>
                <th class=" ps-2">Kontak</th>
                <th class=" ps-2">Email</th>
                <th class=" ps-2">Alamat</th>
                <th class="text-center ">Status</th>
                <th class="text-dark"></th>
            </tr>
        </thead>
        <tbody id="isiTable">
            @forelse ($customers as $key => $pelanggan)
                <tr id="pelanggan-row-{{ $pelanggan->id }}">
                    <td class="text-center">{{ ++$key }}</td>
                    <td>
                        <p title="Nama Customer" class="ms-3 text-xs text-dark mb-0">{{ $pelanggan->name }}</p>
                    </td>
                    <td>
                        <p title="Kontak" class="text-xs text-dark mb-0">{{ $pelanggan->kontak }}</p>
                    </td>
                    <td>
                        <p title="Email" class="text-xs text-dark mb-0">{{ $pelanggan->email }}</p>
                    </td>
                    <td>
                        <p title="Alamat" class="text-xs text-dark mb-0">{!! wordwrap($pelanggan->alamat, 50, "<br>\n", true) !!}</p>
                    </td>

                    <td class="align-middle text-center text-sm">
                        @if ($pelanggan->status)
                            <span class="badge bg-label-success">Aktif</span>
                        @else
                            <span class="badge bg-label-secondary">Tidak Aktif</span>
                        @endif
                    </td>

                    <td class="text-center align-middle">
                        @if ($pelanggan->id != 1)
                        <div class="dropdown">
                            <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown"><i class="bx bx-dots-vertical-rounded fs-4"></i></button>
                            <div class="dropdown-menu dropdown-menu-end">
                                <a href="#" class="dropdown-item text-blue"><i
                                        class="bx bx-show me-2"></i> Detail</a>
                                <a href="#" class="dropdown-item text-secondary" data-bs-toggle="modal"
                                data-bs-target="#editModal" data-url="{{ route('pelanggan.getjson', $pelanggan->id) }}"
                                        data-update-url="{{ route('pelanggan.update', $pelanggan->id) }}"><i
                                        class="bx bx-edit me-2"></i> Edit Data</a>
                                <a href="#" class="dropdown-item text-dager" data-bs-toggle="modal"
                                        data-bs-target="#deleteConfirmationModal" data-pelanggan-id="{{ $pelanggan->id }}"
                                        data-pelanggan-name="{{ $pelanggan->name }}"><i class="bx bx-trash me-2"></i>
                                    delete</a>
                            </div>
                        </div>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center py-4 text-muted">Data pelanggan tidak ditemukan.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    <div class="mt-3 px-3">
        {{ $customers->links() }}
    </div>
</div>
<div class="d-block d-md-none">
    @forelse ($customers as $key => $pelanggan)
        <div class="card mx-2 mb-3">
            <div class="card-body pb-2">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="badge bg-label-secondary">No. {{ ++$key }}</span>
                    <div class="d-flex">
                        <div class="">
                            @if ($pelanggan->status)
                                <span class="badge bg-label-success rounded-pill"><i
                                        class='bx bx-check me-1'></i>Aktif</span>
                            @else
                                <span class="badge bg-label-secondary rounded-pill">Nonaktif</span>
                            @endif
                        </div>
                        @if ($pelanggan->id != 1)
                        <div class="dropdown ms-3">
                            <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown"><i class="bx bx-dots-vertical-rounded fs-4"></i></button>
                            <div class="dropdown-menu dropdown-menu-end">
                                <a href="#" class="dropdown-item text-blue"><i
                                        class="bx bx-show me-2"></i> Detail</a>
                                <a href="#" class="dropdown-item text-secondary" data-bs-toggle="modal"
                                data-bs-target="#editModal" data-url="{{ route('pelanggan.getjson', $pelanggan->id) }}"
                                        data-update-url="{{ route('pelanggan.update', $pelanggan->id) }}"><i
                                        class="bx bx-edit me-2"></i> Edit Data</a>
                                <a href="#" class="dropdown-item text-danger" data-bs-toggle="modal"
                                        data-bs-target="#deleteConfirmationModal" data-pelanggan-id="{{ $pelanggan->id }}"
                                        data-pelanggan-name="{{ $pelanggan->name }}"><i class="bx bx-trash me-2"></i>
                                    delete</a>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
                <div class="d-flex align-items-start justify-content-between mb-3">
                    <div class="d-flex align-items-center gap-3">
                        {{-- Avatar --}}
                        @if ($pelanggan?->avatar)
                            <img src="{{ Storage::url($pelanggan->avatar) }}"
                                class="rounded-circle shadow-sm"
                                style="width:45px;height:45px;object-fit:cover;"
                                alt="{{ $pelanggan->name }}">
                        @else
                            <div class="rounded-circle bg-label-primary d-flex align-items-center justify-content-center shadow-sm"
                                style="width:45px;height:45px;font-size:16px;font-weight:700;flex-shrink:0;">
                                {{ strtoupper(substr($pelanggan->name, 0, 2)) }}
                            </div>
                        @endif

                        <div>
                            <h6 class="mb-0 fw-bold text-truncate" style="max-width: 180px;">
                                {{ $pelanggan->name }}</h6>
                            <small class="text-muted">{{ $pelanggan->email }}</small>
                        </div>
                    </div>
                    {{-- Status --}}
                    
                </div>
            </div>
        </div>
    @empty
    @endforelse
</div>