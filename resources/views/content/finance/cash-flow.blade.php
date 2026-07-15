@extends('layouts/contentNavbarLayout')

@section('title', 'Dashboard Keuangan')

@section('page-style')
    @vite(['resources/assets/vendor/scss/pages/page-bank.scss'])
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
                <button type="button" class="fin-pill {{ request('metode') == 'store' ? 'active' : '' }}"
                    data-metode="store" onclick="setMetode('store')">
                    <i class="bx bx-store-alt"></i> Store
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
                <select name="bank_id" class="form-select form-select-sm fin-bank-select" id="bankSelect"
                    onchange="document.getElementById('filterForm').submit()">
                    <option value="">Semua Bank</option>
                    @foreach ($banks as $bank)
                        <option value="{{ $bank->id }}" {{ request('bank_id') == $bank->id ? 'selected' : '' }}>
                            {{ $bank->nama_bank }}
                        </option>
                    @endforeach
                </select>
            </div>

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
                <button type="submit" class="btn btn-sm btn-primary px-3">
                    <i class="bx bx-filter-alt me-1"></i> Terapkan
                </button>
                <a href="{{ route('keuangan') }}" class="btn btn-sm btn-outline-secondary px-3">
                    <i class="bx bx-reset me-1"></i>
                </a>
                {{-- Tombol Tambah Mutasi --}}
                <button type="button" class="btn btn-sm btn-success px-3" data-bs-toggle="modal"
                    data-bs-target="#modalMutasi">
                    <i class="bx bx-plus me-1"></i> Mutasi
                </button>
                {{-- Tombol Kelola Bank --}}
                <button type="button" class="btn btn-sm btn-outline-primary px-3" data-bs-toggle="modal"
                    data-bs-target="#modalBank">
                    <i class="bx bx-bank me-1"></i> Bank
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
                    @php
                        $kasIncome = $breakdownIncome->get('tunai')?->total ?? 0;
                        $kasExpense = $breakdownExpense->get('tunai')?->total ?? 0;
                    @endphp
                    <div class="fin-bank-card">
                        <div class="fin-bank-inisial" style="background:rgba(255,159,67,.12);color:#ff9f43">
                            <i class="bx bx-money fs-5"></i>
                        </div>
                        <div>
                            <p class="fin-bank-name">Kas Tunai</p>
                            <p class="fin-bank-norek">Uang fisik / kasir</p>
                        </div>
                        <div class="fin-bank-saldo">
                            <div class="saldo-val"
                                style="color: {{ $kasIncome - $kasExpense >= 0 ? '#28c76f' : '#ea5455' }}">
                                @money($kasIncome - $kasExpense)
                            </div>
                            <div class="saldo-lbl">Saldo</div>
                        </div>
                    </div>

                    @foreach ($saldoPerBank as $item)
                        <div class="fin-bank-card" style="cursor:pointer"
                            onclick="openEditBank(
                                {{ $item['bank']->id }},
                                '{{ addslashes($item['bank']->nama_bank) }}',
                                '{{ addslashes($item['bank']->nomor_rekening) }}',
                                '{{ addslashes($item['bank']->nama_pemilik) }}',
                                {{ $item['bank']->is_active ? 1 : 0 }},
                                '{{ $item['bank']->logo_bank ? $item['bank']->logo_url : '' }}'
                            )">
                            @if ($item['bank']->logo_bank)
                                <img src="{{ $item['bank']->logo_url }}" alt="{{ $item['bank']->nama_bank }}"
                                    class="fin-bank-logo">
                            @else
                                <div class="fin-bank-inisial">{{ $item['bank']->inisial }}</div>
                            @endif
                            <div>
                                <p class="fin-bank-name">{{ $item['bank']->nama_bank }}</p>
                                <p class="fin-bank-norek">{{ $item['bank']->nomor_rekening }}</p>
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
                'store' => ['label' => 'Store', 'icon' => 'bx bx-store-alt', 'color' => '#20c997'],
                'tunai' => ['label' => 'Tunai', 'icon' => 'bx bx-money', 'color' => '#ff9f43'],
                'transfer' => ['label' => 'Transfer', 'icon' => 'bx bx-transfer-alt', 'color' => '#7367f0'],
                'qris' => ['label' => 'QRIS', 'icon' => 'bx bx-qr', 'color' => '#00b4d8'],
            ];
        @endphp

        @foreach ($metodes as $key => $m)
            @php
                $inc = $breakdownIncome->get($key);
                $exp = $breakdownExpense->get($key);
                $totalIn = $inc?->total ?? 0;
                $totalOut = $exp?->total ?? 0;
                $txCount = ($inc?->jumlah_transaksi ?? 0) + ($exp?->jumlah_transaksi ?? 0);
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
                            <th>Bank / Rekening</th>
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
                                    <span class="fin-tx-badge {{ $tx->metode_pembayaran ?? 'tunai' }}">
                                        {{ ucfirst($tx->metode_pembayaran ?? 'tunai') }}
                                    </span>
                                </td>
                                <td style="font-size:12px;color:var(--bs-secondary-color)">
                                    {{ $tx->bank?->nama_bank ?? (in_array($tx->metode_pembayaran, ['tunai', 'store']) ? 'Kas' : '—') }}
                                </td>
                                <td class="text-end">
                                    <span class="fin-amount {{ $tx->type }}">
                                        {{ $isIncome ? '+' : '-' }} @money($tx->jumlah)
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
    <div class="modal fade fin-modal" id="modalMutasi" tabindex="-1" aria-labelledby="labelMutasi" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            {{-- Sekarang nulis ke cash_flows (type: income/expense/transfer), bukan tabel mutasi lama --}}
            <form action="{{ route('financial.cash-flows.quick-store') }}" method="POST" class="modal-content">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="labelMutasi">Tambah Mutasi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">

                    {{-- Toggle Masuk / Keluar / Transfer --}}
                    <div class="mb-3">
                        <div class="fin-tipe-switch">
                            <button type="button" class="fin-tipe-btn active masuk" id="btnMasuk"
                                onclick="setTipe('income')">
                                <i class="bx bx-up-arrow-alt me-1"></i> Uang Masuk
                            </button>
                            <button type="button" class="fin-tipe-btn keluar" id="btnKeluar"
                                onclick="setTipe('expense')">
                                <i class="bx bx-down-arrow-alt me-1"></i> Uang Keluar
                            </button>
                            <button type="button" class="fin-tipe-btn transfer" id="btnTransfer"
                                onclick="setTipe('transfer')">
                                <i class="bx bx-transfer-alt me-1"></i> Transfer
                            </button>
                        </div>
                        <input type="hidden" name="type" id="inputTipe" value="income">
                    </div>

                    <div class="row g-3">
                        <div class="col-6">
                            <label class="form-label" style="font-size:12.5px">Tanggal</label>
                            <input type="date" name="tanggal" class="form-control form-control-sm"
                                value="{{ now()->format('Y-m-d') }}" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label" style="font-size:12.5px">Nominal (Rp)</label>
                            <input type="number" name="nominal" class="form-control form-control-sm" placeholder="0"
                                min="1" required>
                        </div>

                        {{-- Kategori — hanya untuk Masuk/Keluar, disembunyikan saat Transfer --}}
                        <div class="col-12" id="mutasiKategoriWrap">
                            <label class="form-label" style="font-size:12.5px">Kategori</label>
                            <select name="transaction_category_id" class="form-select form-select-sm"
                                id="mutasiKategori">
                                <option value="">-- Pilih Kategori --</option>
                                @foreach ($kategoriIncome ?? [] as $kategori)
                                    <option value="{{ $kategori->id }}" data-tipe="income">{{ $kategori->name }}
                                    </option>
                                @endforeach
                                @foreach ($kategoriExpense ?? [] as $kategori)
                                    <option value="{{ $kategori->id }}" data-tipe="expense">{{ $kategori->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-12">
                            <label class="form-label" style="font-size:12.5px" id="mutasiMetodeLabel">Metode
                                Pembayaran</label>
                            <select name="metode_pembayaran" class="form-select form-select-sm" id="mutasiMetode"
                                onchange="toggleMutasiBank(this.value, 'mutasiBankWrap')" required>
                                <option value="TUNAI">Tunai / Kasir</option>
                                <option value="TRANSFER">Transfer Bank</option>
                                <option value="QRIS">QRIS</option>
                            </select>
                        </div>
                        <div class="col-12" id="mutasiBankWrap" style="display:none">
                            <label class="form-label" style="font-size:12.5px">Bank</label>
                            <select name="bank_id" class="form-select form-select-sm" id="mutasiBank">
                                <option value="">-- Pilih Bank --</option>
                                @foreach ($banks as $bank)
                                    <option value="{{ $bank->id }}"
                                        {{ Str::lower($bank->nama_bank) === 'mandiri' ? 'data-default-qris=1' : '' }}>
                                        {{ $bank->nama_bank }} — {{ $bank->nomor_rekening }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted" id="qrisNote" style="display:none;font-size:11px">
                                <i class="bx bx-info-circle"></i> QRIS default ke Bank Mandiri jika tidak dipilih.
                            </small>
                        </div>

                        {{-- Tujuan — hanya muncul saat Transfer --}}
                        <div class="col-12" id="mutasiTujuanWrap" style="display:none">
                            <label class="form-label" style="font-size:12.5px">Ke (Metode Tujuan)</label>
                            <select name="metode_pembayaran_tujuan" class="form-select form-select-sm"
                                id="mutasiMetodeTujuan" onchange="toggleMutasiBank(this.value, 'mutasiBankTujuanWrap')">
                                <option value="TUNAI">Tunai / Kasir</option>
                                <option value="TRANSFER">Transfer Bank</option>
                                <option value="QRIS">QRIS</option>
                            </select>
                        </div>
                        <div class="col-12" id="mutasiBankTujuanWrap" style="display:none">
                            <label class="form-label" style="font-size:12.5px">Rekening Tujuan</label>
                            <select name="bank_id_tujuan" class="form-select form-select-sm" id="mutasiBankTujuan">
                                <option value="">-- Pilih Bank --</option>
                                @foreach ($banks as $bank)
                                    <option value="{{ $bank->id }}">{{ $bank->nama_bank }} —
                                        {{ $bank->nomor_rekening }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-12">
                            <label class="form-label" style="font-size:12.5px">Keterangan</label>
                            <textarea name="keterangan" rows="2" class="form-control form-control-sm" id="mutasiKeterangan"
                                placeholder="Deskripsi mutasi..."></textarea>
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
    </div>

    {{-- ========================================================= --}}
    {{-- MODAL BANK (Create)                                        --}}
    {{-- ========================================================= --}}
    <div class="modal fade fin-modal" id="modalBank" tabindex="-1" aria-labelledby="labelBank" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form action="{{ route('keuangan.bank.store') }}" method="POST" enctype="multipart/form-data"
                class="modal-content">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="labelBank">Tambah Bank</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">

                    {{-- Preview Logo --}}
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <img id="logoPreview" src="{{ asset('assets/img/logo.png') }}" class="fin-logo-preview"
                            alt="Logo Bank">
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
                            <label class="form-label" style="font-size:12.5px">Nama Bank</label>
                            <input type="text" name="nama_bank" class="form-control form-control-sm"
                                placeholder="Contoh: Bank BCA" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label" style="font-size:12.5px">Nomor Rekening</label>
                            <input type="text" name="nomor_rekening" class="form-control form-control-sm"
                                placeholder="1234567890" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label" style="font-size:12.5px">Nama Pemilik Rekening</label>
                            <input type="text" name="nama_pemilik" class="form-control form-control-sm"
                                placeholder="Nama sesuai rekening" required>
                        </div>
                        <div class="col-12">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="is_active" value="1"
                                    id="bankAktif" checked>
                                <label class="form-check-label" for="bankAktif" style="font-size:13px">Aktifkan bank
                                    ini</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-outline-secondary"
                        data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-sm btn-primary px-4">Simpan Bank</button>
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
                <form id="formEditBank" method="POST" enctype="multipart/form-data">
                    @csrf @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Bank</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <img id="editLogoPreview" src="{{ asset('assets/img/logo.png') }}" class="fin-logo-preview"
                                alt="Logo">
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
                                <label class="form-label" style="font-size:12.5px">Nama Bank</label>
                                <input type="text" name="nama_bank" id="editNamaBank"
                                    class="form-control form-control-sm" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label" style="font-size:12.5px">Nomor Rekening</label>
                                <input type="text" name="nomor_rekening" id="editNomorRekening"
                                    class="form-control form-control-sm" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label" style="font-size:12.5px">Nama Pemilik</label>
                                <input type="text" name="nama_pemilik" id="editNamaPemilik"
                                    class="form-control form-control-sm" required>
                            </div>
                            <div class="col-12">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="is_active" value="1"
                                        id="editBankAktif">
                                    <label class="form-check-label" for="editBankAktif" style="font-size:13px">Bank
                                        aktif</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer d-flex justify-content-between">
                        <form id="formDeleteBank" method="POST" onsubmit="return confirm('Hapus bank ini?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                <i class="bx bx-trash me-1"></i> Hapus
                            </button>
                        </form>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-sm btn-outline-secondary"
                                data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-sm btn-primary px-4">Perbarui</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@section('page-script')

    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        // ============================================================
        // Flatpickr — Date Range
        // ============================================================
        const fpRange = flatpickr('#dateRange', {
            mode: 'range',
            locale: 'id',
            dateFormat: 'Y-m-d',
            defaultDate: ['{{ $startDate }}', '{{ $endDate }}'],
            altInput: true,
            altFormat: 'j M Y',
            onChange: function(dates) {
                if (dates.length === 2) {
                    document.getElementById('startDateInput').value = flatpickr.formatDate(dates[0], 'Y-m-d');
                    document.getElementById('endDateInput').value = flatpickr.formatDate(dates[1], 'Y-m-d');
                }
            }
        });

        // ============================================================
        // Filter metode pembayaran
        // ============================================================
        function setMetode(val) {
            document.getElementById('inputMetode').value = val;
            const bankWrap = document.getElementById('bankSelectWrap');
            bankWrap.classList.toggle('d-none', !['transfer', 'qris'].includes(val));
            document.getElementById('filterForm').submit();
        }

        // ============================================================
        // Modal Mutasi — tipe income/expense/transfer (nulis ke cash_flows)
        // ============================================================
        function setTipe(type) {
            document.getElementById('inputTipe').value = type;
            document.getElementById('btnMasuk').classList.toggle('active', type === 'income');
            document.getElementById('btnKeluar').classList.toggle('active', type === 'expense');
            document.getElementById('btnTransfer').classList.toggle('active', type === 'transfer');

            const isTransfer = type === 'transfer';

            // Kategori cuma untuk income/expense
            document.getElementById('mutasiKategoriWrap').style.display = isTransfer ? 'none' : '';
            document.getElementById('mutasiKategori').required = !isTransfer;

            // Filter opsi kategori sesuai tipe yang dipilih
            document.querySelectorAll('#mutasiKategori option[data-tipe]').forEach(function(opt) {
                opt.hidden = opt.dataset.tipe !== type;
            });
            if (!isTransfer) document.getElementById('mutasiKategori').value = '';

            // Field tujuan cuma untuk transfer
            document.getElementById('mutasiTujuanWrap').style.display = isTransfer ? '' : 'none';
            document.getElementById('mutasiMetodeLabel').textContent = isTransfer ? 'Dari (Metode)' : 'Metode Pembayaran';
            document.getElementById('mutasiKeterangan').required = !isTransfer;
            document.getElementById('mutasiKeterangan').placeholder = isTransfer ? 'Transfer Internal (opsional)' :
                'Deskripsi mutasi...';

            if (!isTransfer) {
                document.getElementById('mutasiBankTujuanWrap').style.display = 'none';
            } else {
                toggleMutasiBank(document.getElementById('mutasiMetodeTujuan').value, 'mutasiBankTujuanWrap');
            }
        }

        // Modal Mutasi — tampilkan select bank (dipakai untuk sisi asal & tujuan)
        function toggleMutasiBank(metode, wrapId) {
            const wrap = document.getElementById(wrapId);
            const isBankWrap = wrapId === 'mutasiBankWrap';
            const note = isBankWrap ? document.getElementById('qrisNote') : null;
            const select = isBankWrap ? document.getElementById('mutasiBank') : document.getElementById('mutasiBankTujuan');
            const show = metode === 'TRANSFER' || metode === 'QRIS';

            wrap.style.display = show ? '' : 'none';
            if (note) note.style.display = metode === 'QRIS' ? '' : 'none';

            // Otopilot: pilih Mandiri jika QRIS
            if (isBankWrap && metode === 'QRIS') {
                const mandiriOpt = select.querySelector('[data-default-qris="1"]');
                if (mandiriOpt) select.value = mandiriOpt.value;
            }
        }

        // ============================================================
        // Preview logo bank (create)
        // ============================================================
        function previewLogo(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = e => document.getElementById('logoPreview').src = e.target.result;
                reader.readAsDataURL(input.files[0]);
            }
        }

        function previewEditLogo(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = e => document.getElementById('editLogoPreview').src = e.target.result;
                reader.readAsDataURL(input.files[0]);
            }
        }

        // ============================================================
        // Isi modal edit bank
        // ============================================================
        function openEditBank(id, nama, norek, pemilik, isActive, logoUrl) {
            const base = '{{ url('keuangan/bank') }}';
            document.getElementById('formEditBank').action = `${base}/${id}`;
            document.getElementById('formDeleteBank').action = `${base}/${id}`;
            document.getElementById('editNamaBank').value = nama;
            document.getElementById('editNomorRekening').value = norek;
            document.getElementById('editNamaPemilik').value = pemilik;
            document.getElementById('editBankAktif').checked = isActive == 1;
            document.getElementById('editLogoPreview').src = logoUrl || '{{ asset('assets/img/banks/default-bank.png') }}';
            new bootstrap.Modal(document.getElementById('modalEditBank')).show();
        }

        // ============================================================
        // ApexCharts
        // ============================================================
        document.addEventListener('DOMContentLoaded', function() {
            const chartData = @json($chartData);

            const opts = {
                series: [{
                        name: 'Pemasukan',
                        data: chartData.income
                    },
                    {
                        name: 'Pengeluaran',
                        data: chartData.expense
                    },
                ],
                chart: {
                    type: 'area',
                    height: 280,
                    toolbar: {
                        show: false
                    },
                    zoom: {
                        enabled: false
                    },
                    sparkline: {
                        enabled: false
                    },
                    fontFamily: 'inherit',
                },
                colors: ['#28c76f', '#ea5455'],
                fill: {
                    type: 'gradient',
                    gradient: {
                        shadeIntensity: 1,
                        opacityFrom: 0.25,
                        opacityTo: 0.02,
                        stops: [0, 95, 100]
                    }
                },
                stroke: {
                    curve: 'smooth',
                    width: 2.5
                },
                dataLabels: {
                    enabled: false
                },
                xaxis: {
                    categories: chartData.labels,
                    axisBorder: {
                        show: false
                    },
                    axisTicks: {
                        show: false
                    },
                    labels: {
                        style: {
                            fontSize: '11px',
                            colors: '#6c757d'
                        }
                    },
                },
                yaxis: {
                    labels: {
                        formatter: v => 'Rp ' + new Intl.NumberFormat('id-ID').format(v),
                        style: {
                            fontSize: '11px',
                            colors: '#6c757d'
                        },
                    }
                },
                grid: {
                    borderColor: 'rgba(0,0,0,.06)',
                    strokeDashArray: 4,
                    xaxis: {
                        lines: {
                            show: false
                        }
                    },
                },
                tooltip: {
                    y: {
                        formatter: v => new Intl.NumberFormat('id-ID', {
                            style: 'currency',
                            currency: 'IDR',
                            maximumFractionDigits: 0
                        }).format(v)
                    }
                },
                legend: {
                    position: 'top',
                    horizontalAlign: 'right',
                    fontSize: '12px',
                    markers: {
                        width: 8,
                        height: 8,
                        radius: 4
                    },
                },
            };

            new ApexCharts(document.getElementById('financial-chart'), opts).render();
        });
    </script>
@endsection
