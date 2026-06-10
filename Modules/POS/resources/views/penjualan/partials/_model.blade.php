{{-- Modal: Buat Customer Baru --}}
<div class="modal fade" id="createCustomerModal" tabindex="-1" aria-labelledby="createCustomerModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0 mb-n3">
                <h6 class="modal-title" id="createCustomerModalLabel">Buat Customer Baru</h6>
                <button type="button" class="btn btn-close bg-danger rounded-3 me-1" data-bs-dismiss="modal"
                    aria-label="Tutup modal"></button>
            </div>
            <div class="modal-body">
                <form id="createCustomerForm" action="{{ route('pelanggan.store') }}" method="post" novalidate>
                    @csrf
                    <div class="mb-2">
                        <label for="cust-name" class="form-label">
                            Nama <span class="text-danger">*</span>
                        </label>
                        <input id="cust-name" name="name" type="text" class="form-control" required
                            autocomplete="name">
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="mb-1">
                        <label for="cust-kontak" class="form-label">
                            Kontak <span class="text-danger">*</span>
                        </label>
                        <input id="cust-kontak" name="kontak" type="text" class="form-control" required
                            autocomplete="tel">
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="mb-1">
                        <label for="cust-email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="cust-email" name="email"
                            placeholder="example@gmail.com" autocomplete="email">
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="mb-1">
                        <label for="cust-alamat" class="form-label">Alamat</label>
                        <textarea id="cust-alamat" name="alamat" class="form-control" rows="3" autocomplete="street-address"></textarea>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="justify-content-end form-check form-switch form-check-reverse mb-2">
                        <label class="me-auto fw-bold form-check-label" for="cust-status">
                            Status Aktif
                        </label>
                        <input id="cust-status" class="form-check-input" type="checkbox" name="status" value="1"
                            checked>
                    </div>
                    <div class="modal-footer border-0 pb-0">
                        <button type="submit" class="btn btn-outline-info btn-sm p-2" id="btn-save-customer">Tambah
                            Customer</button>
                        <button type="button" class="btn btn-danger btn-sm p-2"
                            data-bs-dismiss="modal">Batalkan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Modal: Edit Item Keranjang --}}
<div class="modal fade" id="editCartItemModal" tabindex="-1" aria-labelledby="editCartItemModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editCartItemModalLabel">Edit Item</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                <form id="editCartItemForm" novalidate>
                    <input type="hidden" id="edit-item-id">
                    <div class="row g-3 px-1">
                        <div class="col-12">
                            <label class="form-label">Nama Produk</label>
                            <input type="text" class="form-control" id="edit-item-name" readonly disabled
                                aria-readonly="true">
                        </div>
                        <div class="col-12">
                            <label for="edit-item-harga" class="form-label">
                                Harga Jual <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="text" class="form-control" id="edit-item-harga" placeholder="0"
                                    inputmode="numeric" min="1">
                            </div>
                        </div>
                        <div class="col-6">
                            <label for="edit-item-diskon" class="form-label">Diskon (Rp)</label>
                            <input type="text" class="form-control" id="edit-item-diskon" placeholder="0"
                                inputmode="numeric">
                        </div>
                        <div class="col-6">
                            <label for="edit-item-pajak-id" class="form-label">Pajak</label>
                            <select class="form-select" id="edit-item-pajak-id">
                                <option value="" data-rate="0" selected>Tidak Ada</option>
                                @foreach ($taxes as $pajak)
                                    <option value="{{ $pajak->id }}" data-rate="{{ $pajak->rate }}">
                                        {{ $pajak->name_taxe }} ({{ $pajak->rate }}%)
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-info" id="saveItemChangesBtn">Simpan Perubahan</button>
                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Batal</button>
            </div>
        </div>
    </div>
</div>

{{-- Modal: Riwayat Penjualan --}}
<div class="modal fade" id="salesHistoryModal" tabindex="-1" aria-labelledby="salesHistoryModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="salesHistoryModalLabel">
                    <i class="bx bx-history me-2" aria-hidden="true"></i>
                    Riwayat Penjualan Hari Ini
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body" id="salesHistoryBody">
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Memuat data...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal: Edit Biaya Tambahan --}}
<div class="modal fade" id="editExtraCostModal" tabindex="-1" aria-labelledby="editExtraCostModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="editExtraCostModalLabel">Edit Biaya</h6>
                <button type="button" class="btn bg-dark btn-close" data-bs-dismiss="modal"
                    aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                <form id="editExtraCostForm" novalidate>
                    <input type="hidden" id="extra-cost-type">
                    <div id="promo-code-section" class="mb-3" style="display:none;" aria-hidden="true">
                        <label for="promo-code-input" class="form-label">Kode Promosi</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="promo-code-input"
                                placeholder="Masukkan kode promo" autocomplete="off">
                            <button class="btn btn-outline-secondary mb-0" type="button"
                                id="apply-promo-btn">Terapkan</button>
                        </div>
                        <div id="promo-feedback" class="mt-2 text-xs" aria-live="polite"></div>
                    </div>
                    <div class="mb-0">
                        <label for="extra-cost-value" class="form-label" id="extra-cost-label">Jumlah</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="text" class="form-control" id="extra-cost-value" min="0"
                                inputmode="numeric">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-info" id="saveExtraCostBtn">Simpan</button>
            </div>
        </div>
    </div>
</div>

{{-- Modal: Pilih Nomor Seri --}}
<div class="modal fade" id="serialNumberModal" tabindex="-1" aria-labelledby="serialNumberModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="serialNumberModalLabel">Pilih Nomor Seri</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="sn-produk-id">
                <div class="badge bg-label-info p-2 text-sm w-100 text-start mb-2" role="status">
                    Produk: <strong id="sn-name-produk" class="text-warning">—</strong><br>
                    Pilih tepat <strong id="sn-required-count">1</strong> nomor seri.
                </div>
                <div id="sn-list-container" class="list-group" style="max-height: 300px; overflow-y: auto;"
                    role="group" aria-label="Daftar nomor seri">
                    <div class="text-center py-3">
                        <div class="spinner-border spinner-border-sm" role="status">
                            <span class="visually-hidden">Memuat nomor seri...</span>
                        </div>
                    </div>
                </div>
                <div class="invalid-feedback d-block mt-2" id="sn-error-message" aria-live="assertive"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-info" id="btn-confirm-sn">Simpan Pilihan</button>
            </div>
        </div>
    </div>
</div>

{{-- Modal: Pembayaran Checkout --}}
<div class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="paymentModalLabel" aria-hidden="true"
    data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content">

            {{-- Header --}}
            <div class="modal-header border-bottom pb-3">
                <h5 class="modal-title fw-bold" id="paymentModalLabel">
                    <i class="bx bx-wallet text-primary me-2"></i>Payment
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>

            <div class="modal-body pb-0">
                {{-- Total Tagihan Highlight --}}
                <div class="text-center bg-label-primary rounded p-3 mb-4">
                    <p class="text-sm mb-1 text-primary fw-semibold">Total Tagihan</p>
                    <h2 class="fw-bolder text-primary mb-0" id="payment-modal-total">Rp 0</h2>
                </div>

                {{-- Metode Pembayaran (Radio Button bergaya Card) --}}
                <div class="mb-4">
                    <label class="form-label fw-semibold">Metode Pembayaran</label>
                    <div class="row gx-2">
                        <div class="col-4">
                            <input type="radio" class="btn-check" name="metode_pembayaran" id="pay-tunai"
                                value="TUNAI" form="penjualanForm" checked required>
                            <label class="btn btn-outline-primary w-100 p-2" for="pay-tunai">
                                <i class="bx bx-money fs-4 d-block me-2"></i> Tunai
                            </label>
                        </div>
                        <div class="col-4">
                            <input type="radio" class="btn-check" name="metode_pembayaran" id="pay-transfer"
                                value="TRANSFER" form="penjualanForm">
                            <label class="btn btn-outline-primary w-100 p-2" for="pay-transfer">
                                <i class="bx bx-transfer fs-4 d-block me-2"></i> Transfer
                            </label>
                        </div>
                        <div class="col-4">
                            <input type="radio" class="btn-check" name="metode_pembayaran" id="pay-qris"
                                value="QRIS" form="penjualanForm">
                            <label class="btn btn-outline-primary w-100 p-2" for="pay-qris">
                                <i class="bx bx-qr-scan fs-4 d-block me-2"></i> QRIS
                            </label>
                        </div>

                        <div id="transfer-details" class="d-none mt-3 animate__animated animate__fadeIn">
                            <label for="bank_id" class="form-label fw-semibold">Rekening Tujuan <span
                                    class="text-danger">*</span></label>
                            <select name="bank_id" id="bank_id" class="form-select select2 select2-bank"
                                data-placeholder="Pilih Rekening Bank" form="penjualanForm">
                                <option value=""></option>
                                @foreach ($banks as $bank)
                                    <option value="{{ $bank->id }}" data-logo="{{ $bank->logo_url }}">
                                        {{ $bank->nama_bank }} - {{ $bank->nomor_rekening }}
                                    </option>
                                @endforeach
                            </select>
                            </select>
                        </div>
                    </div>
                </div>

                {{-- Input Jumlah Bayar --}}
                <div class="mb-3">
                    <label for="jumlah-dibayar-input" class="form-label fw-semibold">Jumlah Uang Diterima</label>
                    <div class="input-group input-group-merge">
                        <span class="input-group-text text-muted fw-bold">Rp</span>
                        <input type="text" name="jumlah_dibayar" id="jumlah-dibayar-input"
                            class="form-control text-end fw-bold" inputmode="numeric" form="penjualanForm" required>
                    </div>
                </div>

                {{-- Tombol Uang Pas / Quick Pay --}}
                <div class="row gx-2 mb-4">
                    <div class="col-4">
                        <button class="btn bg-label-secondary w-100 btn-pay-exact" id="btn-pay-exact"
                            type="button">Uang
                            Pas</button>
                    </div>
                    <div class="col-4">
                        <button class="btn bg-label-secondary w-100 quick-pay-btn" type="button"
                            data-amount="50000">50
                            Rb</button>
                    </div>
                    <div class="col-4">
                        <button class="btn bg-label-secondary w-100 quick-pay-btn" type="button"
                            data-amount="100000">100 Rb</button>
                    </div>
                </div>


                {{-- Kembalian & Catatan --}}
                <div class="row g-2 mb-3">
                    <div class="col-12">
                        <div class="d-flex justify-content-between align-items-center p-3 border rounded bg-light">
                            <span class="fw-semibold text-muted">Kembalian</span>
                            <h4 class="mb-0 fw-bold change-value" id="change-display">Rp 0</h4>
                        </div>
                    </div>
                    {{-- Bungkus dengan div id="jatuh-tempo-wrapper" --}}
                    <div class="col-12" id="jatuh-tempo-wrapper">
                        <label for="tanggal_jatuh_tempo" class="form-label fw-semibold">Tanggal Jatuh Tempo</label>
                        <input id="tanggal_jatuh_tempo" name="tanggal_jatuh_tempo" type="date"
                            class="form-control @error('tanggal_jatuh_tempo') is-invalid @enderror"
                            value="{{ old('tanggal_jatuh_tempo') }}" form="penjualanForm">
                        @error('tanggal_jatuh_tempo')
                            <div class="invalid-feedback text-sm">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-12">
                        <label for="catatan" class="form-label fw-semibold">Catatan <span
                                class="text-muted fw-normal">(Opsional)</span></label>
                        <textarea name="catatan" id="catatan" class="form-control" rows="2"
                            placeholder="Tambahkan catatan untuk transaksi ini..." form="penjualanForm"></textarea>
                    </div>
                </div>
                <div class="d-flex flex-column flex-sm-row justify-content-end gap-2 my-4 pt-3 border-top">
                    <button type="button" class="btn btn-outline-danger w-100 w-sm-auto order-2 order-sm-1"
                        data-bs-dismiss="modal">Batal</button>
                    <button type="submit"
                        class="btn btn-blue w-100 w-sm-auto order-1 order-sm-2 px-4 btn-save-transaction"
                        id="btn-save-transaction" form="penjualanForm">
                        <i class="bx bx-check-circle me-1"></i> Transaksi
                    </button>
                </div>
            </div>

        </div>
    </div>
</div>
