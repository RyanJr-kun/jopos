<div class="table-responsive text-nowrap">
    <table class="table table-hover align-middle mb-0">
        <thead class="table-secondary text-dark">
            <tr>
                <th class="">No.</th>
                <th class="text-xs font-weight-bolder py-3 ps-4">Kode Penyesuaian</th>
                <th class="text-xs font-weight-bolder py-3">Tanggal</th>
                <th class="text-xs font-weight-bolder py-3">User</th>
                <th class="text-xs font-weight-bolder text-center py-3">Item</th>
                <th class="text-xs font-weight-bolder text-center py-3" width="10%">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($stocks as $key => $penyesuaian)
                <tr>
                    <td class="">{{ ++$key }}</td>
                    <td class="ps-4">
                        <div class="d-flex flex-column">
                            @can('view-toko-gudang')
                                <div class="">
                                    <span class="badge bg-label-secondary fs-6">
                                        {{ $penyesuaian->store->name_toko ?? 'N/A' }}
                                    </span>
                                </div>
                            @endcan
                            <span class="fw-semibold text-sm">{{ $penyesuaian->kode_penyesuaian }}</span>
                        </div>
                    </td>

                    <td>
                        <p class="text-sm mb-0">
                            {{ $penyesuaian->tanggal_penyesuaian->translatedFormat('d M Y') }},&nbsp;
                            {{ $penyesuaian->tanggal_penyesuaian->format('H:i') }} WIB</p>
                    </td>
                    <td>
                        <div class="d-flex align-items-center">
                            <span class="avatar avatar-xs me-2">
                                <span class="avatar-initial rounded-circle bg-label-primary">
                                    {{ strtoupper(substr($penyesuaian->user->username ?? '?', 0, 1)) }}
                                </span>
                            </span>
                            <span class="text-sm">{{ $penyesuaian->user->username ?? 'N/A' }}</span>
                        </div>
                    </td>
                    <td class="text-center">
                        <span class="badge bg-label-dark rounded-pill">
                            {{ $penyesuaian->details_count ?? $penyesuaian->details->count() }} item
                        </span>
                    </td>
                    <td class="text-center">
                        <div class="dropdown">
                            <button class="btn p-0" type="button" id="cardOpt{{ $penyesuaian->id }}"
                                data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="bx bx-dots-vertical-rounded fs-4"></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-end"
                                aria-labelledby="cardOpt{{ $penyesuaian->id }}">
                                <a class="dropdown-item text-info"
                                    href="{{ route('stok-penyesuaian.show', $penyesuaian->kode_penyesuaian) }}""><i
                                        class="bx bx-show me-2"></i> Detail</a>

                                @can('delete-stok-penyesuaian')
                                    <button type="button" class="dropdown-item text-danger" data-bs-toggle="modal"
                                        data-bs-target="#cancelConfirmationModal"
                                        data-kode-penyesuaian="{{ $penyesuaian->kode_penyesuaian }}" title="Batalkan">
                                        <i class="bx bx-trash"></i> Delete
                                    </button>
                                @endcan
                            </div>
                        </div>

                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6">
                        <div class="d-flex flex-column align-items-center justify-content-center py-4">
                            <i class="bx bx-package fs-1 text-muted d-block mb-2"></i>
                            <p class="text-sm fw-bold mb-0">Tidak ada riwayat penyesuaian stok
                                ditemukan.
                            </p>
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="d-flex justify-content-between align-items-center px-4 py-3">
    <p class="text-sm text-muted mb-0">
        Menampilkan {{ $stocks->firstItem() ?? 0 }}-{{ $stocks->lastItem() ?? 0 }}
        dari {{ $stocks->total() }} data
    </p>
    {{ $stocks->links() }}
</div>
