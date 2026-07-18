@extends('layouts/contentNavbarLayout')

@section('title', 'Detail Purchase - ' . $pembelian->referensi)
@section('content')


    <div class="card rounded-3 shadow-sm border-0 printable-area">

        <!-- HEADER -->
        <div class="card-header bg-transparent border-bottom pt-4 pb-3">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                <div class="d-flex align-items-center gap-2">
                    <a href="{{ url()->previous() }}" class="btn btn-icon btn-outline-secondary btn-sm"
                        data-bs-toggle="tooltip" title="Kembali">
                        <i class="bx bx-arrow-back"></i>
                    </a>
                    <h5 class="mb-0 fw-bold">Detail Purchase <span
                            class="text-muted fs-6 ms-1">#{{ $pembelian->referensi }}</span></h5>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('pembelian.thermal', $pembelian->referensi) }}" target="_blank"
                        class="btn btn-sm btn-dark flex-grow-1 flex-md-grow-0">
                        <i class="bx bx-receipt me-1"></i> Struk
                    </a>
                    <a href="{{ route('pembelian.pdf', $pembelian->referensi) }}" target="_blank"
                        class="btn btn-sm btn-outline-danger px-3" data-bs-toggle="tooltip" title="Download PDF">
                        <i class="bx bxs-file-pdf"></i>
                    </a>
                </div>
            </div>
        </div>

        <div class="card-body p-4">
            <!-- INFO GRID SECTION -->
            <div class="row g-4 mb-5">

                <!-- Info Toko -->
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="border rounded-3 p-3 h-100 bg-label-light">
                        <span class="d-block text-muted text-xs fw-bold text-uppercase mb-2">Dari (Penerima)</span>
                        <h6 class="text-dark fw-bold mb-1"><i class="bx bx-store-alt text-muted me-1"></i>
                            {{ $profilToko->name_toko ?? 'Toko Saya' }}</h6>
                        <p class="text-sm mb-2 text-wrap">{{ $profilToko->alamat ?? 'Alamat toko belum diatur' }}</p>
                        <div class="text-sm d-flex align-items-center mb-1"><i class="bx bx-envelope text-muted me-2"></i>
                            {{ $profilToko->email ?? '-' }}</div>
                        <div class="text-sm d-flex align-items-center"><i class="bx bx-phone text-muted me-2"></i>
                            {{ $profilToko->telepon ?? '-' }}</div>
                    </div>
                </div>

                <!-- Info Supplier -->
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="border rounded-3 p-3 h-100 bg-white">
                        <span class="d-block text-muted text-xs fw-bold text-uppercase mb-2">Kepada (Supplier)</span>
                        <h6 class="text-dark fw-bold mb-1"><i class="bx bxs-truck text-primary me-1"></i>
                            {{ $pembelian->supplier->name ?? 'Supplier Dihapus' }}</h6>
                        <p class="text-sm mb-2 text-wrap">{{ $pembelian->supplier->alamat ?? 'Alamat tidak tersedia' }}
                        </p>
                        <div class="text-sm d-flex align-items-center mb-1"><i class="bx bx-envelope text-muted me-2"></i>
                            {{ $pembelian->supplier->email ?? '-' }}
                        </div>
                        <div class="text-sm d-flex align-items-center"><i class="bx bx-phone text-muted me-2"></i>
                            {{ $pembelian->supplier->kontak ?? '-' }}</div>
                    </div>
                </div>

                <!-- Info Transaksi -->
                <div class="col-12 col-lg-4">
                    <div class="border rounded-3 p-3 h-100 bg-white">
                        <span class="d-block text-muted text-xs fw-bold text-uppercase mb-3">Data Transaksi</span>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-sm text-muted">Tanggal:</span>
                            <span
                                class="text-sm fw-semibold">{{ \Carbon\Carbon::parse($pembelian->tanggal_pembelian)->translatedFormat('d F Y') }}</span>
                        </div>
                        @if ($pembelian->status_pembayaran !== 'Lunas' && $pembelian->tanggal_jatuh_tempo)
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-sm text-muted">Jatuh Tempo:</span>
                                <span class="text-sm fw-semibold">
                                    {{ \Carbon\Carbon::parse($pembelian->tanggal_jatuh_tempo)->translatedFormat('d F Y') }}
                                </span>
                            </div>
                        @endif
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-sm text-muted">Dibuat Oleh:</span>
                            <span class="text-sm fw-semibold">{{ $pembelian->user->name ?? 'Sistem' }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-sm text-muted">Status Barang:</span>
                            <span
                                class="badge {{ $pembelian->status_barang == 'Diterima' ? 'bg-label-success' : ($pembelian->status_barang == 'Dibatalkan' ? 'bg-label-danger' : 'bg-label-warning') }}">{{ $pembelian->status_barang }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-sm text-muted">Status Bayar:</span>
                            <span
                                class="badge {{ $pembelian->status_pembayaran == 'Lunas' ? 'bg-label-success' : ($pembelian->status_pembayaran == 'Dibatalkan' ? 'bg-label-danger' : 'bg-label-warning') }}">{{ $pembelian->status_pembayaran }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TABEL ITEM -->
            <div class="mb-4">
                <h6 class="fw-bold mb-3">Rincian Produk</h6>
                <div class="table-responsive text-nowrap border rounded-3">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-secondary text-dark">
                            <tr>
                                <th class="text-center text-xs fw-bold py-3" width="5%">No</th>
                                <th class="text-xs fw-bold py-3">Nama Product</th>
                                <th class="text-center text-xs fw-bold py-3" width="10%">Qty</th>
                                <th class="text-end text-xs fw-bold py-3" width="15%">Harga Beli</th>
                                <th class="text-end text-xs fw-bold py-3" width="15%">Diskon</th>
                                <th class="text-end text-xs fw-bold py-3" width="15%">Subtotal</th>
                                <th class=""></th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                            @foreach ($pembelian->details as $detail)
                                <tr>
                                    <td class="text-center text-sm">{{ $loop->iteration }}</td>
                                    <td>
                                        <div class="d-flex flex-column">
                                            <span class="text-sm fw-semibold text-dark">
                                                {{ $detail->produk->name_product ?? ' - ' }}
                                            </span>

                                            {{-- MENDUKUNG VARIAN: Jika punya varian, panggil atribut label --}}
                                            @if ($detail->product_variant_id && $detail->varian && $detail->varian->label)
                                                <small class="text-muted">{{ $detail->varian->label }}</small>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="text-center text-sm">{{ $detail->qty }}</td>
                                    <td class="text-end text-sm">Rp
                                        {{ number_format($detail->harga_beli, 0, ',', '.') }}</td>
                                    <td class="text-end text-sm text-danger">
                                        {{ $detail->diskon > 0 ? '- Rp ' . number_format($detail->diskon, 0, ',', '.') : 'Rp 0' }}
                                    </td>
                                    <td class="text-end text-sm fw-semibold">Rp
                                        {{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                                    <td class="text-center text-sm">
                                        @if ($detail->produk->wajib_seri ?? false)
                                            <a href="{{ route('serial-number.index', $detail->produk->slug) }}?purchase_id={{ $pembelian->id }}@if ($detail->product_variant_id) &variant_id={{ $detail->product_variant_id }} @endif"
                                                class="btn btn-xs btn-outline-primary" data-bs-toggle="tooltip"
                                                title="Daftarkan Nomor Seri">
                                                <i class="bx bx-barcode"></i>
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- KALKULASI TOTAL -->
            <div class="row justify-content-end">
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="bg-label-light p-3 rounded-3 border">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-sm text-muted">Subtotal Produk</span>
                            <span class="text-sm fw-semibold">Rp
                                {{ number_format($pembelian->subtotal, 0, ',', '.') }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-sm text-muted">Total PPN (Tax)</span>
                            <span class="text-sm fw-semibold">Rp
                                {{ number_format($pembelian->pajak, 0, ',', '.') }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-sm text-muted">Diskon Tambahan</span>
                            <span class="text-sm fw-semibold text-danger">- Rp
                                {{ number_format($pembelian->diskon, 0, ',', '.') }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-3">
                            <span class="text-sm text-muted">Ongkos Kirim</span>
                            <span class="text-sm fw-semibold">Rp
                                {{ number_format($pembelian->ongkir, 0, ',', '.') }}</span>
                        </div>

                        <hr class="border-secondary opacity-25 my-2">

                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="text-dark fw-bold text-uppercase">Total Akhir</span>
                            <span class="fw-bolder fs-5 text-primary">Rp
                                {{ number_format($pembelian->total_akhir, 0, ',', '.') }}</span>
                        </div>

                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-sm text-muted">Dibayar</span>
                            <span class="text-sm fw-semibold">Rp
                                {{ number_format($pembelian->jumlah_dibayar, 0, ',', '.') }}</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-sm text-dark fw-bold">Sisa Tagihan</span>
                            @if ($pembelian->sisa_hutang > 0)
                                <span class="text-sm fw-bold text-danger">Rp
                                    {{ number_format($pembelian->sisa_hutang, 0, ',', '.') }}</span>
                            @else
                                <span class="text-sm fw-bold text-success">Lunas</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Hutang --}}
            <div class="mb-4 mt-5">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="fw-bold mb-0"><i class="bx bx-history me-1 text-primary"></i> Riwayat Pembayaran</h6>
                    @can('edit-pembelian')
                        @if ($pembelian->sisa_hutang > 0 && $pembelian->status_pembayaran !== 'Batal')
                            <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                data-bs-target="#addCicilanModal">
                                <i class="bx bx-plus me-1"></i> Bayar Cicilan
                            </button>
                        @endif
                    @endcan
                </div>

                <div class="table-responsive border rounded-3 bg-white">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light text-dark">
                            <tr>
                                <th class="text-center py-3 fw-bold" width="5%">No</th>
                                <th class="py-3 fw-bold">Tanggal Bayar</th>
                                <th class="py-3 fw-bold">Pemasok / Kasir</th>
                                <th class="py-3 fw-bold">Metode</th>
                                <th class="py-3 fw-bold">Referensi / Catatan</th>
                                <th class="text-end py-3 fw-bold" width="20%">Jumlah Bayar</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($pembelian->payments as $key => $payment)
                                <tr>
                                    <td class="text-center">{{ $key + 1 }}.</td>

                                    <td>
                                        <span class="fw-semibold">
                                            {{ \Carbon\Carbon::parse($payment->tanggal_bayar)->translatedFormat('d F Y') }}
                                        </span>
                                    </td>
                                    <td>{{ $payment->user->name }}</td>

                                    {{-- Metode --}}
                                    <td>
                                        <span class="badge badge-xs bg-label-info">
                                            {{ $payment->metode_pembayaran ?? '-' }}
                                        </span>
                                        @if ($payment->account)
                                            <div>
                                                <img src="{{ $payment->account->logo_url }}"
                                                    alt="{{ $payment->account->account_name ?? 'bank' }}"
                                                    style="width:40px; height:40px; object-fit:contain">

                                            </div>
                                        @endif
                                    </td>

                                    {{-- Referensi / Catatan --}}
                                    <td>
                                        @if ($payment->referensi_pembayaran)
                                            <small
                                                class="d-inline-block bg-label-primary px-2 py-1 rounded-2 mb-1 fw-semibold">
                                                Ref: {{ $payment->referensi_pembayaran }}
                                            </small>
                                        @endif
                                        <div class="text-muted" style="font-size:12px">
                                            {!! $payment->catatan ?? '-' !!}
                                        </div>
                                    </td>

                                    {{-- Jumlah --}}
                                    <td class="text-end">
                                        <span class="fw-bold text-success">
                                            Rp {{ number_format($payment->jumlah_bayar, 0, ',', '.') }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-5 text-muted">
                                        <div class="d-flex flex-column justify-content-center align-items-center ">
                                            <i class="bx bx-credit-card" style="font-size:32px;opacity:.4"></i>
                                            <span style="font-size:13px">Belum ada rekaman pembayaran cicilan untuk
                                                transaksi ini.</span>
                                        </div>
                                    </td>
                            @endforelse
                        </tbody>

                        {{-- Footer Total --}}
                        @if (($pembelian->payments ?? collect())->isNotEmpty())
                            <tfoot>
                                <tr class="bg-label-secondary">
                                    <td colspan="5" class="py-3 px-3 fw-bold">
                                        Total Pembayaran
                                    </td>
                                    <td class="text-end text-blue py-3 ">
                                        <span class="fw-bold">Rp
                                            {{ number_format($pembelian->payments->sum('jumlah_bayar'), 0, ',', '.') }}</span>
                                    </td>
                                </tr>
                            </tfoot>
                        @endif
                    </table>
                </div>
            </div>

            <!-- CATATAN -->
            @if ($pembelian->catatan)
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="alert alert-secondary border-0 mb-0">
                            <h6 class="alert-heading fw-bold mb-1"><i class="bx bx-notepad me-1"></i> Catatan
                                Transaksi</h6>
                            <div class="text-sm text-dark mb-0">
                                {!! $pembelian->catatan !!}
                            </div>
                        </div>
                    </div>
                </div>
            @endif

        </div>
    </div>


    {{-- Modal: Input Cicilan Baru --}}
    @can('edit-pembelian')
        <div class="modal fade" id="addCicilanModal" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header border-bottom">
                        <h5 class="modal-title fw-bold">
                            <i class="bx bx-wallet text-success me-2"></i>Form Pembayaran Cicilan
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('pembelian.payment.store', $pembelian->referensi) }}" method="POST"
                        id="formCicilan">
                        @csrf
                        <div class="modal-body">
                            <div class="row g-2 text-center mb-4">
                                <div class="col-6">
                                    <div class="bg-label-secondary rounded p-2">
                                        <small class="text-muted d-block mb-1">Total Tagihan</small>
                                        <span class="fw-bold text-dark">Rp
                                            {{ number_format($pembelian->total_akhir, 0, ',', '.') }}</span>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="bg-label-danger rounded p-2">
                                        <small class="text-danger d-block mb-1">Sisa Hutang</small>
                                        <span class="fw-bold text-danger">Rp
                                            {{ number_format($pembelian->sisa_hutang, 0, ',', '.') }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="row g-3">
                                <div class="col-6">
                                    <label for="tanggal_bayar" class="form-label fw-semibold">Tanggal Bayar <span
                                            class="text-danger">*</span></label>
                                    <input type="date" name="tanggal_bayar" id="tanggal_bayar" class="form-control"
                                        value="{{ date('Y-m-d') }}" required>
                                </div>
                                <div class="col-6">
                                    <label for="tanggal_jatuh_tempo" class="form-label fw-semibold">Tanggal Jatuh Tempo <span
                                            class="text-danger">*</span></label>
                                    <input type="date" id="tanggal_jatuh_tempo" class="form-control"
                                        value="{{ \Carbon\Carbon::parse($pembelian->tanggal_jatuh_tempo)->format('Y-m-d') }}"
                                        disabled>
                                </div>

                                {{-- Metode Pembayaran (Radio Button bergaya Card) --}}
                                <div class="col-12">
                                    <label class="form-label fw-semibold">Metode Pembayaran <span
                                            class="text-danger">*</span></label>
                                    <div class="row gx-2">
                                        <div class="col-6">
                                            <input type="radio" class="btn-check" name="metode_pembayaran" id="pay-tunai"
                                                value="TUNAI" checked required>
                                            <label class="btn btn-outline-primary d-flex w-100 p-2 align-items-center"
                                                for="pay-tunai">
                                                <i class="bx bx-money fs-4 me-2"></i>
                                                <span class="d-block text-sm">Tunai</span>
                                            </label>
                                        </div>
                                        <div class="col-6">
                                            <input type="radio" class="btn-check" name="metode_pembayaran"
                                                id="pay-transfer" value="TRANSFER">
                                            <label class="btn btn-outline-primary d-flex w-100 p-2 align-items-center"
                                                for="pay-transfer">
                                                <i class="bx bx-transfer fs-4 me-2"></i>
                                                <span class="d-block text-sm">Transfer</span>
                                            </label>
                                        </div>

                                    </div>
                                </div>

                                {{-- Pilihan Bank (Hanya Muncul Jika Transfer) --}}
                                <div class="col-12 d-none animate__animated animate__fadeIn" id="modal-bank-container">
                                    <label for="modal_bank_id" class="form-label fw-semibold">Rekening Tujuan <span
                                            class="text-danger">*</span></label>
                                    <select name="account_id" id="modal_bank_id" class="form-select select2-bank-modal">
                                        <option value="" disabled selected>Pilih Rekening Bank...</option>
                                        @foreach ($accounts as $bank)
                                            <option value="{{ $bank->id }}" data-logo="{{ $bank->logo_url }}">
                                                {{ $bank->account_name }} - {{ $bank->nomor_rekening }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label for="jumlah_bayar_input" class="form-label fw-semibold">Nominal Jumlah Bayar <span
                                            class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text fw-bold">Rp</span>
                                        <input type="text" id="jumlah_bayar_input"
                                            class="form-control text-end fw-bold fs-5" placeholder="0" required
                                            autocomplete="off">
                                        <button type="button" class="btn btn-primary" id="btn-set-lunas"
                                            title="Bayar Semua Sisa Hutang">Uang Pas</button>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <label for="referensi_pembayaran" class="form-label fw-semibold">Referensi Pembayaran
                                        (Opsional)</label>
                                    <input type="text" name="referensi_pembayaran" id="referensi_pembayaran"
                                        class="form-control" placeholder="Nomor struk, id transaksi transfer, dsb.">
                                </div>

                                <div class="col-12">
                                    <label for="modal_catatan" class="form-label fw-semibold">Catatan Tambahan
                                        (Opsional)</label>
                                    <textarea name="catatan" id="modal_catatan" class="form-control" rows="2"
                                        placeholder="Tulis keterangan cicilan di sini..."></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer border-top pt-3">
                            <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Tutup</button>
                            <button type="submit" class="btn btn-success px-4" id="btnSaveCicilan">
                                <i class="bx bx-check-circle me-1"></i> Simpan Pembayaran
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endcan

@endsection
@section('page-script')
    <script type="module">
        function waitForJQuery(callback) {
            if (window.$ && window.$.fn && window.$.fn.select2) {
                callback();
            } else {
                setTimeout(function() {
                    waitForJQuery(callback);
                }, 100);
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            waitForJQuery(function() {

                const formatBankLogo = (state) => {
                    if (!state.id) {
                        return state.text;
                    }
                    const logoUrl = $(state.element).data('logo');
                    if (!logoUrl) {
                        return state.text;
                    }
                    return $(
                        '<span class="d-flex align-items-center">' +
                        '<img src="' + logoUrl +
                        '" style="width: 24px; height: 24px; object-fit: contain; margin-right: 8px;" alt="logo" />' +
                        '<span class="fw-bold">' + state.text + '</span>' +
                        '</span>'
                    );
                };
                // Inisialisasi Select2 di dalam modal
                $('.select2-payment').select2({
                    dropdownParent: $('#addCicilanModal'),
                    width: '100%'
                });

                $('.select2-bank-modal').select2({
                    dropdownParent: $('#addCicilanModal'), // Wajib ada karena di dalam modal
                    placeholder: "Pilih Rekening Bank...",
                    allowClear: true,
                    width: '100%',
                    templateResult: formatBankLogo, // Render saat list dibuka
                    templateSelection: formatBankLogo // Render saat list dipilih
                });

                // Ambil nilai sisa hutang mentah dari PHP untuk validasi client-side
                const sisaHutangMaksimal = {{ $pembelian->sisa_hutang }};

                // Utilitas Format Mata Uang
                const parseCurrency = (string) => parseFloat(String(string).replace(/[^0-9]/g, '')) || 0;

                function formatNumber(num) {
                    return new Intl.NumberFormat('id-ID').format(num);
                }

                // Tampilkan input bank JIKA metode TRANSFER dipilih (Dari Radio Button)
                $('input[name="metode_pembayaran"]').on('change', function() {
                    if ($(this).val() === 'TRANSFER') {
                        $('#modal-bank-container').removeClass('d-none');
                        $('#modal_bank_id').prop('required', true); // Jadikan wajib diisi
                    } else {
                        $('#modal-bank-container').addClass('d-none');
                        $('#modal_bank_id').prop('required', false).val('').trigger(
                            'change'); // Kosongkan & hapus wajib
                    }
                });

                // Format mata uang real-time saat user mengetik nominal cicilan
                $('#jumlah_bayar_input').on('input', function() {
                    let val = parseCurrency($(this).val());

                    // Batasi agar input tidak melebihi sisa hutang secara visual
                    if (val > sisaHutangMaksimal) {
                        val = sisaHutangMaksimal;
                    }

                    $(this).val(formatNumber(val));
                });

                // Tombol Uang Pas langsung isi penuh sisa hutang
                $('#btn-set-lunas').on('click', function() {
                    $('#jumlah_bayar_input').val(formatNumber(sisaHutangMaksimal));
                });

                // Validasi akhir sebelum submit form cicilan
                $('#formCicilan').on('submit', function(e) {
                    const inputRaw = parseCurrency($('#jumlah_bayar_input').val());

                    if (inputRaw <= 0) {
                        e.preventDefault();
                        alert('Nominal jumlah pembayaran cicilan harus lebih dari Rp 0.');
                        return;
                    }

                    if (inputRaw > sisaHutangMaksimal) {
                        e.preventDefault();
                        alert('Nominal pembayaran tidak boleh melebihi sisa hutang.');
                        return;
                    }

                    // Tempelkan nilai bersih tanpa titik ke input form sebelum dikirim ke backend
                    if (!document.querySelector('input[name="jumlah_bayar"]')) {
                        $(this).append(
                            `<input type="hidden" name="jumlah_bayar" value="${inputRaw}">`);
                    } else {
                        $('input[name="jumlah_bayar"]').val(inputRaw);
                    }

                    $('#btnSaveCicilan').prop('disabled', true).html(
                        '<i class="bx bx-loader bx-spin me-1"></i> Memproses...');
                });
            });
        });
    </script>
@endsection
