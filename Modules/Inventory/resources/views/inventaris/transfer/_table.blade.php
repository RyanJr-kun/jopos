@php
    $statusConfig = [
        'draft' => ['label' => 'Draft', 'class' => 'bg-label-secondary', 'icon' => 'bx-edit-alt'],
        'dikirim' => ['label' => 'Dikirim', 'class' => 'bg-label-warning', 'icon' => 'bx-package'],
        'diterima' => ['label' => 'Diterima', 'class' => 'bg-label-success', 'icon' => 'bx-check-double'],
        'diterima_sebagian' => ['label' => 'Diterima Sebagian', 'class' => 'bg-label-info', 'icon' => 'bx-error'],
        'ditolak' => ['label' => 'Ditolak', 'class' => 'bg-label-danger', 'icon' => 'bx-x-circle'],
        'dibatalkan' => ['label' => 'Dibatalkan', 'class' => 'bg-label-dark', 'icon' => 'bx-block'],
    ];
@endphp

<div class="table-responsive text-nowrap">
    <table class="table table-hover align-middle mb-0">
        <thead class="table-secondary text-dark">
            <tr>
                <th class="text-xs font-weight-bolder py-3 ps-4">Kode Transfer</th>
                <th class="text-xs font-weight-bolder py-3">Toko Asal</th>
                <th class="text-xs font-weight-bolder py-3"></th>
                <th class="text-xs font-weight-bolder py-3">Toko Tujuan</th>
                <th class="text-xs font-weight-bolder py-3">Tanggal Kirim</th>
                <th class="text-xs font-weight-bolder text-center py-3">Item</th>
                <th class="text-xs font-weight-bolder text-center py-3">Status</th>
                <th class="text-xs font-weight-bolder text-center py-3" width="10%">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($transfers as $transfer)
                @php $badge = $statusConfig[$transfer->status] ?? ['label' => $transfer->status, 'class' => 'bg-label-secondary', 'icon' => 'bx-question-mark']; @endphp
                <tr>
                    <td class="ps-4">
                        <div class="d-flex align-items-center">
                            <span class="avatar avatar-sm me-2">
                                <span class="avatar-initial rounded bg-label-primary">
                                    <i class="bx bx-transfer"></i>
                                </span>
                            </span>
                            <div>
                                <span class="fw-semibold text-sm d-block">{{ $transfer->kode_transfer }}</span>
                                <span class="text-xs text-muted">oleh
                                    {{ $transfer->userKirim->username ?? 'N/A' }}</span>
                            </div>
                        </div>
                    </td>
                    <td>
                        <span class="badge bg-label-secondary">{{ $transfer->storeAsal->name_toko ?? 'N/A' }}</span>
                    </td>
                    <td class="text-center text-muted">
                        <i class="bx bx-right-arrow-alt fs-5"></i>
                    </td>
                    <td>
                        <span class="badge bg-label-secondary">{{ $transfer->storeTujuan->name_toko ?? 'N/A' }}</span>
                    </td>
                    <td>
                        <p class="text-sm mb-0">{{ $transfer->tanggal_kirim->translatedFormat('d M Y') }}</p>
                        <p class="text-xs text-muted mb-0">{{ $transfer->tanggal_kirim->format('H:i') }}</p>
                    </td>
                    <td class="text-center">
                        <span class="badge bg-label-dark rounded-pill">
                            {{ $transfer->details_count ?? $transfer->details->count() }} item
                        </span>
                    </td>
                    <td class="text-center">
                        <span class="badge {{ $badge['class'] }}">
                            <i class="bx {{ $badge['icon'] }} me-1"></i>{{ $badge['label'] }}
                        </span>
                    </td>
                    <td class="text-center">
                        <div class="d-flex justify-content-center gap-1">
                            <a href="{{ route('stok-transfer.show', $transfer->kode_transfer) }}"
                                class="action-btn text-blue" data-bs-toggle="tooltip" title="Lihat Detail">
                                <i class="bx bx-show"></i>
                            </a>
                            @can('approve-stok-transfer')
                                @if ($transfer->status === 'dikirim')
                                    <a href="{{ route('stok-transfer.show', $transfer->kode_transfer) }}#approval"
                                        class="btn btn-icon btn-sm btn-outline-success" data-bs-toggle="tooltip"
                                        title="Terima Barang">
                                        <i class="bx bx-package"></i>
                                    </a>
                                @endif
                            @endcan
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8">
                        <div class="d-flex flex-column justify-content-center align-items-center">
                            <i class="bx bx-transfer-alt fs-1 text-muted d-block mb-2"></i>
                            <p class="text-sm fw-bold mb-0">Tidak ada riwayat transfer stok ditemukan.</p>
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="d-flex justify-content-between align-items-center px-4 py-3">
    <p class="text-sm text-muted mb-0">
        Menampilkan {{ $transfers->firstItem() ?? 0 }}-{{ $transfers->lastItem() ?? 0 }}
        dari {{ $transfers->total() }} data
    </p>
    {{ $transfers->links() }}
</div>
