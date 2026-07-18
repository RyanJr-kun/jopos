@extends('layouts/contentNavbarLayout')

@section('title', \App\Models\CashFlow::typeLabel($type))

@section('page-style')
    @vite(['resources/assets/vendor/scss/pages/page-bank.scss'])
@endsection

@section('content')

    @php
        use App\Models\CashFlow;
        $isTransfer = $type === CashFlow::TYPE_TRANSFER;
        $label = CashFlow::typeLabel($type);

        $cardClass = match ($type) {
            CashFlow::TYPE_INCOME => 'fin-income',
            CashFlow::TYPE_EXPENSE => 'fin-expense',
            default => 'fin-profit',
        };
        $cardIcon = match ($type) {
            CashFlow::TYPE_INCOME => 'bx-trending-up',
            CashFlow::TYPE_EXPENSE => 'bx-trending-down',
            default => 'bx-transfer-alt',
        };
    @endphp

    {{-- ================================================================= --}}
    {{-- HEADER                                                             --}}
    {{-- ================================================================= --}}
    <div class="d-flex align-items-center justify-content-between flex-wrap mb-3">
        <div class="cf-type-nav">
            <a href="{{ route('financial.cash-flows.index', ['type' => \App\Models\CashFlow::TYPE_INCOME]) }}"
                class="cf-type-tab {{ $type === \App\Models\CashFlow::TYPE_INCOME ? 'active income' : '' }}">
                <i class="bx bx-trending-up"></i>
                <span>Pemasukan</span>
            </a>
            <a href="{{ route('financial.cash-flows.index', ['type' => \App\Models\CashFlow::TYPE_EXPENSE]) }}"
                class="cf-type-tab {{ $type === \App\Models\CashFlow::TYPE_EXPENSE ? 'active expense' : '' }}">
                <i class="bx bx-trending-down"></i>
                <span>Pengeluaran</span>
            </a>
            <a href="{{ route('financial.cash-flows.index', ['type' => \App\Models\CashFlow::TYPE_TRANSFER]) }}"
                class="cf-type-tab {{ $type === \App\Models\CashFlow::TYPE_TRANSFER ? 'active transfer' : '' }}">
                <i class="bx bx-transfer-alt"></i>
                <span>Transfer</span>
            </a>
        </div>
        <button type="button" class="btn btn-primary px-2"
            data-cash-flow-create="{{ route('financial.cash-flows.create', ['type' => $type]) }}"
            data-label="Tambah {{ $label }}" title="Tambah {{ $label }}" data-bs-toggle="tooltip"
            data-bs-placement="top">
            <i class="bx bx-plus fs-5"></i>
        </button>
    </div>

    {{-- ================================================================= --}}
    {{-- TYPE SWITCHER NAV                                                   --}}
    {{-- ================================================================= --}}



    {{-- ================================================================= --}}
    {{-- RINGKASAN TOTAL                                                     --}}
    {{-- ================================================================= --}}
    <div class="fin-summary-card {{ $cardClass }} mb-3">
        <div class="d-flex align-items-center gap-3">
            <div class="fin-card-icon">
                <i class="bx {{ $cardIcon }}"></i>
            </div>
            <div class="flex-grow-1">
                <p class="fin-card-label">Total {{ $label }} <span class="ms-1">(sesuai filter aktif)</span></p>
                <h4 class="fin-card-value mb-0">@money($totalNominal)</h4>
            </div>
            <div class="text-end d-none d-sm-block">
                <p class="fin-card-label">Jumlah Transaksi</p>
                <h5 class="fin-card-value mb-0 fs-5">{{ number_format($totalCount) }}</h5>
            </div>
        </div>
    </div>

    {{-- ================================================================= --}}
    {{-- FILTER BAR                                                          --}}
    {{-- ================================================================= --}}
    <div class="fin-filter-bar mb-4">
        <form method="GET" id="cashFlowFilterForm" action="{{ route('financial.cash-flows.index', ['type' => $type]) }}">

            <div class="row g-2 align-items-end">
                {{-- Search --}}
                <div class="col-12 col-md-4 col-lg-3">
                    <label class="form-label text-muted mb-1">Cari</label>
                    <div class="input-group">
                        <input type="text" name="search" class="form-control" placeholder="Keterangan / referensi..."
                            value="{{ request('search') }}">
                    </div>
                </div>

                {{-- Date From --}}
                {{-- Rentang Tanggal (Date Range) --}}
                <div class="col-12 col-md-6 col-lg-3">
                    <label class="form-label text-muted mb-1">Rentang Tanggal</label>
                    <div class="input-group">
                        <input type="text" id="dateRange" class="form-control" placeholder="Pilih rentang tanggal..."
                            autocomplete="off">
                        {{-- Hidden fields untuk dikirim ke Controller --}}
                        <input type="hidden" name="date_from" id="dateFromHidden" value="{{ $dateFrom }}">
                        <input type="hidden" name="date_to" id="dateToHidden" value="{{ $dateTo }}">
                    </div>
                </div>

                {{-- Kategori --}}
                @unless ($isTransfer)
                    <div class="col-12 col-md-4 col-lg-2">
                        <label class="form-label form-label-sm text-muted mb-1">Kategori</label>
                        <select name="kategori_id" class="form-select select2-filter">
                            <option value="">Semua Kategori</option>
                            @foreach ($kategoriFilters as $kategori)
                                <option value="{{ $kategori->id }}"
                                    {{ request('kategori_id') == $kategori->id ? 'selected' : '' }}>
                                    {{ $kategori->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endunless

                {{-- Store --}}
                @if ($stores)
                    <div class="col-12 col-md-4 col-lg-2">
                        <label class="form-label form-label-sm text-muted mb-1">Toko</label>
                        <select name="store_id" class="form-select select2-filter">
                            <option value="">Semua Toko</option>
                            @foreach ($stores as $store)
                                <option value="{{ $store->id }}"
                                    {{ request('store_id') == $store->id ? 'selected' : '' }}>
                                    {{ $store->name_toko }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endif

                {{-- Status --}}
                <div class="col-6 col-md-3 col-lg-2">
                    <label class="form-label form-label-sm text-muted mb-1">Status</label>
                    <select name="status" class="form-select select2-filter">
                        <option value="">Semua Status</option>
                        <option value="aktif" {{ request('status') === 'aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="batal" {{ request('status') === 'batal' ? 'selected' : '' }}>Dibatalkan</option>
                    </select>
                </div>

                {{-- Action Buttons --}}
                <div class="col-6 col-md-auto d-flex justify-content-end gap-2">
                    <button type="submit" class="btn btn-primary px-2" title="Filter" data-bs-toggle="tooltip"
                        data-bs-placement="top">
                        <i class="bx bx-search fs-5"></i>
                    </button>
                    <a href="{{ route('financial.cash-flows.index', ['type' => $type]) }}"
                        class="btn btn-outline-secondary px-2" title="Reset filter" data-bs-toggle="tooltip"
                        data-bs-placement="top">
                        <i class="bx bx-reset fs-5"></i>
                    </a>
                </div>
            </div>
        </form>
    </div>

    {{-- ================================================================= --}}
    {{-- TABEL (dibungkus @fragment untuk AJAX refresh)                      --}}
    {{-- ================================================================= --}}
    @fragment('cash-flow-table-area')
        <div id="cash-flow-table-area">
            <div class="card">
                <div class="table-responsive">
                    <table class="table fin-tx-table align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Referensi</th>
                                <th>Keterangan</th>
                                @unless ($isTransfer)
                                    <th>Kategori</th>
                                @endunless
                                @if ($isTransfer)
                                    <th>Dari</th>
                                    <th>Ke</th>
                                @else
                                    <th>Akun</th>
                                @endif
                                <th>Metode</th>
                                <th class="text-end">Nominal</th>
                                <th>Penanggung Jawab</th>
                                <th>Status</th>
                                <th class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($cashFlows as $cf)
                                <tr class="{{ $cf->dibatalkan_at ? 'cf-row-cancelled' : '' }}">
                                    <td class="cf-date">{{ $cf->tanggal->isoFormat('D MMM Y') }}</td>
                                    <td>
                                        <span class="cf-ref">{{ $cf->referensi }}</span>
                                    </td>
                                    <td>
                                        <div class="cf-keterangan">{{ \Illuminate\Support\Str::limit($cf->keterangan, 50) }}
                                        </div>
                                        @if ($cf->is_otomatis)
                                            <span class="cf-badge-auto">
                                                <i class="bx bx-bot"></i> Otomatis
                                            </span>
                                        @endif
                                    </td>
                                    @unless ($isTransfer)
                                        <td>
                                            @if ($cf->transaction_category)
                                                <span class="cf-kategori-pill">{{ $cf->transaction_category->name }}</span>
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                    @endunless
                                    @if ($isTransfer)
                                        <td class="cf-bank">{{ $cf->account?->account_name ?? '—' }}</td>
                                        <td class="cf-bank">{{ $cf->toAccount?->account_name ?? '—' }}</td>
                                    @else
                                        <td class="cf-bank">{{ $cf->account?->account_name ?? '—' }}</td>
                                    @endif
                                    <td>
                                        @php $metode = strtolower($cf->metode_pembayaran ?? ''); @endphp
                                        <span class="fin-tx-badge {{ $metode }}">
                                            @if ($metode === 'tunai')
                                                <i class="bx bx-money"></i>
                                            @elseif ($metode === 'qris')
                                                <i class="bx bx-qr-scan"></i>
                                            @else
                                                <i class="bx bx-credit-card"></i>
                                            @endif
                                            {{ strtoupper($cf->metode_pembayaran ?? '—') }}
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <span class="fin-amount {{ $type }}">@money($cf->nominal)</span>
                                    </td>
                                    <td>
                                        <div class="cf-user d-flex align-items-center gap-2">
                                            <div class="cf-user-avatar">
                                                {{ strtoupper(substr($cf->user?->name ?? '?', 0, 1)) }}
                                            </div>
                                            <span class="cf-user-name">{{ $cf->user?->name ?? '—' }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        @if ($cf->dibatalkan_at)
                                            <span class="badge bg-label-danger">Dibatalkan</span>
                                        @else
                                            <span class="badge bg-label-success">Aktif</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        @if (!$cf->source_type && !$cf->dibatalkan_at)
                                            <div class="d-flex gap-1 justify-content-end">
                                                <button type="button"
                                                    class="btn btn-sm btn-icon btn-outline-primary cf-btn-edit"
                                                    data-cash-flow-edit="{{ route('financial.cash-flows.edit', ['type' => $type, 'cash_flow' => $cf->referensi]) }}"
                                                    data-label="Edit {{ $label }}" title="Edit">
                                                    <i class="bx bx-edit"></i>
                                                </button>
                                                <button type="button"
                                                    class="btn btn-sm btn-icon btn-outline-danger cf-btn-delete"
                                                    data-delete-action="{{ route('financial.cash-flows.destroy', ['type' => $type, 'cash_flow' => $cf->referensi]) }}"
                                                    data-delete-label="{{ $cf->keterangan }}" title="Hapus">
                                                    <i class="bx bx-trash"></i>
                                                </button>
                                            </div>
                                        @else
                                            <span class="cf-lock-icon"
                                                title="{{ $cf->source_type ? 'Data otomatis, tidak bisa diubah manual' : 'Sudah dibatalkan' }}">
                                                <i class="bx bx-lock-alt"></i>
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="p-0">
                                        <div class="cf-empty-state">
                                            <div class="cf-empty-icon">
                                                <i class="bx bx-receipt"></i>
                                            </div>
                                            <h6 class="cf-empty-title">Tidak ada data {{ strtolower($label) }}</h6>
                                            <p class="cf-empty-desc">
                                                Belum ada transaksi yang sesuai dengan filter yang dipilih.<br>
                                                Coba ubah filter atau tambah transaksi baru.
                                            </p>
                                            <button type="button" class="btn btn-sm btn-primary"
                                                data-cash-flow-create="{{ route('financial.cash-flows.create', ['type' => $type]) }}"
                                                data-label="Tambah {{ $label }}">
                                                <i class="bx bx-plus me-1"></i> Tambah {{ $label }}
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if ($cashFlows->hasPages())
                    <div class="card-footer">
                        {{ $cashFlows->onEachSide(1)->links() }}
                    </div>
                @endif
            </div>
        </div>
    @endfragment

    {{-- Offcanvas create/edit --}}
    @include('content.finance._canvas')

    {{-- Modal Konfirmasi Hapus --}}
    <div class="modal fade" id="cfDeleteModal" tabindex="-1" aria-labelledby="cfDeleteModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content">
                <div class="modal-body text-center py-4 px-4">
                    <div class="mb-3" style="font-size:3rem;color:#ea5455">
                        <i class="bx bx-error-circle"></i>
                    </div>
                    <h6 class="fw-bold mb-1">Hapus Transaksi?</h6>
                    <p class="text-muted small mb-0" id="cfDeleteModalBody">Transaksi ini akan dihapus permanen.</p>
                </div>
                <div class="modal-footer border-0 justify-content-center gap-2 pt-0 pb-4">
                    <button type="button" class="btn btn-outline-secondary btn-sm px-4"
                        data-bs-dismiss="modal">Batal</button>
                    <form id="cfDeleteForm" method="POST">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm px-4">Ya, Hapus</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('page-script')
    <script type="module">
        /* global $, bootstrap, flatpickr */
        const $ = window.jQuery;
        const {
            Modal
        } = window.bootstrap;

        $(function() {

            /**
             * Select2 di-load secara ASYNC (dynamic import) oleh Vite wrapper.
             * Pada saat $(function(){}) jalan, $.fn.select2 kemungkinan belum tersedia.
             * Maka kita gunakan polling singkat untuk menunggu Select2 siap.
             */
            function waitForSelect2(callback, maxWait) {
                maxWait = maxWait || 5000;
                var interval = 50;
                var elapsed = 0;

                var timer = setInterval(function() {
                    elapsed += interval;
                    if ($.fn.select2) {
                        clearInterval(timer);
                        callback();
                    } else if (elapsed >= maxWait) {
                        clearInterval(timer);
                        console.warn('Select2 tidak tersedia setelah ' + maxWait +
                            'ms, filter fallback ke <select> biasa.');
                    }
                }, interval);
            }

            waitForSelect2(function() {
                $('.select2-filter').select2({
                    width: '100%',
                    // Angka 10: kotak search disembunyikan jika pilihan < 10 (misal Status)
                    minimumResultsForSearch: 10
                }).on('change', function() {
                    $('#cashFlowFilterForm').submit();
                });
            });

            /* ── Flatpickr — Filter Tanggal (Mode Range) ─────────────────────────────── */
            function waitForFlatpickr(callback, maxWait) {
                maxWait = maxWait || 5000;
                var interval = 50;
                var elapsed = 0;

                if (window.flatpickr) {
                    callback();
                    return;
                }

                var timer = setInterval(function() {
                    elapsed += interval;
                    if (window.flatpickr) {
                        clearInterval(timer);
                        callback();
                    } else if (elapsed >= maxWait) {
                        clearInterval(timer);
                        console.warn('Flatpickr tidak tersedia setelah ' + maxWait + 'ms.');
                    }
                }, interval);
            }

            // Helper: konversi Date ke string 'YYYY-MM-DD' tanpa masalah timezone
            function toISODate(date) {
                var y = date.getFullYear();
                var m = String(date.getMonth() + 1).padStart(2, '0');
                var d = String(date.getDate()).padStart(2, '0');
                return y + '-' + m + '-' + d;
            }

            waitForFlatpickr(function() {
                // Ambil nilai filter yang sedang aktif dari URL (jika ada)
                var activeDateFrom = $('#dateFromHidden').val(); // format: 'YYYY-MM-DD' atau ''
                var activeDateTo = $('#dateToHidden').val();
                var defaultDates = [];

                // Flatpickr defaultDate harus berupa Date object atau string yang cocok
                // dengan dateFormat. Karena dateFormat kita 'd/m/Y' tapi hidden value
                // format 'Y-m-d', kita pass sebagai Date object agar aman.
                if (activeDateFrom) defaultDates.push(new Date(activeDateFrom + 'T00:00:00'));
                if (activeDateTo) defaultDates.push(new Date(activeDateTo + 'T00:00:00'));

                flatpickr('#dateRange', {
                    mode: 'range',
                    dateFormat: 'd/m/Y',
                    allowInput: true,
                    defaultDate: defaultDates.length ? defaultDates : undefined,
                    locale: {
                        firstDayOfWeek: 1,
                        rangeSeparator: ' sampai '
                    },
                    onChange: function(selectedDates) {
                        if (selectedDates.length === 0) {
                            $('#dateFromHidden').val('');
                            $('#dateToHidden').val('');
                            $('#cashFlowFilterForm').submit();
                        } else if (selectedDates.length === 1) {
                            $('#dateFromHidden').val(toISODate(selectedDates[0]));
                            $('#dateToHidden').val('');
                        } else if (selectedDates.length === 2) {
                            $('#dateFromHidden').val(toISODate(selectedDates[0]));
                            $('#dateToHidden').val(toISODate(selectedDates[1]));
                            $('#cashFlowFilterForm').submit();
                        }
                    },
                });
            });

            /* ── Modal Konfirmasi Hapus ─────────────────────────────────── */
            const $deleteModal = $('#cfDeleteModal');
            if ($deleteModal.length) {
                const bsDelete = new Modal($deleteModal[0]);

                $(document).on('click', '.cf-btn-delete', function() {
                    const action = $(this).data('delete-action');
                    const label = $(this).data('delete-label') || 'transaksi ini';

                    $('#cfDeleteForm').attr('action', action);
                    $('#cfDeleteModalBody').text(
                        `"${label}" akan dihapus permanen dan tidak bisa dikembalikan.`
                    );
                    bsDelete.show();
                });
            }

        }); // end $(function)
    </script>
@endsection
