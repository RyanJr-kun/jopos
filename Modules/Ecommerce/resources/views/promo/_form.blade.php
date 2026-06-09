{{-- resources/views/dashboard/promo/_form.blade.php --}}
@props(['promo' => null])

<div class="row g-3">
    <div class="col-md-6">
        <label for="name" class="form-label">Nama Promotion <span class="text-danger">*</span></label>
        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name"
            value="{{ old('name', $promo?->name ?? '') }}" required>
        @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-6">
        <label for="code" class="form-label">Kode Promotion (Opsional)</label>
        <input type="text" class="form-control @error('code') is-invalid @enderror" id="code" name="code"
            value="{{ old('code', $promo?->code ?? '') }}">
        @error('code')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-6">
        <label for="type" class="form-label">Tipe Diskon <span class="text-danger">*</span></label>
        <select class="form-select @error('type') is-invalid @enderror" id="type" name="type" required>
            <option value="percentage" @selected(old('type', $promo?->type ?? '') == 'percentage')>Persentase (%)</option>
            <option value="fixed" @selected(old('type', $promo?->type ?? '') == 'fixed')>Jumlah Tetap (Rp)</option>
        </select>
        @error('type')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-6">
        <label for="nilai_diskon" class="form-label">Nilai Diskon <span class="text-danger">*</span></label>
        <input type="number" step="0.01" class="form-control @error('nilai_diskon') is-invalid @enderror"
            id="nilai_diskon" name="nilai_diskon" value="{{ old('nilai_diskon', $promo?->nilai_diskon ?? '') }}"
            required min="0">
        @error('nilai_diskon')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-6">
        <label for="min_pembelian" class="form-label">Minimum Purchase (Rp)</label>
        <input type="number" step="0.01" class="form-control @error('min_pembelian') is-invalid @enderror"
            id="min_pembelian" name="min_pembelian" value="{{ old('min_pembelian', $promo?->min_pembelian ?? 0) }}"
            min="0">
        @error('min_pembelian')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-6">
        <label for="max_diskon" class="form-label">Maksimal Diskon (Rp) - untuk persentase</label>
        <input type="number" step="0.01" class="form-control @error('max_diskon') is-invalid @enderror"
            id="max_diskon" name="max_diskon" value="{{ old('max_diskon', $promo?->max_diskon ?? '') }}" min="0">
        @error('max_diskon')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-6">
        <label for="tanggal_mulai" class="form-label">Tanggal Mulai <span class="text-danger">*</span></label>
        <input type="datetime-local" class="form-control @error('tanggal_mulai') is-invalid @enderror"
            id="tanggal_mulai" name="tanggal_mulai"
            value="{{ old('tanggal_mulai', ($promo?->tanggal_mulai ?? now())->format('Y-m-d\TH:i')) }}" required>
        @error('tanggal_mulai')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-6">
        <label for="tanggal_berakhir" class="form-label">Tanggal Berakhir <span class="text-danger">*</span></label>
        <input type="datetime-local" class="form-control @error('tanggal_berakhir') is-invalid @enderror"
            id="tanggal_berakhir" name="tanggal_berakhir"
            value="{{ old('tanggal_berakhir', ($promo?->tanggal_berakhir ?? now()->addMonth())->format('Y-m-d\TH:i')) }}"
            required>
        @error('tanggal_berakhir')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-12">
        <label for="quill-description" class="form-label">Description (Opsional)</label>
        <div id="quill-description" style="max-height: 50px;">{!! old('description', $promo?->description ?? '') !!}</div>
        <input type="hidden" name="description" id="description"
            value="{{ old('description', $promo?->description ?? '') }}">
        @error('description')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-12">
        <label for="products" class="form-label select2">Berlaku untuk Product (Opsional)</label>
        <select class="form-select" id="products" name="products[]" multiple>
            @php
                $selectedProductIds = old('products', $promo?->products->pluck('id')->toArray() ?? []);
            @endphp
            @if (!empty($selectedProductIds))
                @foreach (Product::whereIn('id', $selectedProductIds)->get() as $produk)
                    <option value="{{ $produk->id }}" selected>{{ $produk->name_product }}</option>
                @endforeach
            @endif
        </select>
        <small class="form-text text-muted">Pilih satu atau lebih produk. Jika tidak ada produk yang dipilih, promo
            akan berlaku untuk semua produk.</small>
    </div>
    <div class="col-12 form-check form-switch ms-2 mt-3">
        <input class="form-check-input" type="checkbox" id="status" name="status" value="1"
            @checked(old('status', $promo?->status ?? true))>
        <label class="form-check-label" for="status">Status Aktif</label>
    </div>
</div>

@section('vendor-style')
    {{-- Select2 CSS --}}
    <link href="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" />
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
@endsection

@section('page-script')
    {{-- jQuery (pastikan ini dimuat sebelum Select2) --}}
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    {{-- Select2 JS --}}
    <script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        // DIUBAH: Inisialisasi Select2 dengan AJAX dan template kustom
        $(document).ready(function() {
            // Fungsi untuk memformat tampilan item di dropdown
            function formatProduct(produk) {
                if (!produk.id) {
                    return produk.text;
                }

                const imageUrl = produk.image_url;
                const hargaFormatted = new Intl.NumberFormat('id-ID', {
                    style: 'currency',
                    currency: 'IDR',
                    minimumFractionDigits: 0
                }).format(produk.harga_jual);

                var $container = $(
                    `<div class='select2-result-repository clearfix d-flex align-items-center'>
                        <div class='select2-result-repository__avatar'>
                            <img src='${imageUrl}' class='img-fluid rounded-2 avatar avatar-md'/>
                        </div>
                        <div class='select2-result-repository__meta ms-2'>
                            <div class='select2-result-repository__title fw-bold'>${produk.text}</div>
                            <div class='select2-result-repository__description'>
                                <span class='text-sm text-dark'>${hargaFormatted}</span>
                            </div>
                        </div>
                    </div>`
                );

                return $container;
            }

            $('#products').select2({
                theme: 'bootstrap-5',
                placeholder: 'Pilih produk...',
                allowClear: true,
                templateResult: formatProduct, // Terapkan template untuk hasil dropdown
                ajax: {
                    url: "{{ route('get-data.produk') }}", // Pastikan route ini ada dan mengembalikan JSON
                    dataType: 'json',
                    delay: 250, // Tunda request saat user mengetik
                    data: function(params) {
                        return {
                            search: params.term, // Kata kunci pencarian
                            page: params.page || 1
                        };
                    },
                    processResults: function(data, params) {
                        params.page = params.page || 1;
                        // Map data dari controller ke format yang dibutuhkan Select2
                        return {
                            results: data.data.map(item => ({
                                id: item.id,
                                text: item.name_product,
                                image_url: item.image_url,
                                harga_jual: item.harga_jual
                            })),
                            pagination: {
                                more: data.next_page_url !== null
                            }
                        };
                    },
                    cache: true
                }
            });

            // --- QUILL INITIALIZATION ---
            if (document.getElementById('quill-description')) {
                const hiddenInputDescription = document.getElementById('description');
                const quill = new Quill('#quill-description', {
                    theme: 'snow',
                    placeholder: 'Tulis description promo di sini...',
                });

                quill.on('text-change', function() {
                    hiddenInputDescription.value = quill.root.innerHTML;
                });

                // Jika ada old value atau data dari database, set ke editor
                if (hiddenInputDescription.value) {
                    quill.root.innerHTML = hiddenInputDescription.value;
                }
            }
        });
    </script>
@endsection
