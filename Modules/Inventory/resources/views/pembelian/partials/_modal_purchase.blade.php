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
                                value="TUNAI" form="formPurchase" checked required>
                            <label class="btn btn-outline-primary w-100 p-2" for="pay-tunai">
                                <i class="bx bx-money fs-4 d-block me-2"></i> Tunai
                            </label>
                        </div>
                        <div class="col-4">
                            <input type="radio" class="btn-check" name="metode_pembayaran" id="pay-transfer"
                                value="TRANSFER" form="formPurchase">
                            <label class="btn btn-outline-primary w-100 p-2" for="pay-transfer">
                                <i class="bx bx-transfer fs-4 d-block me-2"></i> Transfer
                            </label>
                        </div>
                        <div class="col-4">
                            <input type="radio" class="btn-check" name="metode_pembayaran" id="pay-qris"
                                value="QRIS" form="formPurchase">
                            <label class="btn btn-outline-primary w-100 p-2" for="pay-qris">
                                <i class="bx bx-qr-scan fs-4 d-block me-2"></i> QRIS
                            </label>
                        </div>

                        <div id="transfer-details" class="d-none mt-3 animate__animated animate__fadeIn">
                            <label for="bank_tujuan" class="form-label fw-semibold">Rekening Tujuan <span
                                    class="text-danger">*</span></label>
                            <select name="bank_tujuan" id="bank_tujuan" class="form-select select2 select2-bank"
                                data-placeholder="Pilih Rekening Bank" form="formPurchase">
                                <option value=""></option>
                                @foreach ($banks as $bank)
                                    <option value="{{ $bank->id }}" data-logo="{{ $bank->logo_bank }}">
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
                            class="form-control text-end fw-bold" inputmode="numeric" form="formPurchase" required>
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
                        <button class="btn bg-label-secondary w-100 quick-pay-btn" type="button" data-amount="50000">50
                            Rb</button>
                    </div>
                    <div class="col-4">
                        <button class="btn bg-label-secondary w-100 quick-pay-btn" type="button"
                            data-amount="100000">100 Rb</button>
                    </div>
                </div>

                {{-- Kembalian & Catatan --}}
                <div class="row mb-3">
                    <div class="col-12 mb-3">
                        <div class="d-flex justify-content-between align-items-center p-3 border rounded bg-light">
                            <span class="fw-semibold text-muted">Kembalian</span>
                            <h4 class="mb-0 fw-bold change-value" id="change-display">Rp 0</h4>
                        </div>
                    </div>
                    <div class="col-12">
                        <label for="catatan" class="form-label fw-semibold">Catatan</label>
                        <div class="border rounded-3 bg-white">
                            <div id="quill-editor-catatan" style="min-height: 80px; border: none;">
                                {!! old('catatan') !!}
                            </div>
                        </div>
                        <input type="hidden" name="catatan" id="catatan" value="{{ old('catatan') }}">
                        @error('catatan')
                            <div class="invalid-feedback d-block text-sm">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="d-flex flex-column flex-sm-row justify-content-end gap-2 my-4 pt-3 border-top">
                    <button type="button" class="btn btn-outline-danger w-100 w-sm-auto order-2 order-sm-1"
                        data-bs-dismiss="modal">Batal</button>
                    <button type="submit"
                        class="btn btn-success w-100 w-sm-auto order-1 order-sm-2 px-4 btn-save-transaction"
                        id="saveBtn" form="formPurchase">
                        <i class="bx bx-check-circle me-1"></i> Transaksi
                    </button>
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
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
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

{{-- Modal Create Supplier --}}
<div class="modal fade" id="createSupplierModal" tabindex="-1" aria-labelledby="createSupplierModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0 mb-n3">
                <h6 class="modal-title" id="createSupplierModalLabel">Tambah Supplier Baru</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                <form id="createSupplierForm" action="{{ route('pemasok.store') }}" method="post">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="name" class="form-label">Nama</label>
                            <input id="name" name="name" type="text"
                                class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}"
                                required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="perusahaan" class="form-label">Perusahaan</label>
                            <input id="perusahaan" name="perusahaan" type="text"
                                class="form-control @error('perusahaan') is-invalid @enderror"
                                value="{{ old('perusahaan') }}" required>
                            @error('perusahaan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="kontak" class="form-label">Kontak</label>
                            <input id="kontak" name="kontak" type="text"
                                class="form-control @error('kontak') is-invalid @enderror"
                                value="{{ old('kontak') }}" required>
                            @error('kontak')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror"
                                id="email" name="email" placeholder="example@gmail.com"
                                value="{{ old('email') }}">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-12">
                            <label for="alamat" class="form-label">Alamat</label>
                            <textarea id="alamat" name="alamat" class="form-control @error('alamat') is-invalid @enderror" rows="2">{{ old('alamat') }}</textarea>
                            @error('alamat')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-12">
                            <label for="note" class="form-label">Catatan (Opsional)</label>
                            <textarea id="note" name="note" class="form-control @error('note') is-invalid @enderror" rows="2">{{ old('note') }}</textarea>
                            @error('note')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="justify-content-end form-check form-switch form-check-reverse my-2">
                        <label class="me-auto fw-bold form-check-label" for="status">Status</label>
                        <input id="status" class="form-check-input" type="checkbox" name="status"
                            value="1" checked>
                    </div>
                    <div class="modal-footer border-0 pb-0">
                        <button type="submit" class="btn btn-info btn-sm">Buat Supplier</button>
                        <button type="button" class="btn btn-danger btn-sm"
                            data-bs-dismiss="modal">Batalkan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Modal Edit Item --}}
<div class="modal fade" id="editItemModal" tabindex="-1" aria-labelledby="editItemModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editItemModalLabel">Edit Item</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                <form id="editItemForm" onsubmit="return false;">
                    <input type="hidden" id="edit-item-id">
                    <div class="row g-3 px-1">
                        <div class="col-12">
                            <label class="form-label">Nama Product</label>
                            <input type="text" class="form-control" id="edit-item-name" readonly disabled>
                        </div>
                        <div class="col-md-6 col-12 form-group">
                            <label for="edit-item-qty" class="form-control-label">Qty <span
                                    class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="edit-item-qty" min="1" required>
                        </div>
                        <div class="col-md-6 col-12 form-group">
                            <label for="edit-item-harga" class="form-control-label">Harga Beli <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="edit-item-harga" placeholder="0">
                        </div>
                        <div class="col-6">
                            <label for="edit-item-pajak-id" class="form-label">Taxe</label>
                            <select class="form-select select2" id="edit-item-pajak-id">
                                <option value="" data-rate="0" selected>Tidak ada</option>
                                @foreach ($taxes as $pajak)
                                    <option value="{{ $pajak->id }}" data-rate="{{ $pajak->rate }}">
                                        {{ $pajak->name_taxe }} ({{ $pajak->rate }}%)</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-6">
                            <label for="edit-item-diskon" class="form-label">Diskon (Rp)</label>
                            <input type="text" class="form-control" id="edit-item-diskon" placeholder="0">
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
