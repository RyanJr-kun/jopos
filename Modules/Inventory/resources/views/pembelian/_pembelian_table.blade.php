<div class="table-responsive text-nowrap p-0">
    <table class="table table-hover align-middle mb-0">
        <thead class="table-secondary text-dark border-top">
            <tr>
                <th>Detail Transaksi</th>
                <th>Keuangan</th>
                <th class="text-center">Status</th>
                <!-- Kolom Pembuat disembunyikan di HP (d-none), muncul di tablet ke atas (d-md-table-cell) -->
                <th>Pembuat</th>
                <th></th>
            </tr>
        </thead>
        <tbody class="table-border-bottom-0">
            @forelse ($pembelian as $item)
                <tr>
                    <!-- 1. DETAIL TRANSAKSI (Gabungan Supplier, Invoice, Tanggal) -->
                    <td>
                        <div class="d-flex flex-column">
                            <span
                                class="fw-bold text-dark text-sm mb-1">{{ $item->supplier->name ?? 'Supplier Dihapus' }}</span>
                            <small class="text-muted mb-1"><i
                                    class="bx bx-receipt text-xs me-1"></i>{{ $item->referensi }}</small>
                            <small class="text-muted"><i
                                    class="bx bx-calendar text-xs me-1"></i>{{ \Carbon\Carbon::parse($item->tanggal_pembelian)->translatedFormat('d M Y') }}</small>
                        </div>
                    </td>

                    <!-- 2. KEUANGAN (Gabungan Total, Dibayar, Sisa) -->
                    <td>
                        <div class="d-flex flex-column">
                            <span class="text-primary text-sm fw-bold mb-1" title="Total Akhir">
                                Rp {{ number_format($item->total_akhir, 0, ',', '.') }}
                            </span>
                            <small class="text-muted mb-1" title="Dibayar">
                                Bayar: Rp {{ number_format($item->jumlah_dibayar, 0, ',', '.') }}
                            </small>

                            @if ($item->sisa_hutang > 0)
                                <small class="text-danger fw-semibold" title="Sisa Hutang">
                                    Sisa: Rp {{ number_format($item->sisa_hutang, 0, ',', '.') }}
                                </small>
                            @else
                                <small class="text-success fw-semibold" title="Lunas">Sisa: Rp 0</small>
                            @endif
                        </div>
                    </td>

                    <!-- 3. STATUS (Gabungan Status Barang & Pembayaran) -->
                    <td class="text-center">
                        <div class="d-flex flex-column gap-2 align-items-center">
                            <!-- Status Barang -->
                            <span
                                class="badge {{ $item->status_barang == 'Diterima' ? 'bg-label-success' : ($item->status_barang == 'Dibatalkan' ? 'bg-label-danger' : 'bg-label-warning') }}"
                                style="font-size: 0.7rem; width: 110px;">
                                <i class="bx bx-box text-xs me-1"></i> {{ $item->status_barang }}
                            </span>

                            <!-- Status Pembayaran -->
                            @php
                                $statusClass =
                                    $item->status_pembayaran == 'Lunas'
                                        ? 'bg-label-success'
                                        : ($item->status_pembayaran == 'Belum Lunas'
                                            ? 'bg-label-warning'
                                            : 'bg-label-danger');
                            @endphp
                            <span class="badge {{ $statusClass }}" style="font-size: 0.7rem; width: 110px;">
                                <i class="bx bx-wallet text-xs me-1"></i> {{ $item->status_pembayaran }}
                            </span>
                        </div>
                    </td>

                    <!-- 4. PEMBUAT (Hanya tampil di layar menengah/besar) -->
                    <td class="d-none d-md-table-cell">
                        <div class="d-flex align-items-center">
                            @if ($item->user && $item->user->employeeProfile && $item->user->employeeProfile->avatar)
                                <img src="{{ asset('storage/' . $item->user->employeeProfile->avatar) }}"
                                    class="avatar avatar-sm rounded-circle me-2" alt="user_img">
                            @else
                                <!-- Fallback Avatar Inisial yang lebih modern ala Sneat -->
                                <div class="avatar avatar-sm me-2">
                                    <span
                                        class="avatar-initial rounded-circle bg-label-info">{{ substr($item->user->name ?? 'U', 0, 1) }}</span>
                                </div>
                            @endif
                            <div class="d-flex flex-column">
                                <span
                                    class="text-sm text-dark fw-semibold">{{ $item->user->name ?? 'User Dihapus' }}</span>
                            </div>
                        </div>
                    </td>

                    <!-- 5. AKSI (Menggunakan Dropdown agar hemat tempat di HP) -->
                    <td class="text-center">
                        <div class="dropdown">
                            <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                <i class="bx bx-dots-vertical-rounded"></i>
                            </button>
                            <div class="dropdown-menu">
                                <a class="dropdown-item" href="{{ route('pembelian.show', $item->referensi) }}">
                                    <i class="bx bx-show-alt me-2 text-info"></i> Lihat Detail
                                </a>
                                <a class="dropdown-item" href="{{ route('pembelian.edit', $item->referensi) }}">
                                    <i class="bx bx-edit-alt me-2 text-warning"></i> Edit Pembelian
                                </a>
                                @if ($item->status_pembayaran != 'Dibatalkan')
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item text-danger" href="#" data-bs-toggle="modal"
                                        data-bs-target="#cancelConfirmationModal"
                                        data-pembelian-referensi="{{ $item->referensi }}">
                                        <i class="bx bx-ban me-2"></i> Batalkan
                                    </a>
                                @endif
                            </div>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center py-5">
                        <div class="d-flex flex-column align-items-center">
                            <i class="bx bx-folder-open display-4 text-muted mb-3"></i>
                            <h6 class="text-dark fw-bold mb-0">Belum ada data pembelian.</h6>
                            <small class="text-muted">Transaksi yang Anda buat akan muncul di sini.</small>
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Pagination -->
<div class="d-flex justify-content-center justify-content-md-end mt-4 px-3">
    {{ $pembelian->links() }}
</div>
