{{-- resources/views/content/finance/cash-flow-form.blade.php --}}
{{-- Di-load via fetch() ke dalam #cashFlowOffcanvas .offcanvas-body, dipakai untuk create & edit --}}

@php
    $isEdit = $cashFlow->exists;
    $action = $isEdit
        ? route('financial.cash-flows.update', ['type' => $type, 'cash_flow' => $cashFlow->referensi])
        : route('financial.cash-flows.store', ['type' => $type]);
@endphp

<form id="cashFlowForm" data-type="{{ $type }}" data-method="{{ $isEdit ? 'PUT' : 'POST' }}"
    action="{{ $action }}" enctype="multipart/form-data">
    @csrf
    @if ($isEdit)
        @method('PUT')
    @endif

    <div class="mb-3">
        <label class="form-label">Tanggal</label>
        <input type="date" name="tanggal" class="form-control"
            value="{{ old('tanggal', $isEdit ? $cashFlow->tanggal->format('Y-m-d') : now()->format('Y-m-d')) }}"
            required>
    </div>

    <div class="mb-3">
        <label class="form-label">Nominal</label>
        <input type="number" name="nominal" class="form-control" min="1" step="1"
            value="{{ old('nominal', $cashFlow->nominal) }}" required>
    </div>

    @unless ($type === \App\Models\CashFlow::TYPE_TRANSFER)
        <div class="mb-3">
            <label class="form-label">Kategori</label>
            <select name="transaction_category_id" class="form-select" required>
                <option value="">-- Pilih Kategori --</option>
                @foreach ($kategoris as $kategori)
                    <option value="{{ $kategori->id }}" @selected(old('transaction_category_id', $cashFlow->transaction_category_id) == $kategori->id)>
                        {{ $kategori->name }}
                    </option>
                @endforeach
            </select>
        </div>
    @endunless

    <div class="row">
        <div class="col-6 mb-3">
            <label
                class="form-label">{{ $type === \App\Models\CashFlow::TYPE_TRANSFER ? 'Dari (Metode)' : 'Metode Pembayaran' }}</label>
            <select name="metode_pembayaran" class="form-select js-metode" data-target="#bank_id_wrap">
                @foreach ($cashFlow->getPaymentMethods() as $metode)
                    <option value="{{ $metode }}" @selected(old('metode_pembayaran', $cashFlow->metode_pembayaran ?? 'TUNAI') === $metode)>{{ $metode }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-6 mb-3" id="bank_id_wrap">
            <label
                class="form-label">{{ $type === \App\Models\CashFlow::TYPE_TRANSFER ? 'Rekening Asal' : 'Bank' }}</label>
            <select name="bank_id" class="form-select">
                <option value="">-- Pilih Bank --</option>
                @foreach ($banks as $bank)
                    <option value="{{ $bank->id }}" @selected(old('bank_id', $cashFlow->bank_id) == $bank->id)>
                        {{ $bank->nama_bank ?? $bank->name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    @if ($type === \App\Models\CashFlow::TYPE_TRANSFER)
        <div class="row">
            <div class="col-6 mb-3">
                <label class="form-label">Ke (Metode)</label>
                <select name="metode_pembayaran_tujuan" class="form-select js-metode"
                    data-target="#bank_id_tujuan_wrap">
                    @foreach ($cashFlow->getPaymentMethods() as $metode)
                        <option value="{{ $metode }}" @selected(old('metode_pembayaran_tujuan', $cashFlow->metode_pembayaran_tujuan ?? 'TRANSFER') === $metode)>{{ $metode }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-6 mb-3" id="bank_id_tujuan_wrap">
                <label class="form-label">Rekening Tujuan</label>
                <select name="bank_id_tujuan" class="form-select">
                    <option value="">-- Pilih Bank --</option>
                    @foreach ($banks as $bank)
                        <option value="{{ $bank->id }}" @selected(old('bank_id_tujuan', $cashFlow->bank_id_tujuan) == $bank->id)>
                            {{ $bank->nama_bank ?? $bank->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    @endif

    <div class="mb-3">
        <label class="form-label">Referensi</label>
        <input type="text" name="referensi" class="form-control"
            value="{{ old('referensi', $isEdit ? $cashFlow->referensi : $referensi_otomatis) }}"
            {{ $isEdit ? '' : 'readonly' }}>
    </div>

    <div class="mb-3">
        <label class="form-label">Keterangan</label>
        <input type="text" name="keterangan" class="form-control"
            value="{{ old('keterangan', $cashFlow->keterangan) }}"
            placeholder="{{ $type === \App\Models\CashFlow::TYPE_TRANSFER ? 'Transfer Internal (opsional)' : '' }}"
            {{ $type === \App\Models\CashFlow::TYPE_TRANSFER ? '' : 'required' }}>
    </div>

    <div class="mb-3">
        <label class="form-label">Catatan (opsional)</label>
        <textarea name="description" class="form-control" rows="2">{{ old('description', $cashFlow->description) }}</textarea>
    </div>

    <div class="mb-3">
        <label class="form-label">Bukti (opsional)</label>
        <input type="file" name="bukti" class="form-control" accept="image/*">
        @if ($isEdit && $cashFlow->bukti)
            <small class="text-muted">File saat ini akan diganti kalau kamu upload baru.</small>
        @endif
    </div>

    <div class="text-danger small mb-3" id="cashFlowFormErrors"></div>

    <button type="submit" class="btn btn-primary w-100">
        {{ $isEdit ? 'Simpan Perubahan' : 'Simpan' }}
    </button>
</form>

<script>
    (function() {
        // Sembunyikan field bank kalau metode bukan TRANSFER (js-metode dipakai untuk sisi asal & tujuan)
        document.querySelectorAll('#cashFlowForm .js-metode').forEach(function(select) {
            var target = document.querySelector(select.dataset.target);

            function toggle() {
                target.style.display = select.value === 'TRANSFER' ? '' : 'none';
            }
            select.addEventListener('change', toggle);
            toggle();
        });
    })();
</script>
