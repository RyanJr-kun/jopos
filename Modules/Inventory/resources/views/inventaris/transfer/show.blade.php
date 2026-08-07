@extends('layouts/contentNavbarLayout')
@section('title', 'Detail Transfer Stok - Inventory')

@php
    $statusConfig = [
        'draft' => ['label' => 'Draft', 'class' => 'bg-label-secondary', 'icon' => 'bx-edit-alt'],
        'dikirim' => ['label' => 'Dikirim', 'class' => 'bg-label-warning', 'icon' => 'bx-package'],
        'diterima' => ['label' => 'Diterima', 'class' => 'bg-label-success', 'icon' => 'bx-check-double'],
        'diterima_sebagian' => ['label' => 'Diterima Sebagian', 'class' => 'bg-label-info', 'icon' => 'bx-error'],
        'ditolak' => ['label' => 'Ditolak', 'class' => 'bg-label-danger', 'icon' => 'bx-x-circle'],
        'dibatalkan' => ['label' => 'Dibatalkan', 'class' => 'bg-label-dark', 'icon' => 'bx-block'],
    ];
    $badge = $statusConfig[$transfer->status] ?? [
        'label' => $transfer->status,
        'class' => 'bg-label-secondary',
        'icon' => 'bx-question-mark',
    ];
@endphp

@section('content')
    <div class="container-fluid p-0">

        {{-- HEADER --}}
        <div class="card rounded-3 shadow-sm border-0 mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div class="d-flex align-items-center">
                    <span class="avatar avatar-md me-3">
                        <span class="avatar-initial rounded bg-label-primary">
                            <i class="bx bx-transfer fs-4"></i>
                        </span>
                    </span>
                    <div>
                        <h5 class="mb-0 fw-bold">{{ $transfer->kode_transfer }}</h5>
                        <span class="badge {{ $badge['class'] }} mt-1">
                            <i class="bx {{ $badge['icon'] }} me-1"></i>{{ $badge['label'] }}
                        </span>
                    </div>
                </div>
                <a href="{{ route('stok-transfer.index') }}" class="btn btn-outline-secondary px-2" title="Kembali"
                    data-bs-toggle="tooltip" data-bs-placement="top">
                    <i class="bx bx-arrow-back"></i>
                </a>
            </div>
            <hr class="mx-5 my-0 pb-0">

            <div class="card-body">
                <div class="row g-4">
                    {{-- Alur toko asal -> tujuan --}}
                    <div class="col-12 col-lg-5">
                        <div class="d-flex align-items-center justify-content-between p-3 rounded-3 bg-light-subtle">
                            <div class="text-center flex-fill">
                                <p class="text-xs text-muted mb-1">TOKO ASAL</p>
                                <p class="fw-semibold mb-0">{{ $transfer->storeAsal->name_toko ?? 'N/A' }}</p>
                            </div>
                            <div class="px-3">
                                <i class="bx bx-right-arrow-alt fs-3 text-info"></i>
                            </div>
                            <div class="text-center flex-fill">
                                <p class="text-xs text-muted mb-1">TOKO TUJUAN</p>
                                <p class="fw-semibold mb-0">{{ $transfer->storeTujuan->name_toko ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </div>

                    {{-- Info pengiriman --}}
                    <div class="col-6 col-lg-3">
                        <p class="text-xs text-muted mb-1">DIKIRIM OLEH</p>
                        <p class="text-sm fw-semibold mb-1">{{ $transfer->userKirim->username ?? 'N/A' }}</p>
                        <p class="text-xs text-muted mb-0">
                            {{ $transfer->tanggal_kirim->translatedFormat('d M Y, H:i') }}
                        </p>
                    </div>

                    {{-- Info penerimaan --}}
                    <div class="col-6 col-lg-4">
                        <p class="text-xs text-muted mb-1">DITERIMA OLEH</p>
                        @if ($transfer->userTerima)
                            <p class="text-sm fw-semibold mb-1">{{ $transfer->userTerima->username }}</p>
                            <p class="text-xs text-muted mb-0">
                                {{ $transfer->tanggal_diterima?->translatedFormat('d M Y, H:i') }}
                            </p>
                        @else
                            <p class="text-sm text-muted mb-0">
                                <i class="bx bx-time-five me-1"></i> Menunggu konfirmasi
                            </p>
                        @endif
                    </div>
                </div>

                @if ($transfer->catatan_kirim)
                    <div class="mt-3">
                        <p class="text-xs text-muted mb-1">CATATAN PENGIRIMAN</p>
                        <p class="text-sm mb-0">{{ $transfer->catatan_kirim }}</p>
                    </div>
                @endif

                @if ($transfer->catatan_terima)
                    <div class="mt-3">
                        <p class="text-xs text-muted mb-1">CATATAN PENERIMAAN</p>
                        <p class="text-sm mb-0">{{ $transfer->catatan_terima }}</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- ITEMS + APPROVAL --}}
        <form action="{{ route('stok-transfer.approve', $transfer->kode_transfer) }}" method="POST" id="approveForm">
            @csrf
            <div class="card rounded-3 shadow-sm border-0" id="approval">
                <div class="card-header border-bottom py-3">
                    <h6 class="mb-0 fw-bold">Item Transfer</h6>
                    <p class="text-sm text-muted mb-0">
                        @if ($canApprove)
                            Periksa jumlah barang yang diterima. Untuk produk ber-serial number, centang/hapus centang
                            SN yang sesuai. Untuk produk non-SN, ubah qty secara manual.
                        @else
                            Rincian produk yang dikirim dalam transfer ini
                        @endif
                    </p>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive text-nowrap">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-secondary text-dark">
                                <tr>
                                    <th class="text-xs font-weight-bolder py-3 ps-4">Produk</th>
                                    <th class="text-xs font-weight-bolder text-center py-3">Qty Kirim</th>
                                    <th class="text-xs font-weight-bolder text-center py-3" width="15%">Qty Diterima</th>
                                    <th class="text-xs font-weight-bolder text-center py-3">Selisih</th>
                                    <th class="text-xs font-weight-bolder py-3">Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($transfer->details as $detail)
                                    <tr>
                                        <td class="ps-4">
                                            <div class="d-flex align-items-center">
                                                <img src="{{ $detail->produk->img_produk ? Storage::url($detail->produk->img_produk) : asset('assets/img/produk.png') }}"
                                                    class="rounded rounded-2 me-3"
                                                    style="width:40px; height:40px; object-fit:cover;" alt="product image">
                                                <div>
                                                    <h6 class="mb-0 text-sm">
                                                        {{ $detail->produk->name_product ?? 'Produk Dihapus' }}
                                                    </h6>
                                                    @if ($detail->variant)
                                                        <span
                                                            class="text-xs text-muted">{{ $detail->variant->name ?? '' }}</span>
                                                    @endif

                                                    {{-- TAMPILAN NOMOR SERI --}}
                                                    @if ($detail->serialNumbers->isNotEmpty())
                                                        @if ($canApprove)
                                                            @php $snCollapseId = 'sn-collapse-' . $detail->id; @endphp
                                                            {{-- MODE PENERIMAAN: dropdown trigger, checkbox SN disembunyikan sampai diklik --}}
                                                            <div class="mt-2 sn-receive-section">
                                                                <button type="button"
                                                                    class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center gap-1 sn-toggle-btn"
                                                                    data-bs-toggle="collapse"
                                                                    data-bs-target="#{{ $snCollapseId }}"
                                                                    aria-expanded="false"
                                                                    aria-controls="{{ $snCollapseId }}">
                                                                    <i class="bx bx-barcode text-xs"></i>
                                                                    <span
                                                                        class="sn-toggle-summary">{{ $detail->serialNumbers->count() }}
                                                                        SN dipilih</span>
                                                                    <i class="bx bx-chevron-down sn-toggle-chevron"></i>
                                                                </button>

                                                                <div class="collapse mt-2" id="{{ $snCollapseId }}">
                                                                    <div class="border rounded-3 p-2 sn-list-scroll"
                                                                        style="max-height: 220px; overflow-y: auto;">
                                                                        @foreach ($detail->serialNumbers as $sn)
                                                                            <div
                                                                                class="sn-item d-flex align-items-start gap-2 mb-1">
                                                                                <div class="form-check mb-0">
                                                                                    <input type="checkbox"
                                                                                        class="form-check-input sn-receive-check"
                                                                                        name="items[{{ $loop->parent->index }}][serial_numbers_diterima][]"
                                                                                        value="{{ $sn->id }}"
                                                                                        data-row-index="{{ $loop->parent->index }}"
                                                                                        data-sn-id="{{ $sn->id }}"
                                                                                        id="sn-{{ $detail->id }}-{{ $sn->id }}"
                                                                                        checked>
                                                                                    <label
                                                                                        class="form-check-label font-monospace text-sm"
                                                                                        for="sn-{{ $detail->id }}-{{ $sn->id }}">
                                                                                        {{ $sn->nomor_seri }}
                                                                                    </label>
                                                                                </div>
                                                                                <input type="text"
                                                                                    name="items[{{ $loop->parent->index }}][alasan_tolak_sn][{{ $sn->id }}]"
                                                                                    class="form-control form-control-sm sn-reason-input"
                                                                                    placeholder="Alasan tolak SN ini..."
                                                                                    style="display: none; max-width: 220px; font-size: 0.75rem;">
                                                                            </div>
                                                                        @endforeach
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @else
                                                            {{-- MODE VIEW ONLY --}}
                                                            <div class="mt-1">
                                                                @php
                                                                    $hasStatusTerima = $detail->serialNumbers->contains(
                                                                        fn($sn) => $sn->pivot->status_terima !== null,
                                                                    );
                                                                @endphp
                                                                @if ($hasStatusTerima)
                                                                    {{-- Tampilkan dengan badge warna per status --}}
                                                                    @foreach ($detail->serialNumbers as $sn)
                                                                        <div
                                                                            class="d-inline-flex align-items-center me-1 mb-1">
                                                                            <span
                                                                                class="badge {{ $sn->pivot->status_terima === 'ditolak' ? 'bg-label-danger' : 'bg-label-success' }}">
                                                                                <i
                                                                                    class="bx {{ $sn->pivot->status_terima === 'ditolak' ? 'bx-x' : 'bx-check' }} me-1"></i>{{ $sn->nomor_seri }}
                                                                            </span>
                                                                            @if ($sn->pivot->alasan_tolak)
                                                                                <small
                                                                                    class="text-muted ms-1 fst-italic">{{ $sn->pivot->alasan_tolak }}</small>
                                                                            @endif
                                                                        </div>
                                                                    @endforeach
                                                                @else
                                                                    <small class="text-info fw-medium">
                                                                        <i class="bx bx-barcode text-xs me-1"></i>
                                                                        SN:
                                                                        {{ $detail->serialNumbers->pluck('nomor_seri')->implode(', ') }}
                                                                    </small>
                                                                @endif
                                                            </div>
                                                        @endif
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td class="align-middle text-center">
                                            <span class="fw-bold">{{ $detail->qty_kirim }}</span>
                                        </td>
                                        <td class="align-middle text-center">
                                            @if ($canApprove)
                                                <input type="hidden" name="items[{{ $loop->index }}][id]"
                                                    value="{{ $detail->id }}">
                                                @if ($detail->serialNumbers->isNotEmpty())
                                                    {{-- SN item: qty readonly, otomatis dari checkbox --}}
                                                    <input type="number" name="items[{{ $loop->index }}][qty_diterima]"
                                                        class="form-control form-control-sm text-center qty-diterima-input sn-auto-qty"
                                                        data-qty-kirim="{{ $detail->qty_kirim }}"
                                                        value="{{ $detail->qty_kirim }}" min="0"
                                                        max="{{ $detail->qty_kirim }}" required readonly>
                                                    <small class="text-muted d-block mt-1"
                                                        style="font-size: 0.65rem;">Otomatis dari SN</small>
                                                @else
                                                    {{-- Non-SN item: qty manual --}}
                                                    <input type="number" name="items[{{ $loop->index }}][qty_diterima]"
                                                        class="form-control form-control-sm text-center qty-diterima-input"
                                                        data-qty-kirim="{{ $detail->qty_kirim }}"
                                                        value="{{ $detail->qty_kirim }}" min="0"
                                                        max="{{ $detail->qty_kirim }}" required>
                                                @endif
                                            @else
                                                @if (is_null($detail->qty_diterima))
                                                    <span class="text-muted">-</span>
                                                @else
                                                    <span class="fw-bold">{{ $detail->qty_diterima }}</span>
                                                @endif
                                            @endif
                                        </td>
                                        <td class="align-middle text-center selisih-cell"
                                            data-row-index="{{ $loop->index }}">
                                            @if (!$canApprove)
                                                @if (is_null($detail->qty_diterima))
                                                    <span class="text-muted">-</span>
                                                @elseif ($detail->qty_diterima == $detail->qty_kirim)
                                                    <span class="badge bg-label-success">Sesuai</span>
                                                @else
                                                    <span class="badge bg-label-danger">
                                                        -{{ $detail->qty_kirim - $detail->qty_diterima }}
                                                    </span>
                                                @endif
                                            @else
                                                <span class="badge bg-label-success">Sesuai</span>
                                            @endif
                                        </td>
                                        <td class="align-middle text-sm">
                                            @if ($canApprove)
                                                <input type="text"
                                                    name="items[{{ $loop->index }}][keterangan_selisih]"
                                                    class="form-control form-control-sm keterangan-input"
                                                    placeholder="Isi kalau ada selisih" style="display: none;">
                                            @else
                                                {{ $detail->keterangan_selisih ?: '-' }}
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4">Tidak ada item pada transfer ini.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                @if ($canApprove)
                    <div class="card-footer bg-light-subtle">
                        <div class="mb-3">
                            <label for="catatan_terima" class="form-label fw-semibold">Catatan Penerimaan
                                (Opsional)</label>
                            <textarea name="catatan_terima" id="catatan_terima" class="form-control" rows="2"
                                placeholder="Contoh: 1 unit monitor pecah saat pengiriman"></textarea>
                        </div>
                        <div class="d-flex justify-content-end gap-2">
                            <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal"
                                data-bs-target="#rejectModal">
                                <i class="bx bx-x-circle me-1"></i> Tolak Transfer
                            </button>
                            <button type="submit" class="btn btn-success" id="btn-confirm-approve">
                                <i class="bx bx-check-double me-1"></i> Konfirmasi Terima
                            </button>
                        </div>
                    </div>
                @endif
            </div>
        </form>

        {{-- MODAL TOLAK TRANSFER --}}
        @if ($canApprove)
            <div class="modal fade" id="rejectModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <form action="{{ route('stok-transfer.reject', $transfer->kode_transfer) }}" method="POST">
                            @csrf
                            <div class="modal-body text-center mt-3 mx-n3">
                                <i class="bx bx-error-circle fa-3x text-danger mb-3"></i>
                                <h5 class="mb-2">Tolak Transfer Ini?</h5>
                                <p class="mb-3">
                                    Semua stok yang dikirim ({{ $transfer->details->sum('qty_kirim') }} unit) akan
                                    dikembalikan ke <strong>{{ $transfer->storeAsal->name_toko }}</strong>.
                                </p>
                                <div class="text-start">
                                    <label for="catatan_terima_reject" class="form-label fw-semibold">
                                        Alasan Penolakan <span class="text-danger">*</span>
                                    </label>
                                    <textarea name="catatan_terima" id="catatan_terima_reject" class="form-control" rows="2" required
                                        placeholder="Contoh: Salah kirim, harusnya ke toko lain"></textarea>
                                </div>
                            </div>
                            <div class="modal-footer border-0 justify-content-center">
                                <button type="submit" class="btn btn-danger">Ya, Tolak Transfer</button>
                                <button type="button" class="btn btn-outline-secondary"
                                    data-bs-dismiss="modal">Batal</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection

@section('page-script')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Live update: tampilkan input keterangan & badge selisih begitu qty_diterima diubah
            document.querySelectorAll('.qty-diterima-input').forEach(function(input) {
                input.addEventListener('input', function() {
                    const row = this.closest('tr');
                    const qtyKirim = parseInt(this.dataset.qtyKirim);
                    const qtyDiterima = parseInt(this.value) || 0;
                    const selisihCell = row.querySelector('.selisih-cell');
                    const keteranganInput = row.querySelector('.keterangan-input');

                    if (qtyDiterima > qtyKirim) {
                        this.value = qtyKirim;
                        return;
                    }

                    if (qtyDiterima === qtyKirim) {
                        selisihCell.innerHTML =
                            '<span class="badge bg-label-success">Sesuai</span>';
                        if (keteranganInput) {
                            keteranganInput.style.display = 'none';
                            keteranganInput.removeAttribute('required');
                        }
                    } else {
                        const selisih = qtyKirim - qtyDiterima;
                        selisihCell.innerHTML =
                            `<span class="badge bg-label-danger">-${selisih}</span>`;
                        if (keteranganInput) {
                            keteranganInput.style.display = 'block';
                            keteranganInput.setAttribute('required', 'required');
                        }
                    }
                });
            });

            // === SERIAL NUMBER CHECKBOX HANDLING (Fase 2) ===
            document.querySelectorAll('.sn-receive-check').forEach(function(checkbox) {
                checkbox.addEventListener('change', function() {
                    const snItem = this.closest('.sn-item');
                    const reasonInput = snItem.querySelector('.sn-reason-input');
                    const row = this.closest('tr');

                    // Show/hide alasan tolak input
                    if (!this.checked) {
                        reasonInput.style.display = 'block';
                        reasonInput.setAttribute('required', 'required');
                    } else {
                        reasonInput.style.display = 'none';
                        reasonInput.removeAttribute('required');
                        reasonInput.value = '';
                    }

                    // Auto-calculate qty_diterima dari jumlah SN yang dicentang
                    const qtyInput = row.querySelector('.sn-auto-qty');
                    if (qtyInput) {
                        const checkedCount = row.querySelectorAll('.sn-receive-check:checked')
                            .length;
                        qtyInput.value = checkedCount;
                        // Trigger existing selisih update
                        qtyInput.dispatchEvent(new Event('input'));
                    }

                    // Auto-generate keterangan selisih dari alasan tolak SN
                    updateKeteranganFromSN(row);

                    // Update badge ringkasan di tombol dropdown SN
                    updateSnToggleSummary(row);
                });
            });

            // Sinkronkan arah chevron dengan status buka/tutup dropdown SN
            document.querySelectorAll('.sn-toggle-btn').forEach(function(btn) {
                const target = document.querySelector(btn.getAttribute('data-bs-target'));
                const chevron = btn.querySelector('.sn-toggle-chevron');
                if (!target || !chevron) return;

                target.addEventListener('show.bs.collapse', function() {
                    chevron.classList.replace('bx-chevron-down', 'bx-chevron-up');
                });
                target.addEventListener('hide.bs.collapse', function() {
                    chevron.classList.replace('bx-chevron-up', 'bx-chevron-down');
                });
            });

            /**
             * Update label "X SN dipilih" / "Y/X SN dipilih" di tombol dropdown,
             * plus warnai tombolnya kalau ada SN yang ditolak.
             */
            function updateSnToggleSummary(row) {
                const toggleBtn = row.querySelector('.sn-toggle-btn');
                if (!toggleBtn) return;

                const summarySpan = toggleBtn.querySelector('.sn-toggle-summary');
                const total = row.querySelectorAll('.sn-receive-check').length;
                const checked = row.querySelectorAll('.sn-receive-check:checked').length;

                if (summarySpan) {
                    summarySpan.textContent = checked === total ?
                        `${total} SN dipilih` :
                        `${checked}/${total} SN dipilih`;
                }

                toggleBtn.classList.toggle('btn-outline-secondary', checked === total);
                toggleBtn.classList.toggle('btn-outline-danger', checked !== total);
            }

            // Listen juga pada perubahan input alasan tolak SN
            document.querySelectorAll('.sn-reason-input').forEach(function(input) {
                input.addEventListener('input', function() {
                    const row = this.closest('tr');
                    updateKeteranganFromSN(row);
                });
            });

            /**
             * Gabungkan alasan tolak per-SN menjadi keterangan selisih item.
             */
            function updateKeteranganFromSN(row) {
                const keteranganInput = row.querySelector('.keterangan-input');
                if (!keteranganInput) return;

                const unchecked = row.querySelectorAll('.sn-receive-check:not(:checked)');
                if (unchecked.length === 0) return;

                const reasons = [];
                unchecked.forEach(function(cb) {
                    const snItem = cb.closest('.sn-item');
                    const label = snItem.querySelector('.form-check-label');
                    const reasonInput = snItem.querySelector('.sn-reason-input');
                    const snLabel = label ? label.textContent.trim() : '';
                    const reason = reasonInput ? reasonInput.value.trim() : '';
                    if (reason) {
                        reasons.push(snLabel + ': ' + reason);
                    }
                });

                if (reasons.length > 0) {
                    keteranganInput.value = reasons.join('; ');
                }
            }

            // Konfirmasi sebelum submit approve, khusus kalau ada selisih
            const approveForm = document.getElementById('approveForm');
            if (approveForm) {
                approveForm.addEventListener('submit', function(e) {
                    // Validasi: SN yang ditolak harus punya alasan
                    let missingReason = false;
                    document.querySelectorAll('.sn-receive-check:not(:checked)').forEach(function(cb) {
                        const snItem = cb.closest('.sn-item');
                        const reasonInput = snItem.querySelector('.sn-reason-input');
                        if (reasonInput && !reasonInput.value.trim()) {
                            missingReason = true;
                            reasonInput.classList.add('is-invalid');

                            // Buka dropdown SN-nya biar user bisa lihat & isi alasannya
                            const collapseEl = cb.closest('.collapse');
                            if (collapseEl && window.bootstrap) {
                                window.bootstrap.Collapse.getOrCreateInstance(collapseEl, {
                                    toggle: false
                                }).show();
                            }
                        } else if (reasonInput) {
                            reasonInput.classList.remove('is-invalid');
                        }
                    });

                    if (missingReason) {
                        e.preventDefault();
                        alert('Harap isi alasan untuk setiap serial number yang ditolak.');
                        return;
                    }

                    const adaSelisih = Array.from(document.querySelectorAll('.selisih-cell'))
                        .some(cell => cell.querySelector('.bg-label-danger'));

                    if (adaSelisih && !confirm(
                            'Ada selisih qty pada transfer ini. Selisih akan otomatis dikembalikan sebagai stok di toko asal. Lanjutkan?'
                        )) {
                        e.preventDefault();
                    }
                });
            }
        });
    </script>
@endsection
