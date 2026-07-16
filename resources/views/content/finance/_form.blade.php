{{-- resources/views/content/finance/_form.blade.php --}}
{{-- Di-load via fetch() ke dalam #cashFlowOffcanvas .offcanvas-body, dipakai untuk create & edit --}}

@php
    $isEdit = $cashFlow->exists;
    $action = $isEdit
        ? route('financial.cash-flows.update', ['type' => $type, 'cash_flow' => $cashFlow->referensi])
        : route('financial.cash-flows.store', ['type' => $type]);
    $isTransfer = $type === \App\Models\CashFlow::TYPE_TRANSFER;
@endphp

<form id="cashFlowForm" data-type="{{ $type }}" data-method="{{ $isEdit ? 'PUT' : 'POST' }}"
    action="{{ $action }}" enctype="multipart/form-data">
    @csrf
    @if ($isEdit)
        @method('PUT')
    @endif

    {{-- ── Tanggal ──────────────────────────────────────────── --}}
    <div class="mb-3">
        <label class="form-label fw-medium">Tanggal <span class="text-danger">*</span></label>
        <input type="date" name="tanggal" class="form-control"
            value="{{ old('tanggal', $isEdit ? $cashFlow->tanggal->format('Y-m-d') : now()->format('Y-m-d')) }}"
            max="{{ now()->format('Y-m-d') }}" required>
    </div>

    {{-- ── Nominal ──────────────────────────────────────────── --}}
    <div class="mb-3">
        <label class="form-label fw-medium">Nominal <span class="text-danger">*</span></label>
        <div class="input-group">
            <span class="input-group-text fw-semibold text-muted">Rp</span>
            <input type="text" id="nominalDisplay" class="form-control" placeholder="0" autocomplete="off"
                value="{{ old('nominal', $cashFlow->nominal) ? number_format((float) old('nominal', $cashFlow->nominal), 0, ',', '.') : '' }}">
            <input type="hidden" name="nominal" id="nominalHidden" value="{{ old('nominal', $cashFlow->nominal) }}">
        </div>
        <small class="text-muted" id="nominalReadable"></small>
    </div>

    {{-- ── Kategori (bukan transfer) ───────────────────────── --}}
    @unless ($isTransfer)
        <div class="mb-3">
            <label class="form-label fw-medium">Kategori <span class="text-danger">*</span></label>
            <select name="transaction_category_id" class="form-select" required>
                <option value="">— Pilih Kategori —</option>
                @foreach ($kategoris as $kategori)
                    <option value="{{ $kategori->id }}" @selected(old('transaction_category_id', $cashFlow->transaction_category_id) == $kategori->id)>
                        {{ $kategori->name }}
                    </option>
                @endforeach
            </select>
        </div>
    @endunless

    {{-- ── Metode Pembayaran & Bank ────────────────────────── --}}
    @if ($isTransfer)
        <div class="row g-2 mb-3">
            {{-- Hidden metode supaya lolos validasi BE yang mengharuskan metode = TRANSFER --}}
            <input type="hidden" name="metode_pembayaran" value="TRANSFER">
            <input type="hidden" name="metode_pembayaran_tujuan" value="TRANSFER">

            <div class="col-6">
                <label class="form-label fw-medium">Dari (Rekening Asal) <span class="text-danger">*</span></label>
                <select name="bank_id" class="form-select select2" required>
                    <option value="">— Pilih Rekening —</option>
                    @foreach ($banks as $bank)
                        <option value="{{ $bank->id }}" @selected(old('bank_id', $cashFlow->bank_id) == $bank->id)>
                            {{ $bank->nama_bank ?? $bank->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-6">
                <label class="form-label fw-medium">Ke (Rekening Tujuan) <span class="text-danger">*</span></label>
                <select name="bank_id_tujuan" class="form-select select2" required>
                    <option value="">— Pilih Rekening —</option>
                    @foreach ($banks as $bank)
                        <option value="{{ $bank->id }}" @selected(old('bank_id_tujuan', $cashFlow->bank_id_tujuan) == $bank->id)>
                            {{ $bank->nama_bank ?? $bank->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
    @else
        <div class="row g-2 mb-3">
            <div class="col-6">
                <label class="form-label fw-medium">Metode Pembayaran <span class="text-danger">*</span></label>
                <select name="metode_pembayaran" class="form-select select2 js-metode" data-target="#bank_id_wrap">
                    @foreach ($cashFlow->getPaymentMethods() as $metode)
                        <option value="{{ $metode }}" @selected(old('metode_pembayaran', $cashFlow->metode_pembayaran ?? 'TUNAI') === $metode)>
                            {{ $metode }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-6" id="bank_id_wrap">
                <label class="form-label fw-medium">Bank</label>
                <select name="bank_id" class="form-select select2">
                    <option value="">— Pilih Bank —</option>
                    @foreach ($banks as $bank)
                        <option value="{{ $bank->id }}" @selected(old('bank_id', $cashFlow->bank_id) == $bank->id)>
                            {{ $bank->nama_bank ?? $bank->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
    @endif

    {{-- ── Referensi ────────────────────────────────────────── --}}
    <div class="mb-3">
        <label class="form-label fw-medium">Nomor Referensi</label>
        <div class="input-group">
            <span class="input-group-text text-muted"><i class="bx bx-hash"></i></span>
            <input type="text" name="referensi" class="form-control"
                value="{{ old('referensi', $isEdit ? $cashFlow->referensi : $referensi_otomatis) }}"
                {{ $isEdit ? '' : 'readonly' }}>
        </div>
        @unless ($isEdit)
            <small class="text-muted">Dibuat otomatis, tidak perlu diubah.</small>
        @endunless
    </div>

    {{-- ── Keterangan ───────────────────────────────────────── --}}
    <div class="mb-3">
        <label class="form-label fw-medium">Keterangan <span class="text-danger">*</span></label>
        <input type="text" name="keterangan" class="form-control"
            value="{{ old('keterangan', $cashFlow->keterangan) }}"
            placeholder="{{ $isTransfer ? 'Transfer Internal (opsional)' : 'Tulis keterangan transaksi...' }}"
            {{ $isTransfer ? '' : 'required' }}>
    </div>

    {{-- ── Catatan ───────────────────────────────────────────── --}}
    <div class="mb-3">
        <label class="form-label fw-medium">Catatan <small class="text-muted fw-normal">(opsional)</small></label>
        <textarea name="description" class="form-control" rows="2" placeholder="Catatan tambahan...">{{ old('description', $cashFlow->description) }}</textarea>
    </div>

    {{-- ── Bukti Upload ──────────────────────────────────────── --}}
    <div class="mb-4">
        <label class="form-label fw-medium">Bukti Transaksi <small
                class="text-muted fw-normal">(opsional)</small></label>
        <div class="cf-upload-area" id="cfUploadArea">
            <i class="bx bx-cloud-upload"></i>
            <p class="mb-0">Klik atau seret foto bukti ke sini</p>
            <small class="text-muted">JPG, PNG, max 2 MB</small>
            <input type="file" name="bukti" id="buktiFoto" accept="image/*" class="cf-upload-input">
        </div>
        <div id="buktiFotoPreview" class="mt-2 {{ $isEdit && $cashFlow->bukti ? '' : 'd-none' }}">
            @if ($isEdit && $cashFlow->bukti)
                <div class="d-flex align-items-center gap-2 p-2 border rounded">
                    <img src="{{ Storage::disk('r2')->url($cashFlow->bukti) }}" class="cf-bukti-thumb"
                        alt="Bukti saat ini">
                    <small class="text-muted">File saat ini — upload baru untuk mengganti.</small>
                </div>
            @endif
        </div>
    </div>

    {{-- ── Error Box ─────────────────────────────────────────── --}}
    <div id="cashFlowFormErrors" class="mb-3"></div>

    {{-- ── Submit ────────────────────────────────────────────── --}}
    <button type="submit" class="btn btn-primary w-100 fw-semibold">
        <i class="bx {{ $isEdit ? 'bx-save' : 'bx-plus-circle' }} me-1"></i>
        {{ $isEdit ? 'Simpan Perubahan' : 'Simpan Transaksi' }}
    </button>
</form>

<script>
    (function() {
        // ── Toggle bank field berdasarkan metode ────────────────
        document.querySelectorAll('#cashFlowForm .js-metode').forEach(function(select) {
            const target = document.querySelector(select.dataset.target);

            function toggle() {
                if (!target) return;
                target.style.transition = 'opacity .2s';
                if (select.value === 'TRANSFER') {
                    target.style.opacity = '1';
                    target.style.pointerEvents = '';
                    target.querySelector('select').required = false; // bank_id boleh kosong, validasi di BE
                } else {
                    target.style.opacity = '0.4';
                    target.style.pointerEvents = 'none';
                }
            }

            select.addEventListener('change', toggle);
            toggle();
        });

        // ── Format nominal (Rupiah display) ────────────────────
        const nominalDisplay = document.getElementById('nominalDisplay');
        const nominalHidden = document.getElementById('nominalHidden');
        const nominalReadable = document.getElementById('nominalReadable');

        function terbilangSimple(n) {
            if (n >= 1_000_000_000) return (n / 1_000_000_000).toFixed(1).replace('.0', '') + ' Miliar';
            if (n >= 1_000_000) return (n / 1_000_000).toFixed(1).replace('.0', '') + ' Juta';
            if (n >= 1_000) return (n / 1_000).toFixed(0) + ' Ribu';
            return '';
        }

        function formatRupiah(val) {
            const num = parseInt(val.replace(/\D/g, ''), 10) || 0;
            nominalHidden.value = num || '';
            nominalDisplay.value = num ? num.toLocaleString('id-ID') : '';
            nominalReadable.textContent = num >= 1000 ? '≈ ' + terbilangSimple(num) + ' Rupiah' : '';
        }

        if (nominalDisplay) {
            nominalDisplay.addEventListener('input', function() {
                formatRupiah(this.value);
            });
            // Inisialisasi format saat form dibuka (mode edit)
            if (nominalHidden.value) {
                nominalDisplay.value = parseInt(nominalHidden.value, 10).toLocaleString('id-ID');
                nominalReadable.textContent = parseInt(nominalHidden.value, 10) >= 1000 ?
                    '≈ ' + terbilangSimple(parseInt(nominalHidden.value, 10)) + ' Rupiah' : '';
            }
        }

        // ── Preview foto bukti ─────────────────────────────────
        const fileInput = document.getElementById('buktiFoto');
        const previewArea = document.getElementById('buktiFotoPreview');
        const uploadArea = document.getElementById('cfUploadArea');

        if (fileInput) {
            fileInput.addEventListener('change', function() {
                const file = this.files[0];
                if (!file) return;

                const reader = new FileReader();
                reader.onload = function(e) {
                    previewArea.classList.remove('d-none');
                    previewArea.innerHTML = `
                    <div class="d-flex align-items-center gap-2 p-2 border rounded">
                        <img src="${e.target.result}" class="cf-bukti-thumb" alt="Preview">
                        <div>
                            <div class="fw-medium small">${file.name}</div>
                            <small class="text-muted">${(file.size / 1024).toFixed(0)} KB</small>
                        </div>
                        <button type="button" class="btn btn-sm btn-link text-danger ms-auto p-0" id="cfClearBukti">
                            <i class="bx bx-x fs-5"></i>
                        </button>
                    </div>`;

                    document.getElementById('cfClearBukti')?.addEventListener('click', function() {
                        fileInput.value = '';
                        previewArea.classList.add('d-none');
                        previewArea.innerHTML = '';
                    });
                };
                reader.readAsDataURL(file);
            });

            // Drag & drop
            uploadArea?.addEventListener('dragover', e => {
                e.preventDefault();
                uploadArea.classList.add('dragover');
            });
            uploadArea?.addEventListener('dragleave', () => uploadArea.classList.remove('dragover'));
            uploadArea?.addEventListener('drop', e => {
                e.preventDefault();
                uploadArea.classList.remove('dragover');
                if (e.dataTransfer.files[0]) {
                    fileInput.files = e.dataTransfer.files;
                    fileInput.dispatchEvent(new Event('change'));
                }
            });
        }
    })();
</script>
