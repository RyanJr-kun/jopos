<div class="">
    <div class="table-responsive text-nowrap p-0 d-none d-md-block">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-secondary text-dark border-top">
                <tr>
                    <th>Detail Transaksi</th>
                    <th>Keuangan</th>
                    <th class="text-center">Status</th>
                    <th>Pembuat</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($penjualan as $key => $item)
                    <tr>
                        <td>
                            <div class="d-flex flex-column">
                                <span
                                    class="fw-bold text-dark text-sm mb-1">{{ $item->customer->name ?? 'Customer Umum' }}</span>
                                <small class="text-muted mb-1"><i
                                        class="bx bx-receipt text-xs me-1"></i>{{ $item->referensi }}</small>
                                <small class="text-muted"><i
                                        class="bx bx-calendar text-xs me-1"></i>{{ \Carbon\Carbon::parse($item->tanggal_penjualan)->translatedFormat('d M Y, H:i') }}</small>
                            </div>
                        </td>
                        <td>
                            @php
                                $persentase = $item->persentase_bayar;
                                $isJatuhTempo = $item->is_overdue;

                                $barColor = 'bg-info';
                                if ($item->status_pembayaran == 'Lunas') {
                                    $barColor = 'bg-success';
                                } elseif ($item->status_pembayaran == 'Piutang') {
                                    $barColor = 'bg-warning';
                                } elseif ($item->status_pembayaran == 'Batal') {
                                    $barColor = 'bg-danger';
                                } elseif ($isJatuhTempo) {
                                    $barColor = 'bg-danger';
                                } 

                                $statusClass =
                                    $item->status_pembayaran == 'Lunas'
                                        ? 'bg-label-success'
                                        : ($item->status_pembayaran == 'Piutang'
                                            ? 'bg-label-warning'
                                            : ($item->status_pembayaran == 'Batal'
                                                ? 'bg-label-danger'
                                                : 'bg-label-secondary'));
                            @endphp

                            <div class="d-flex flex-column" style="min-width: 170px;">
                                <div class="d-flex justify-content-between text-xs mb-1">
                                    <span class="fw-bold text-dark" title="Total Tagihan">Rp
                                        {{ number_format($item->total_akhir, 0, ',', '.') }}</span>
                                    
                                    @if ($item->status_pembayaran === 'Batal')
                                        {{-- Jika statusnya Batal, tampilkan label Batal --}}
                                        <span class="text-danger fw-semibold" title="Dibatalkan">Batal</span>
                                    @elseif ($item->sisa_piutang > 0)
                                        {{-- Jika tidak batal dan masih ada sisa piutang --}}
                                        <span class="text-danger fw-semibold" title="Sisa Piutang">- Rp
                                            {{ number_format($item->sisa_piutang, 0, ',', '.') }}</span>
                                    @else
                                        {{-- Jika tidak batal dan sisa piutang 0 --}}
                                        <span class="text-success fw-semibold" title="Lunas">Lunas</span>
                                    @endif
                                </div>

                                <div class="progress shadow-none border mb-1" style="height: 6px;">
                                    <div class="progress-bar {{ $barColor }}" role="progressbar"
                                        style="width: {{ $persentase }}%" aria-valuenow="{{ $persentase }}"
                                        aria-valuemin="0" aria-valuemax="100"></div>
                                </div>

                                <div class="d-flex justify-content-between align-items-center"
                                    style="font-size: 0.7rem;">
                                    <span class="text-muted" title="Sudah Dibayar">Bayar: Rp
                                        {{ number_format($item->jumlah_dibayar, 0, ',', '.') }}</span>
                                    @if ($item->status_pembayaran == 'Belum Lunas' && $item->tanggal_jatuh_tempo)
                                        <span class="{{ $isJatuhTempo ? 'text-danger fw-bold' : 'text-muted' }}">Tempo:
                                            {{ \Carbon\Carbon::parse($item->tanggal_jatuh_tempo)->format('d/m/y') }}</span>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="text-center">
                            <span class="badge {{ $statusClass }}" style="font-size: 0.7rem; width: 110px;">
                                <i class="bx bx-wallet text-xs me-1"></i> {{ $item->status_pembayaran }}
                            </span>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                @if ($item->user && $item->user->employee && $item->user->employee->avatar)
                                    <img src="{{ Storage::url($item->user->employee->avatar) }}"
                                        class="avatar avatar-sm rounded-circle me-2" alt="user_img">
                                @else
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
                        <td class="text-center">
                            <div class="dropdown">
                                <button type="button" class="btn p-0 dropdown-toggle hide-arrow"
                                    data-bs-toggle="dropdown"><i class="bx bx-dots-vertical-rounded"></i></button>
                                <div class="dropdown-menu">
                                    <a href="{{ route('penjualan.show', $item->referensi) }}"
                                        class="dropdown-item text-info" data-bs-toggle="tooltip" data-bs-placement="top"
                                        title="Lihat Detail">
                                        <i class="bx bx-show-alt me-2 text-info"></i> Lihat Detail
                                    </a>
                                    <a href="{{ route('penjualan.edit', $item->referensi) }}"
                                        class="dropdown-item text-secondary"data-bs-toggle="tooltip"
                                        data-bs-placement="top" title="Edit Transaksi">
                                        <i class="bx bx-edit-alt me-2 text-warning"></i> Edit penjualan</a>
                                    </a>
                                    <a href="javascript:void(0);" class="dropdown-item text-danger"
                                        data-bs-toggle="modal" data-bs-target="#cancelConfirmationModal"
                                        data-invoice-number="{{ $item->referensi }}"
                                        data-url="{{ route('penjualan.update', $item->referensi) }}"
                                        title="Batalkan Transaksi">
                                        <i class="bx bx-ban me-2"></i> Batalkan
                                    </a>
                                </div>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-3">
                            <p class=" text-dark text-sm fw-bold mb-0">Belum ada data penjualan.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="d-block d-md-none p-3">
        @forelse ($penjualan as $item)
            @php
                $persentase = $item->persentase_bayar;
                $isJatuhTempo = $item->is_overdue;

                $barColor = 'bg-info';
                if ($item->status_pembayaran == 'Lunas') {
                    $barColor = 'bg-success';
                } elseif ($item->status_pembayaran == 'Piutang') {
                    $barColor = 'bg-warning';
                } elseif ($item->status_pembayaran == 'Batal') {
                    $barColor = 'bg-danger';
                } elseif ($isJatuhTempo) {
                    $barColor = 'bg-danger';
                } 

                $statusPembayaranClass =
                    $item->status_pembayaran == 'Lunas'
                        ? 'bg-label-success'
                        : ($item->status_pembayaran == 'Piutang'
                            ? 'bg-label-warning'
                            : ($item->status_pembayaran == 'Batal'
                                ? 'bg-label-danger'
                                : 'bg-label-secondary'));
            @endphp

            <div class="card mb-3 shadow-sm border">
                <div class="card-body p-3">

                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <h6 class="mb-0 fw-bold text-dark">{{ $item->customer->name ?? 'customer Dihapus' }}</h6>
                            <small class="text-muted"><i
                                    class="bx bx-receipt text-xs me-1"></i>{{ $item->referensi }}</small>
                        </div>
                        <div class="dropdown">
                            <button type="button" class="btn p-0 dropdown-toggle hide-arrow"
                                data-bs-toggle="dropdown"><i class="bx bx-dots-vertical-rounded fs-4"></i></button>
                            <div class="dropdown-menu dropdown-menu-end">
                                <a class="dropdown-item" href="{{ route('penjualan.show', $item->referensi) }}"><i
                                        class="bx bx-show-alt me-2 text-info"></i> Lihat Detail</a>
                                <a class="dropdown-item" href="{{ route('penjualan.edit', $item->referensi) }}"><i
                                        class="bx bx-edit-alt me-2 text-warning"></i> Edit penjualan</a>
                                @if ($item->status_pembayaran != 'Batal')
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item text-danger" href="#" data-bs-toggle="modal"
                                        data-bs-target="#cancelConfirmationModal"
                                        data-invoice-number="{{ $item->referensi }}"
                                        data-url="{{ route('penjualan.update', $item->referensi) }}">
                                        <i class="bx bx-ban me-2"></i> Batalkan
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-3 text-xs">
                        <span class="text-muted"><i
                                class="bx bx-calendar text-xs me-1"></i>{{ \Carbon\Carbon::parse($item->tanggal_penjualan)->translatedFormat('d M Y') }}</span>
                        <div class="d-flex align-items-center">
                            @if ($item->user && $item->user->employee && $item->user->employee->avatar)
                                <img src="{{ Storage::url($item->user->employee->avatar) }}"
                                    class="avatar avatar-xs rounded-circle me-1" alt="user_img"
                                    style="width: 20px; height: 20px;">
                            @else
                                <div class="avatar avatar-xs me-1" style="width: 20px; height: 20px;">
                                    <span class="avatar-initial rounded-circle bg-label-info"
                                        style="font-size: 0.6rem;">{{ substr($item->user->name ?? 'U', 0, 1) }}</span>
                                </div>
                            @endif
                            <span class="text-muted text-truncate"
                                style="max-width: 80px;">{{ explode(' ', trim($item->user->name ?? 'User'))[0] }}</span>
                        </div>
                    </div>

                    <div class="d-flex gap-2 mb-3">
                        <span class="badge {{ $statusPembayaranClass }} px-2 py-1" style="font-size: 0.7rem;">
                            <i class="bx bx-wallet text-xs me-1"></i> {{ $item->status_pembayaran }}
                        </span>
                    </div>

                    <div class="bg-lighter rounded p-2 border">
                        <div class="d-flex justify-content-between text-sm mb-1">
                            <span class="fw-bold text-dark">Rp
                                {{ number_format($item->total_akhir, 0, ',', '.') }}</span>
                            @if ($item->sisa_piutang > 0)
                                <span class="text-danger fw-semibold">- Rp
                                    {{ number_format($item->sisa_piutang, 0, ',', '.') }}</span>
                            @else
                                <span class="text-success fw-semibold">Lunas</span>
                            @endif
                        </div>

                        <div class="progress shadow-none border mb-1" style="height: 6px;">
                            <div class="progress-bar {{ $barColor }}" role="progressbar"
                                style="width: {{ $persentase }}%" aria-valuenow="{{ $persentase }}"
                                aria-valuemin="0" aria-valuemax="100"></div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center" style="font-size: 0.75rem;">
                            <span class="text-muted">Bayar: Rp
                                {{ number_format($item->jumlah_dibayar, 0, ',', '.') }}</span>
                            @if ($item->status_pembayaran == 'Piutang' && $item->tanggal_jatuh_tempo)
                                <span class="{{ $isJatuhTempo ? 'text-danger fw-bold' : 'text-muted' }}">Tempo:
                                    {{ \Carbon\Carbon::parse($item->tanggal_jatuh_tempo)->format('d/m/y') }}</span>
                            @endif
                        </div>
                    </div>

                </div>
            </div>
        @empty
            <div class="text-center py-5 text-muted bg-lighter rounded border border-dashed">
                <i class="bx bx-folder-open display-4 mb-2"></i>
                <h6 class="text-dark fw-bold mb-0">Belum ada data penjualan.</h6>
            </div>
        @endforelse
    </div>
</div>

<div class="mt-3 px-3">
    {{ $penjualan->links() }}
</div>
