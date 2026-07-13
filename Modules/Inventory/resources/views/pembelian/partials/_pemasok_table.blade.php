<div class="">
    <div class="table-responsive p-0 mt-3 d-none d-md-block">
        <table class="table table-hover align-items-center justify-content-start mb-0" id="tableData">
            <thead>
                <tr>
                    <th width="5%" class="text-center">No</th>
                    <th width="20%">Nama</th>
                    <th>Perusahaan</th>
                    <th>Kontak</th>
                    <th class="text-center">Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody id="isiTable">
                @forelse ($suppliers as $key => $pemasok)
                    <tr id="pemasok-row-{{ $pemasok->id }}">
                        <td class="text-center">{{ ++$key }}</td>
                        <td>{{ $pemasok->name }}</td>
                        <td class="text-uppercase">{{ $pemasok->perusahaan }}</td>
                        <td>
                            <div class="d-flex flex-column">
                                <small class="text-muted" title="Hubungi Kontak ini?" data-bs-toggle="tooltip"
                                    data-bs-placement="top"><a
                                        href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $pemasok->kontak) }}"
                                        target="_blank" title="Kontak"
                                        class="text-secondary">{{ $pemasok->kontak }}</a></small>
                                <small title="Email" class="text-muted">{{ $pemasok->email }}</small>
                            </div>
                        </td>
                        <td class="align-middle text-center text-sm">
                            @if ($pemasok->status)
                                <span class="badge bg-label-success">Aktif</span>
                            @else
                                <span class="badge bg-label-secondary">Tidak Aktif</span>
                            @endif
                        </td>

                        <td class="text-center">
                            <button type="button" class="action-btn text-info popover-detail-btn"
                                data-bs-container="body" data-bs-toggle="popover" data-bs-placement="left"
                                data-bs-html="true"
                                data-bs-title="<span class='fw-bold d-flex align-items-center'><i class='bx bx-info-circle me-2 text-primary'></i> Detail Pemasok</span>"
                                data-bs-content="
                <div class='p-1' style='min-width: 200px;'>
                    <div class='mb-2 pb-2 border-bottom'>
                        <span class='d-block fw-semibold text-dark mb-1'><i class='bx bx-map text-danger me-1'></i> Alamat:</span>
                        <span class='text-muted small'>{{ $pemasok->alamat ?: 'Alamat tidak tersedia' }}</span>
                    </div>
                    <div>
                        <span class='d-block fw-semibold text-dark mb-1'><i class='bx bx-notepad text-warning me-1'></i> Catatan:</span>
                        <span class='text-muted small'>{{ $pemasok->note ?: 'Tidak ada catatan' }}</span>
                    </div>
                </div>
            ">
                                <i class="bx bx-show"></i>
                            </button>
                            @can('edit-pemasok')
                                <a href="#" class="action-btn text-secondary" data-bs-toggle="modal"
                                    data-bs-target="#editModal" data-url="{{ route('pemasok.getjson', $pemasok->id) }}"
                                    data-update-url="{{ route('pemasok.update', $pemasok->id) }}" title="Edit Supplier">
                                    <i class="bx bx-edit"></i>
                                </a>
                            @endcan
                            @can('delete-pemasok')
                                <a href="#" class="action-btn text-danger" data-bs-toggle="modal"
                                    data-bs-target="#deleteConfirmationModal" data-pemasok-id="{{ $pemasok->id }}"
                                    data-pemasok-name="{{ $pemasok->name }}" title="Hapus Supplier">
                                    <i class="bx bx-trash"></i>
                                </a>
                            @endcan
                        </td>
                    </tr>
                @empty
                    <tr id="pemasok-row-empty">
                        <td colspan="7" class="text-center py-4">
                            <p class="text-dark text-sm fw-bold mb-0">Belum ada data pemasok.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="my-3 ms-3">{{ $suppliers->onEachSide(1)->links() }}</div>
    </div>


    <div class="d-block d-md-none ">
        @forelse ($suppliers as $pemasok)
            <div class="card mx-3 shadow-sm border border-secondary">
                <div class="card-body px-3 pt-3 pb-0">
                    <div class="d-flex align-items-center justify-content-between gap-2">
                        <span class="badge bg-label-secondary">No. {{ ++$key }}</span>
                        @if ($pemasok->status)
                            <span class="badge bg-label-success">Aktif</span>
                        @else
                            <span class="badge bg-label-secondary">Tidak Aktif</span>
                        @endif
                    </div>
                    <div class="d-flex align-items-center justify-content-between gap-2 mt-2">
                        <div>
                            <h5 class="mb-0 text-uppercase fw-bold">{{ $pemasok->perusahaan }}</h5>
                            <p class="text-muted fw-semibold">{{ $pemasok->name }}</p>
                        </div>
                        <div class="">
                            <button type="button" class="action-btn text-info popover-detail-btn"
                                data-bs-container="body" data-bs-toggle="popover" data-bs-placement="left"
                                data-bs-html="true"
                                data-bs-title="<span class='fw-bold d-flex align-items-center'><i class='bx bx-info-circle me-2 text-primary'></i> Detail Pemasok</span>"
                                data-bs-content="
                                <div class='p-1' style='min-width: 200px;'>

                                    <div class='d-flex flex-column align-items-center justify-content-start bg-label-success mb-3 rounded-3'>
                                        <p class='text-muted mt-2 mb-0' title='Hubungi Kontak ini?' data-bs-toggle='tooltip' data-bs-placement='top'><a
                                            href='https://wa.me/{{ preg_replace('/[^0-9]/', '', $pemasok->kontak) }}'
                                            target='_blank' title='Kontak'
                                            class='text-success'>{{ $pemasok->kontak }}</a></p>
                                        <p class='text-muted mb-2' title='Email'>{{ $pemasok->email }}</p>
                                    </div>
                                    <div class='mb-2 pb-2 border-bottom'>
                                        <span class='d-block fw-semibold text-dark mb-1'><i
                                                class='bx bx-map text-danger me-1'></i> Alamat:</span>
                                        <span
                                            class='text-muted small'>{{ $pemasok->alamat ?: 'Alamat tidak tersedia' }}</span>
                                    </div>
                                    <div>
                                        <span class='d-block fw-semibold text-dark mb-1'><i
                                                class='bx bx-notepad text-warning me-1'></i> Catatan:</span>
                                        <span class='text-muted small'>{{ $pemasok->note ?: 'Tidak ada catatan' }}</span>
                                    </div>
                                </div>
                                ">
                                <i class="bx bx-show"></i>
                            </button>
                            @can('edit-pemasok')
                                <a href="#" class="action-btn text-secondary" data-bs-toggle="modal"
                                    data-bs-target="#editModal" data-url="{{ route('pemasok.getjson', $pemasok->id) }}"
                                    data-update-url="{{ route('pemasok.update', $pemasok->id) }}" title="Edit Supplier">
                                    <i class="bx bx-edit"></i>
                                </a>
                            @endcan
                            @can('delete-pemasok')
                                <a href="#" class="action-btn text-danger" data-bs-toggle="modal"
                                    data-bs-target="#deleteConfirmationModal" data-pemasok-id="{{ $pemasok->id }}"
                                    data-pemasok-name="{{ $pemasok->name }}" title="Hapus Supplier">
                                    <i class="bx bx-trash"></i>
                                </a>
                            @endcan
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <small class="text-dark text-sm fw-bold mb-0">Belum ada data pemasok.</small>
        @endforelse

    </div>
</div>
