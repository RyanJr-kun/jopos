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

                    <!-- 2. KEUANGAN (Gabungan Total, Dibayar, Sisa, & Progress Bar Hutang) -->
                    <td>
                        @php
                            $persentase = $item->persentase_bayar;
                            $isJatuhTempo = $item->is_overdue;

                            // Tentukan warna Bar
                            $barColor = 'bg-info';
                            if ($item->status_pembayaran == 'Lunas') {
                                $barColor = 'bg-success';
                            } elseif ($item->status_pembayaran == 'Dibatalkan') {
                                $barColor = 'bg-secondary';
                                $persentase = 0;
                            } elseif ($isJatuhTempo) {
                                $barColor = 'bg-danger';
                            } else {
                                $barColor = 'bg-warning';
                            }
                        @endphp

                        <div class="d-flex flex-column" style="min-width: 170px;">
                            <!-- Teks Nominal Atas -->
                            <div class="d-flex justify-content-between text-xs mb-1">
                                <span class="fw-bold text-dark" title="Total Tagihan">Rp
                                    {{ number_format($item->total_akhir, 0, ',', '.') }}</span>
                                @if ($item->sisa_hutang > 0)
                                    <span class="text-danger fw-semibold" title="Sisa Hutang">- Rp
                                        {{ number_format($item->sisa_hutang, 0, ',', '.') }}</span>
                                @else
                                    <span class="text-success fw-semibold" title="Lunas">Lunas</span>
                                @endif
                            </div>

                            <!-- Progress Bar Visual -->
                            <div class="progress shadow-none border mb-1" style="height: 6px;">
                                <div class="progress-bar {{ $barColor }}" role="progressbar"
                                    style="width: {{ $persentase }}%" aria-valuenow="{{ $persentase }}"
                                    aria-valuemin="0" aria-valuemax="100">
                                </div>
                            </div>

                            <!-- Teks Informasi Bawah -->
                            <div class="d-flex justify-content-between align-items-center" style="font-size: 0.7rem;">
                                <span class="text-muted" title="Sudah Dibayar">Bayar: Rp
                                    {{ number_format($item->jumlah_dibayar, 0, ',', '.') }}</span>

                                <!-- Indikator Jatuh Tempo (Hanya tampil jika belum lunas & punya tempo) -->
                                @if ($item->status_pembayaran == 'Belum Lunas' && $item->tanggal_jatuh_tempo)
                                    <span class="{{ $isJatuhTempo ? 'text-danger fw-bold' : 'text-muted' }}">
                                        Tempo: {{ \Carbon\Carbon::parse($item->tanggal_jatuh_tempo)->format('d/m/y') }}
                                    </span>
                                @endif
                            </div>
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
                            @if ($item->user && $item->user->employee && $item->user->employee->avatar)
                                <img src="{{ asset('storage/' . $item->user->employee->avatar) }}"
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
