@extends('layouts/contentNavbarLayout')

@section('title', 'Dashboard Keuangan')

@section('page-style')
    @vite(['resources/assets/vendor/scss/pages/page-bank.scss'])
@endsection

@section('vendor-script')
    @vite(['resources/assets/vendor/libs/apex-charts/apexcharts.js'])
@endsection

@section('content')

    {{-- ========================================================= --}}
    {{-- FILTER BAR                                                 --}}
    {{-- ========================================================= --}}
    <form action="{{ route('keuangan') }}" method="GET" id="filterForm">
        <div class="fin-filter-bar mb-3 d-flex align-items-center flex-wrap gap-3">

            {{-- Metode Pembayaran Pills --}}
            <div class="fin-metode-pills">
                <button type="button" class="fin-pill {{ !request('metode') ? 'active' : '' }}" onclick="setMetode('')">
                    <i class="bx bx-grid-alt"></i> Semua
                </button>
                <button type="button" class="fin-pill {{ request('metode') == 'tunai' ? 'active' : '' }}"
                    data-metode="tunai" onclick="setMetode('tunai')">
                    <i class="bx bx-money"></i> Tunai
                </button>
                <button type="button" class="fin-pill {{ request('metode') == 'transfer' ? 'active' : '' }}"
                    data-metode="transfer" onclick="setMetode('transfer')">
                    <i class="bx bx-transfer-alt"></i> Transfer
                </button>
                <button type="button" class="fin-pill {{ request('metode') == 'qris' ? 'active' : '' }}" data-metode="qris"
                    onclick="setMetode('qris')">
                    <i class="bx bx-qr"></i> QRIS
                </button>
            </div>

            <input type="hidden" name="metode" id="inputMetode" value="{{ request('metode') }}">

            {{-- Bank select (muncul saat transfer) --}}
            <div id="bankSelectWrap" class="{{ in_array(request('metode'), ['transfer', 'qris']) ? '' : 'd-none' }}">
                <select name="account_id" class="form-select form-select-sm fin-bank-select" id="bankSelect"
                    onchange="document.getElementById('filterForm').submit()">
                    <option value="">Semua akun</option>
                    @foreach ($accounts as $a)
                        <option value="{{ $a->id }}" {{ request('account_id') == $a->id ? 'selected' : '' }}>
                            {{ $a->account_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            @can('view-toko-gudang')
                <div id="storeSelectWrap">
                    <select name="store_id" class="form-select form-select-sm fin-bank-select" id="storeSelect"
                        onchange="document.getElementById('filterForm').submit()">
                        <option value="">Semua Toko</option>
                        @foreach ($stores as $s)
                            <option value="{{ $s->id }}" {{ request('store_id') == $s->id ? 'selected' : '' }}>
                                {{ $s->name_toko }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @endcan

            <div class="fin-filter-divider"></div>

            {{-- Date range - Flatpickr --}}
            <div class="d-flex align-items-center gap-2">
                <input type="text" id="dateRange" name="date_range" class="form-control form-control-sm flatpickr-input"
                    placeholder="Pilih rentang tanggal" style="width: 220px" readonly>
                {{-- Hidden inputs untuk start & end --}}
                <input type="hidden" name="start_date" id="startDateInput" value="{{ $startDate }}">
                <input type="hidden" name="end_date" id="endDateInput" value="{{ $endDate }}">
            </div>

            <div class="d-flex gap-2 ms-auto">
                <button type="submit" class="btn btn-sm btn-primary px-2" title="Filter" data-bs-toggle="tooltip"
                    data-bs-placement="top">
                    <i class="bx bx-filter-alt fs-5"></i>
                </button>
                <a href="{{ route('keuangan') }}" class="btn btn-sm btn-outline-secondary px-2" title="Reset Filter"
                    data-bs-toggle="tooltip" data-bs-placement="top">
                    <i class="bx bx-reset fs-5"></i>
                </a>
                {{-- Tombol Tambah Mutasi --}}
                {{-- <button type="button" class="btn btn-sm btn-success px-3" data-bs-toggle="modal"
                    data-bs-target="#modalMutasi">
                    <i class="bx bx-plus me-1"></i> Mutasi
                </button> --}}
                {{-- Tombol Kelola Bank --}}
                <button type="button" class="btn btn-sm btn-outline-primary px-3" data-bs-toggle="modal"
                    data-bs-target="#modalBank">
                    <i class="bx bxs-bank me-1"></i> Akun
                </button>
            </div>
        </div>
    </form>

    {{-- ========================================================= --}}
    {{-- SUMMARY CARDS                                              --}}
    {{-- ========================================================= --}}
    <div class="row g-3 mb-3">
        <div class="col-6 col-md-3">
            <div class="fin-summary-card fin-income">
                <div class="d-flex align-items-center gap-3 mb-2">
                    <div class="fin-card-icon"><i class="bx bx-trending-up"></i></div>
                    <p class="fin-card-label">Total Pemasukan</p>
                </div>
                <div class="fin-card-value">@money($totalIncome)</div>
                <p class="fin-card-sub">Periode: {{ \Carbon\Carbon::parse($startDate)->isoFormat('D MMM') }} –
                    {{ \Carbon\Carbon::parse($endDate)->isoFormat('D MMM Y') }}</p>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="fin-summary-card fin-expense">
                <div class="d-flex align-items-center gap-3 mb-2">
                    <div class="fin-card-icon"><i class="bx bx-trending-down"></i></div>
                    <p class="fin-card-label">Total Pengeluaran</p>
                </div>
                <div class="fin-card-value">@money($totalExpense)</div>
                <p class="fin-card-sub">{{ $totalTransaksi }} transaksi tercatat</p>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="fin-summary-card fin-profit">
                <div class="d-flex align-items-center gap-3 mb-2">
                    <div class="fin-card-icon"><i class="bx bx-wallet-alt"></i></div>
                    <p class="fin-card-label">Laba / Rugi</p>
                </div>
                <div class="fin-card-value" style="color: {{ $labaRugi >= 0 ? '#28c76f' : '#ea5455' }}">
                    {{ $labaRugi >= 0 ? '+' : '' }}@money($labaRugi)
                </div>
                <p class="fin-card-sub">{{ $labaRugi >= 0 ? 'Surplus' : 'Defisit' }} periode ini</p>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="fin-summary-card fin-count">
                <div class="d-flex align-items-center gap-3 mb-2">
                    <div class="fin-card-icon"><i class="bx bx-receipt"></i></div>
                    <p class="fin-card-label">Total Transaksi</p>
                </div>
                <div class="fin-card-value" style="color: #ff9f43">{{ number_format($totalTransaksi) }}</div>
                <p class="fin-card-sub">Gabungan income & expense</p>
            </div>
        </div>
    </div>

    {{-- ========================================================= --}}
    {{-- GRAFIK & SALDO BANK                                        --}}
    {{-- ========================================================= --}}
    <div class="row g-3 mb-3">
        {{-- Grafik --}}
        <div class="col-md-8">
            <div class="fin-chart-card h-100">
                <div class="fin-chart-header">
                    <h6>Arus Kas Harian</h6>
                    <div class="fin-chart-legend">
                        <span><span class="dot" style="background:#28c76f"></span> Pemasukan</span>
                        <span><span class="dot" style="background:#ea5455"></span> Pengeluaran</span>
                    </div>
                </div>
                <div class="p-3">
                    <div id="financial-chart"></div>
                </div>
            </div>
        </div>

        {{-- Saldo per Bank --}}
        <div class="col-md-4">
            <div class="card h-100 rounded-3 border" style="border-color: var(--bs-border-color) !important;">
                <div class="card-header d-flex align-items-center justify-content-between py-2 px-3">
                    <span style="font-size:13px; font-weight:600">Saldo Per Rekening</span>
                    <small class="text-muted" style="font-size:11px">Periode ini</small>
                </div>
                <div class="card-body p-3 d-flex flex-column gap-2 overflow-auto" style="max-height:300px">
                    {{-- Kas Tunai --}}
                    @if ($kasTunaiAccount)
                        <div class="fin-bank-card" style="cursor:pointer"
                            onclick="openEditBank(
                                {{ $kasTunaiAccount->id }},
                                '{{ addslashes($kasTunaiAccount->account_name) }}',
                                '{{ $kasTunaiAccount->tipe_akun }}',
                                '{{ addslashes($kasTunaiAccount->nomor_rekening ?? '') }}',
                                '{{ addslashes($kasTunaiAccount->nama_pemilik ?? '') }}',
                                {{ $kasTunaiAccount->store_id ?? 'null' }},
                                {{ $kasTunaiAccount->saldo_awal ?? 0 }},
                                {{ $kasTunaiAccount->is_active ? 1 : 0 }},
                                '{{ $kasTunaiAccount->logo_bank ? $kasTunaiAccount->logo_url : '' }}'
                            )">
                        @else
                            <div class="fin-bank-card">
                    @endif
                    <div class="fin-bank-inisial" style="background:rgba(255,159,67,.12);color:#ff9f43">
                        <i class="bx bx-money fs-5"></i>
                    </div>
                    <div>
                        <p class="fin-bank-name">{{ $kasTunaiAccount ? $kasTunaiAccount->account_name : 'Kas Tunai' }}</p>
                        <p class="fin-bank-norek">Uang fisik / kasir</p>
                    </div>
                    <div class="fin-bank-saldo">
                        <div class="saldo-val"
                            style="color: {{ $kasSaldoAwal + $kasIncome - $kasExpense >= 0 ? '#28c76f' : '#ea5455' }}">
                            @money($kasSaldoAwal + $kasIncome - $kasExpense)
                        </div>
                        <div class="saldo-lbl">Saldo</div>
                    </div>
                </div>

                @foreach ($saldoPerBank as $item)
                    <div class="fin-bank-card" style="cursor:pointer"
                        onclick="openEditBank(
                                {{ $item['account']->id }},
                                '{{ addslashes($item['account']->account_name) }}',
                                '{{ $item['account']->tipe_akun }}',
                                '{{ addslashes($item['account']->nomor_rekening) }}',
                                '{{ addslashes($item['account']->nama_pemilik) }}',
                                {{ $item['account']->store_id ?? 'null' }},
                                {{ $item['account']->saldo_awal ?? 0 }},
                                {{ $item['account']->is_active ? 1 : 0 }},
                                '{{ $item['account']->logo_bank ? $item['account']->logo_url : '' }}'
                            )">
                        @if ($item['account']->logo_bank)
                            <img src="{{ $item['account']->logo_url }}" alt="{{ $item['account']->account_name }}"
                                class="fin-bank-logo">
                        @else
                            <div class="fin-bank-inisial">{{ $item['account']->inisial }}</div>
                        @endif
                        <div>
                            <p class="fin-bank-name">{{ $item['account']->account_name }}</p>
                            <p class="fin-bank-norek">{{ $item['account']->nomor_rekening }}</p>
                        </div>
                        <div class="fin-bank-saldo">
                            <div class="saldo-val" style="color: {{ $item['saldo'] >= 0 ? '#28c76f' : '#ea5455' }}">
                                @money($item['saldo'])
                            </div>
                            <div class="saldo-lbl">Saldo</div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    </div>

    {{-- ========================================================= --}}
    {{-- DETAIL MUTASI (Breakdown per Metode)                       --}}
    {{-- ========================================================= --}}
    <p class="fin-section-title">Detail Mutasi per Metode Pembayaran</p>
    <div class="row g-3 mb-3">
        @php
            $metodes = [
                'tunai' => ['label' => 'Tunai', 'icon' => 'bx bx-money', 'color' => '#ff9f43'],
                'transfer' => ['label' => 'Transfer', 'icon' => 'bx bx-transfer-alt', 'color' => '#7367f0'],
                'qris' => ['label' => 'QRIS', 'icon' => 'bx bx-qr', 'color' => '#00b4d8'],
            ];
        @endphp

        @foreach ($metodes as $key => $m)
            @php
                if ($key === 'tunai' && isset($kasTunaiAccount)) {
                    $totalIn = $kasIncome;
                    $totalOut = $kasExpense;
                    $txCount = $kasTxCount ?? 0;
                } else {
                    $inc = $breakdownIncome->get($key);
                    $exp = $breakdownExpense->get($key);
                    $totalIn = $inc?->total ?? 0;
                    $totalOut = $exp?->total ?? 0;
                    $txCount = ($inc?->jumlah_transaksi ?? 0) + ($exp?->jumlah_transaksi ?? 0);
                }
            @endphp
            <div class="col-6 col-md-3">
                <div class="card border rounded-3 h-100" style="border-color: var(--bs-border-color) !important;">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <div
                                style="width:34px;height:34px;border-radius:8px;background:{{ $m['color'] }}1a;color:{{ $m['color'] }};display:flex;align-items:center;justify-content:center;font-size:17px">
                                <i class="{{ $m['icon'] }}"></i>
                            </div>
                            <div>
                                <div style="font-size:13px;font-weight:600">{{ $m['label'] }}</div>
                                <div style="font-size:11px;color:var(--bs-secondary-color)">{{ $txCount }} transaksi
                                </div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-between" style="font-size:12px">
                            <span style="color:#28c76f"><i class="bx bx-up-arrow-alt"></i> @money($totalIn)</span>
                            <span style="color:#ea5455"><i class="bx bx-down-arrow-alt"></i> @money($totalOut)</span>
                        </div>
                        <hr class="my-2" style="border-color:var(--bs-border-color)">
                        <div
                            style="font-size:13px;font-weight:600;color:{{ $totalIn - $totalOut >= 0 ? '#28c76f' : '#ea5455' }}">
                            Net: @money($totalIn - $totalOut)
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- ========================================================= --}}
    {{-- TABEL TRANSAKSI TERBARU                                    --}}
    {{-- ========================================================= --}}
    <p class="fin-section-title">Riwayat Transaksi</p>
    <div class="card border rounded-3" style="border-color: var(--bs-border-color) !important;">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="fin-tx-table">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Keterangan</th>
                            <th>Metode</th>
                            <th>Akun</th>
                            <th class="text-end">Jumlah</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($recentTransactions as $tx)
                            @php $isIncome = $tx->type === 'income'; @endphp
                            <tr>
                                <td style="white-space:nowrap;color:var(--bs-secondary-color);font-size:12px">
                                    {{ \Carbon\Carbon::parse($tx->tanggal)->isoFormat('D MMM Y') }}
                                </td>
                                <td>
                                    <div style="font-size:13px">
                                        {{ \Illuminate\Support\Str::limit(strip_tags($tx->keterangan), 55) }}</div>
                                    @if ($tx->transaction_category)
                                        <div style="font-size:11px;color:var(--bs-secondary-color)">
                                            {{ $tx->transaction_category->name }}</div>
                                    @endif
                                </td>
                                <td>
                                    @php $metodeLower = strtolower($tx->metode_pembayaran ?? 'tunai'); @endphp
                                    <span class="fin-tx-badge {{ $metodeLower }}">
                                        {{ ucfirst($metodeLower) }}
                                    </span>
                                </td>
                                <td style="font-size:12px;color:var(--bs-secondary-color)">
                                    {{ $tx->account?->account_name ?? ($metodeLower === 'tunai' ? 'Kas' : '—') }}
                                </td>
                                <td class="text-end">
                                    <span class="fin-amount {{ $tx->type }}">
                                        {{ $isIncome ? '+' : '-' }} @money($tx->nominal)
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4"
                                    style="color:var(--bs-secondary-color);font-size:13px">
                                    <i class="bx bx-info-circle d-block fs-4 mb-1"></i>
                                    Belum ada transaksi pada periode ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>


    {{-- ========================================================= --}}
    {{-- MODAL MUTASI                                               --}}
    {{-- ========================================================= --}}
    {{-- <div class="modal fade fin-modal" id="modalMutasi" tabindex="-1" aria-labelledby="labelMutasi" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form action="{{ route('keuangan.mutasi.store') }}" method="POST" class="modal-content">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="labelMutasi">Tambah Mutasi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">

                    <div class="mb-3">
                        <div class="fin-tipe-switch">
                            <button type="button" class="fin-tipe-btn active masuk" id="btnMasuk"
                                onclick="setTipe('masuk')">
                                <i class="bx bx-up-arrow-alt me-1"></i> Uang Masuk
                            </button>
                            <button type="button" class="fin-tipe-btn keluar" id="btnKeluar"
                                onclick="setTipe('keluar')">
                                <i class="bx bx-down-arrow-alt me-1"></i> Uang Keluar
                            </button>
                        </div>
                        <input type="hidden" name="tipe" id="inputTipe" value="masuk">
                    </div>

                    <div class="row g-3">
                        <div class="col-6">
                            <label class="form-label" style="font-size:12.5px">Tanggal</label>
                            <input type="date" name="tanggal" class="form-control form-control-sm"
                                value="{{ now()->format('Y-m-d') }}" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label" style="font-size:12.5px">Jumlah (Rp)</label>
                            <input type="number" name="jumlah" class="form-control form-control-sm" placeholder="0"
                                min="1" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label" style="font-size:12.5px">Metode Pembayaran</label>
                            <select name="metode_pembayaran" class="form-select form-select-sm" id="mutasiMetode"
                                onchange="toggleMutasiBank(this.value)" required>
                                <option value="tunai">Tunai / Kasir</option>
                                <option value="transfer">Transfer Bank</option>
                                <option value="qris">QRIS</option>
                            </select>
                        </div>
                        <div class="col-12" id="mutasiBankWrap" style="display:none">
                            <label class="form-label" style="font-size:12.5px">Bank</label>
                            <select name="account_id" class="form-select form-select-sm" id="mutasiBank">
                                <option value="">-- Pilih Bank --</option>
                                @foreach ($accounts as $bank)
                                    <option value="{{ $bank->id }}"
                                        {{ Str::lower($bank->account_name) === 'mandiri' ? 'data-default-qris=1' : '' }}>
                                        {{ $bank->account_name }} — {{ $bank->nomor_rekening }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted" id="qrisNote" style="display:none;font-size:11px">
                                <i class="bx bx-info-circle"></i> QRIS default ke Bank Mandiri jika tidak dipilih.
                            </small>
                        </div>
                        <div class="col-12">
                            <label class="form-label" style="font-size:12.5px">Keterangan</label>
                            <textarea name="keterangan" rows="2" class="form-control form-control-sm" placeholder="Deskripsi mutasi..."
                                required></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-outline-secondary"
                        data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-sm btn-success px-4">Simpan Mutasi</button>
                </div>
            </form>
        </div>
    </div> --}}

    {{-- ========================================================= --}}
    {{-- MODAL BANK (Create)                                        --}}
    {{-- ========================================================= --}}
    <div class="modal fade fin-modal" id="modalBank" tabindex="-1" aria-labelledby="labelBank" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form action="{{ route('keuangan.account.store') }}" method="POST" enctype="multipart/form-data"
                class="modal-content" id="formCreateAccount">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="labelBank">Tambah Account</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">

                    {{-- STEP 1: Tipe Akun — field lain baru muncul setelah ini dipilih --}}
                    <div class="mb-3">
                        <label class="form-label" style="font-size:12.5px">Tipe Akun</label>
                        <select name="tipe_akun" class="form-select form-select-sm" id="createTipeAkun"
                            onchange="toggleAccountFields(this.value, 'create')" required>
                            <option value="" selected disabled>-- Pilih tipe akun --</option>
                            <option value="tunai">Tunai</option>
                            <option value="qris">QRIS</option>
                            <option value="bank">Bank</option>
                        </select>
                    </div>

                    {{-- STEP 2: Form lainnya, hidden sampai tipe akun dipilih --}}
                    <div id="createAccountFields" class="d-none">

                        {{-- Preview Logo --}}
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <img id="logoPreview" src="{{ asset('assets/img/logo.png') }}" class="fin-logo-preview"
                                alt="Logo akun">
                            <div class="flex-grow-1">
                                <label class="fin-upload-area" for="logoInput">
                                    <i class="bx bx-cloud-upload"></i>
                                    Klik untuk upload logo (PNG/JPG/WebP, max 1 MB)
                                </label>
                                <input type="file" name="logo_bank" id="logoInput" accept="image/*" class="d-none"
                                    onchange="previewLogo(this)">
                            </div>
                        </div>

                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label" style="font-size:12.5px">Nama akun</label>
                                <input type="text" name="account_name" id="createAccountName"
                                    class="form-control form-control-sm" placeholder="Contoh: Bank BCA / Kas Toko A">
                            </div>
                            <div class="col-12" id="createNorekWrap">
                                <label class="form-label" style="font-size:12.5px">Nomor Rekening</label>
                                <input type="text" name="nomor_rekening" id="createNorek"
                                    class="form-control form-control-sm" placeholder="1234567890">
                            </div>
                            <div class="col-12" id="createPemilikWrap">
                                <label class="form-label" style="font-size:12.5px">Nama Pemilik Rekening</label>
                                <input type="text" name="nama_pemilik" id="createPemilik"
                                    class="form-control form-control-sm" placeholder="Nama sesuai rekening">
                            </div>
                            <div class="col-12">
                                <label class="form-label" style="font-size:12.5px">Toko</label>
                                <select name="store_id" class="form-select form-select-sm" id="createStoreId">
                                    <option value="">-- Pilih toko --</option>
                                    @foreach ($stores as $store)
                                        <option value="{{ $store->id }}">{{ $store->name_toko }}</option>
                                    @endforeach
                                </select>
                                <small class="text-muted" id="createStoreNote" style="font-size:11px"></small>
                            </div>
                            <div class="col-12">
                                <label class="form-label" style="font-size:12.5px">Saldo Awal (Rp)</label>
                                <input type="number" name="saldo_awal" id="createSaldoAwal"
                                    class="form-control form-control-sm" placeholder="0" min="0" step="1"
                                    value="0">
                            </div>
                            <div class="col-12">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="is_active" value="1"
                                        id="bankAktif" checked>
                                    <label class="form-check-label" for="bankAktif" style="font-size:13px">Aktifkan
                                        akun ini</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-outline-secondary"
                        data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-sm btn-primary px-4">Simpan Akun</button>
                </div>
            </form>
        </div>
    </div>

    {{-- ========================================================= --}}
    {{-- MODAL BANK (Edit) — di-trigger dari tabel saldo bank       --}}
    {{-- ========================================================= --}}
    <div class="modal fade fin-modal" id="modalEditBank" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form id="formEditAccount" method="POST" enctype="multipart/form-data">
                    @csrf @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Account</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label" style="font-size:12.5px">Tipe Akun</label>
                            <select name="tipe_akun" class="form-select form-select-sm" id="editTipeAkun"
                                onchange="toggleAccountFields(this.value, 'edit')" required>
                                <option value="tunai">Tunai</option>
                                <option value="qris">QRIS</option>
                                <option value="bank">Bank</option>
                            </select>
                        </div>

                        <div id="editAccountFields">
                            <div class="d-flex align-items-center gap-3 mb-3">
                                <img id="editLogoPreview" src="{{ asset('assets/img/logo.png') }}"
                                    class="fin-logo-preview" alt="Logo">
                                <div class="flex-grow-1">
                                    <label class="fin-upload-area" for="editLogoInput">
                                        <i class="bx bx-cloud-upload"></i>
                                        Ganti logo (kosongkan = tetap pakai yang lama)
                                    </label>
                                    <input type="file" name="logo_bank" id="editLogoInput" accept="image/*"
                                        class="d-none" onchange="previewEditLogo(this)">
                                </div>
                            </div>
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label" style="font-size:12.5px">Nama Akun</label>
                                    <input type="text" name="account_name" id="editNamaBank"
                                        class="form-control form-control-sm" required>
                                </div>
                                <div class="col-12" id="editNorekWrap">
                                    <label class="form-label" style="font-size:12.5px">Nomor Rekening</label>
                                    <input type="text" name="nomor_rekening" id="editNomorRekening"
                                        class="form-control form-control-sm">
                                </div>
                                <div class="col-12" id="editPemilikWrap">
                                    <label class="form-label" style="font-size:12.5px">Nama Pemilik</label>
                                    <input type="text" name="nama_pemilik" id="editNamaPemilik"
                                        class="form-control form-control-sm">
                                </div>
                                <div class="col-12">
                                    <label class="form-label" style="font-size:12.5px">Toko</label>
                                    <select name="store_id" class="form-select form-select-sm" id="editStoreId">
                                        <option value="">-- Pilih toko --</option>
                                        @foreach ($stores as $store)
                                            <option value="{{ $store->id }}">{{ $store->name_toko }}</option>
                                        @endforeach
                                    </select>
                                    <small class="text-muted" id="editStoreNote" style="font-size:11px"></small>
                                </div>
                                <div class="col-12">
                                    <label class="form-label" style="font-size:12.5px">Saldo Awal (Rp)</label>
                                    <input type="number" name="saldo_awal" id="editSaldoAwal"
                                        class="form-control form-control-sm" min="0" step="1">
                                </div>
                                <div class="col-12">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="is_active" value="1"
                                            id="editBankAktif">
                                        <label class="form-check-label" for="editBankAktif" style="font-size:13px">Akun
                                            aktif</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer d-flex justify-content-between">
                        <button type="submit" form="formDeleteBank" class="btn btn-sm btn-outline-danger"
                            onclick="return confirm('Hapus Akun ini?')">
                            <i class="bx bx-trash me-1"></i> Hapus
                        </button>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-sm btn-outline-secondary"
                                data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-sm btn-primary px-4">Perbarui</button>
                        </div>
                    </div>
                </form>
                {{-- Form terpisah (bukan nested) khusus untuk hapus akun. Tombol "Hapus" di atas
                     terhubung ke form ini lewat atribut form="formDeleteBank" --}}
                <form id="formDeleteBank" method="POST" class="d-none">
                    @csrf @method('DELETE')
                </form>
            </div>
        </div>
    </div>

@endsection

@php
    $keuanganConfig = [
        'startDate' => $startDate,
        'endDate' => $endDate,
        'chartData' => $chartData,
        'accountBaseUrl' => url('keuangan/account'),
        'defaultLogoUrl' => asset('assets/img/logo.png'),
    ];
@endphp

@section('page-script')
    <script>
        window.keuanganConfig = @json($keuanganConfig);
    </script>
    @vite(['resources/assets/js/keuangan.js'])
@endsection
