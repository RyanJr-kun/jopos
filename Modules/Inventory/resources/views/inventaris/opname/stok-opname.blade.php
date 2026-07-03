@extends('layouts/contentNavbarLayout')

@section('title', 'Stock Opname')

@section('vendor-style')
    <style>
        .physical-stock-input {
            width: 100px;
            text-align: center;
        }

        .difference-positive {
            color: #2dce89;
            font-weight: bold;
        }

        .difference-negative {
            color: #f5365c;
            font-weight: bold;
        }

        .difference-zero {
            color: #8898aa;
        }

        /* Highlight baris yang sudah diedit */
        .row-edited {
            background-color: #f8f9fa !important;
            border-left: 4px solid #696cff;
        }
    </style>
@endsection

@section('content')
    <div class="card rounded-2 mb-4">
        <div class="card-header pb-0">
            <h6 class="mb-0">Pilih Lokasi dan Kategori</h6>
            <p class="text-sm">Tentukan lokasi toko dan kategori produk yang akan dihitung.</p>
        </div>
        <div class="card-body p-3">
            <form method="GET" action="{{ route('stok-opname.index') }}">
                <div class="row g-3 align-items-end">
                    {{-- TOKO --}}
                    <div class="col-md-3">
                        <label class="form-label">Lokasi Toko / Cabang <span class="text-danger">*</span></label>
                        <select name="store_id" id="storeFilter" class="form-select select2" required
                            data-placeholder="-- Pilih Toko --">
                            <option value="">-- Pilih Toko --</option>
                            @foreach ($tokos as $toko)
                                <option value="{{ $toko->id }}" @selected(request('store_id') == $toko->id)>
                                    {{ $toko->name_toko ?? '-' }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- KATEGORI UTAMA --}}
                    <div class="col-md-3">
                        <label class="form-label">Kategori Utama <span class="text-danger">*</span></label>
                        <select name="kategori" id="categoryFilter" class="form-select select2" required
                            data-placeholder="-- Pilih Kategori --">
                            <option value="">-- Pilih Kategori --</option>
                            @foreach ($kategoris as $kategori)
                                @php
                                    $isActive =
                                        request('kategori') == $kategori->id ||
                                        $kategori->children->contains('id', request('kategori'));
                                @endphp
                                @if ($kategori->children->isNotEmpty())
                                    <option value="{{ $kategori->id }}" @selected($selectedKategori == $kategori->id)>
                                        {{ $kategori->name }}
                                    </option>
                                @endif
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Subkategori <span class="text-muted">(opsional)</span></label>
                        <select name="subkategori" id="subCategoryFilter" class="form-select select2"
                            data-placeholder="Semua Subkategori">
                            <option value="">Semua Subkategori</option>
                            @foreach ($kategoris as $kat)
                                @foreach ($kat->children as $child)
                                    <option value="{{ $child->id }}" data-parent="{{ $kat->id }}"
                                        @selected($selectedSubKategori == $child->id)>
                                        {{ $child->name }}
                                    </option>
                                @endforeach
                            @endforeach
                        </select>
                    </div>

                    {{-- <div class="col-md-3">
                        <label class="form-label">Cari Produk</label>
                        <input type="text" name="search" class="form-control" placeholder="Nama produk atau SKU..."
                            value="{{ request('search') }}">
                    </div> --}}

                    <div class="col-md-3 d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="bx bx-play-circle me-2"></i>Buka Lembar Kerja
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- TAHAP 2: LEMBAR KERJA (WORKSHEET) OPNAME --}}
    @if ($selectedKategori && $selectedToko)
        <div class="card rounded-2">
            <div class="card-header pb-0 mb-3 d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="mb-0">Cek Stok fisik Sesuai Produk</h6>
                    <p class="text-sm text-warning mb-0"><i class="bx bx-info-circle"></i> Selesaikan perhitungan
                        sebelum menutup halaman ini.</p>
                </div>
                <a href="{{ route('stok-opname.history') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bx bx-history me-2"></i>Riwayat
                </a>
            </div>
            <div class="card-body px-0 pt-0 pb-2">
                <form action="{{ route('stok-opname.store') }}" method="POST" id="stockOpnameForm">
                    @csrf
                    <input type="hidden" name="store_id" value="{{ $selectedToko }}">
                    <div class="px-3 mb-3">
                        <label for="catatan_opname" class="form-label">Catatan Opname</label>
                        <textarea name="catatan_opname" id="catatan_opname" class="form-control" rows="2"
                            placeholder="Contoh: Pengecekan stok etalase depan..."></textarea>
                    </div>

                    <div class="px-3 mb-3">
                        <input type="text" id="produkQuickSearch" class="form-control"
                            placeholder="Filter cepat produk yang sudah tampil (tanpa reload)...">
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-items-center mb-0" id="opnameTable">
                            <thead class="table-secondary">
                                <tr>
                                    <th class="text-uppercase text-dark text-xs font-weight-bolder ps-4">Product</th>
                                    <th class="text-center text-uppercase text-dark text-xs font-weight-bolder">Stock
                                        Sistem</th>
                                    <th class="text-center text-uppercase text-dark text-xs font-weight-bolder">Stock
                                        Fisik</th>
                                    <th class="text-center text-uppercase text-dark text-xs font-weight-bolder">Selisih
                                    </th>
                                    <th class="text-uppercase text-dark text-xs font-weight-bolder">Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($products as $produk)
                                    @if ($produk->variants && $produk->variants->isNotEmpty())
                                        {{-- JIKA PRODUK MEMILIKI VARIAN --}}
                                        @foreach ($produk->variants as $variant)
                                            @php
                                                $varStok =
                                                    $produk->stocks
                                                        ->where('product_variant_id', $variant->id)
                                                        ->sum('qty') ?? 0;

                                                $itemKey = $produk->id . '_v_' . $variant->id;
                                                $imgUrl =
                                                    $variant->img_variant && $variant->img_variant
                                                        ? Storage::url($variant->img_variant)
                                                        : ($produk->primaryImage && $produk->primaryImage->path
                                                            ? Storage::url($produk->primaryImage->path)
                                                            : asset('assets/img/produk.png'));
                                            @endphp
                                            <tr class="opname-row">
                                                <td>
                                                    <div class="d-flex px-2 py-1">
                                                        <div><img src="{{ $imgUrl }}" class="avatar avatar-sm me-3"
                                                                alt="product image"></div>
                                                        <div class="d-flex flex-column justify-content-center">
                                                            <h6 class="mb-0 text-sm">{{ $produk->name_product }}</h6>
                                                            <p class="text-xs text-secondary mb-0">
                                                                <span
                                                                    class="badge bg-label-info">{{ $variant->label }}</span>
                                                                | SKU: {{ $variant->sku ?: ($produk->sku ?: 'No SKU') }}
                                                            </p>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="align-middle text-center text-sm">
                                                    <span class="fw-bold system-stock">{{ $varStok }}</span>
                                                    {{-- Input Hidden Baru --}}
                                                    <input type="hidden" name="items[{{ $itemKey }}][product_id]"
                                                        value="{{ $produk->id }}">
                                                    <input type="hidden"
                                                        name="items[{{ $itemKey }}][product_variant_id]"
                                                        value="{{ $variant->id }}">
                                                    <input type="hidden"
                                                        name="items[{{ $itemKey }}][stok_sistem_awal]"
                                                        value="{{ $varStok }}">
                                                </td>
                                                <td class="align-middle text-center">
                                                    <input type="number" name="items[{{ $itemKey }}][stok_fisik]"
                                                        class="form-control form-control-sm physical-stock-input mx-auto"
                                                        value="{{ $varStok }}" min="0" autocomplete="off">
                                                </td>
                                                <td class="align-middle text-center text-sm">
                                                    <span class="difference-cell difference-zero">0</span>
                                                </td>
                                                <td class="align-middle">
                                                    <input type="text" name="items[{{ $itemKey }}][keterangan]"
                                                        class="form-control form-control-sm"
                                                        placeholder="Catatan jika selisih...">
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        {{-- JIKA PRODUK TUNGGAL (TANPA VARIAN) --}}
                                        @php
                                            $currentStock = $produk->stocks->first()?->qty ?? 0;
                                            $itemKey = $produk->id . '_v_0';
                                            $imgUrl =
                                                $produk->primaryImage && $produk->primaryImage->path
                                                    ? Storage::url($produk->primaryImage->path)
                                                    : asset('assets/img/produk.png');
                                        @endphp
                                        <tr class="opname-row">
                                            <td>
                                                <div class="d-flex px-2 py-1">
                                                    <div><img src="{{ $imgUrl }}" class="avatar avatar-sm me-3"
                                                            alt="product image"></div>
                                                    <div class="d-flex flex-column justify-content-center">
                                                        <h6 class="mb-0 text-sm">{{ $produk->name_product }}</h6>
                                                        <p class="text-xs text-secondary mb-0">
                                                            {{ $produk->sku ?: 'No SKU' }}</p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="align-middle text-center text-sm">
                                                <span class="fw-bold system-stock">{{ $currentStock }}</span>
                                                {{-- Input Hidden Baru --}}
                                                <input type="hidden" name="items[{{ $itemKey }}][product_id]"
                                                    value="{{ $produk->id }}">
                                                <input type="hidden"
                                                    name="items[{{ $itemKey }}][product_variant_id]" value="">
                                                <input type="hidden" name="items[{{ $itemKey }}][stok_sistem_awal]"
                                                    value="{{ $currentStock }}">
                                            </td>
                                            <td class="align-middle text-center">
                                                <input type="number" name="items[{{ $itemKey }}][stok_fisik]"
                                                    class="form-control form-control-sm physical-stock-input mx-auto"
                                                    value="{{ $currentStock }}" min="0" autocomplete="off">
                                            </td>
                                            <td class="align-middle text-center text-sm">
                                                <span class="difference-cell difference-zero">0</span>
                                            </td>
                                            <td class="align-middle">
                                                <input type="text" name="items[{{ $itemKey }}][keterangan]"
                                                    class="form-control form-control-sm"
                                                    placeholder="Catatan jika selisih...">
                                            </td>
                                        </tr>
                                    @endif

                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4">
                                            <p class="text-dark text-sm fw-bold mb-0">Tidak ada produk di kategori ini.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                        {{-- Pagination Dihapus. Kita load semua item dalam kategori terpilih --}}
                    </div>

                    <div class="card-footer text-end mt-3 border-top">
                        <button type="button" class="btn btn-info" id="btnSimpan"
                            {{ $products->isEmpty() ? 'disabled' : '' }}>
                            <i class="bx bx-save me-2"></i>Simpan Hasil Opname
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @else
        {{-- Tampilan instruksi jika kategori belum dipilih --}}
        <div class="card rounded-2 bg-transparent shadow-none border border-dashed text-center p-5 mt-4">
            <div class="d-flex flex-column align-items-center justify-content-center">
                <i class="bx bx-box text-muted mb-3" style="font-size: 3rem;"></i>
                <h5>Pilih Kategori Terlebih Dahulu</h5>
                <p class="text-muted">Untuk memulai stok opname dan meminimalisir kesalahan, silakan pilih kategori produk
                    yang ingin dihitung pada form di atas.</p>
            </div>
        </div>
    @endif
@endsection

@section('page-script')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script type="module">
        const initSelect2 = () => {
            if (typeof $ !== 'undefined' && $.fn.select2) {
                $('.select2').each(function() {
                    const $this = $(this);
                    $this.select2({
                        placeholder: $this.data('placeholder') || "Pilih...",
                        allowClear: false,
                        width: '100%'
                    });
                });
            } else {
                setTimeout(initSelect2, 100);
            }
        };
        initSelect2();
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // 1. Simpan semua opsi subkategori (cloning) ke memori saat halaman pertama kali dimuat
            const $subSelect = $('#subCategoryFilter');
            const allSubOptions = $subSelect.find('option').clone();

            function filterSubCategoryOptions() {
                const selectedParent = $('#categoryFilter').val();

                // Simpan value subkategori yang sedang terpilih (jika ada)
                const currentSelectedSub = $subSelect.val();

                // 2. Kosongkan semua opsi di dropdown subkategori saat ini
                $subSelect.empty();

                // 3. Masukkan kembali opsi default (Semua Subkategori)
                $subSelect.append(allSubOptions.filter('[value=""]'));

                if (!selectedParent) {
                    // Jika kategori utama kosong, matikan dropdown subkategori
                    $subSelect.prop('disabled', true);
                } else {
                    // Jika kategori utama dipilih, hidupkan dropdown
                    $subSelect.prop('disabled', false);

                    // 4. Cari opsi subkategori yang data-parent-nya cocok dengan kategori utama, lalu masukkan
                    const matchingOptions = allSubOptions.filter(function() {
                        return $(this).data('parent') == selectedParent;
                    });
                    $subSelect.append(matchingOptions);
                }

                // 5. Kembalikan pilihan subkategori sebelumnya (jika opsi tersebut masih ada di list yang baru)
                if (currentSelectedSub && $subSelect.find(`option[value="${currentSelectedSub}"]`).length > 0) {
                    $subSelect.val(currentSelectedSub);
                } else {
                    $subSelect.val(''); // Reset jika tidak cocok
                }

                // 6. Refresh tampilan Select2 agar membaca data terbaru
                $subSelect.trigger('change.select2');
            }

            // Panggil fungsi saat Kategori Utama diubah oleh user
            $('#categoryFilter').on('change', filterSubCategoryOptions);

            // Panggil sekali saat halaman dimuat (untuk menangani state dari URL/query string)
            setTimeout(filterSubCategoryOptions, 200);
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            // Quick filter client-side untuk baris yang sudah tampil (tidak reset input yang sedang diisi)
            const produkQuickSearch = document.getElementById('produkQuickSearch');
            if (produkQuickSearch) {
                produkQuickSearch.addEventListener('keyup', function() {
                    const keyword = this.value.toLowerCase().trim();
                    document.querySelectorAll('#opnameTable tbody tr.opname-row').forEach(function(row) {
                        const nama = row.querySelector('h6')?.textContent.toLowerCase() || '';
                        const sku = row.querySelector('p.text-xs.text-secondary')?.textContent
                            .toLowerCase() || '';
                        row.style.display = (nama.includes(keyword) || sku.includes(keyword)) ? '' :
                            'none';
                    });
                });
            }

            // Fungsi menghitung selisih dan mewarnai baris
            function calculateDifference(inputElement) {
                const row = inputElement.closest('.opname-row');
                const systemStock = parseInt(row.querySelector('.system-stock').textContent, 10);
                const physicalStock = parseInt(inputElement.value, 10);
                const differenceCell = row.querySelector('.difference-cell');

                // Beri tanda warna pada row jika user mengedit input (UI Feedback)
                row.classList.add('row-edited');

                if (isNaN(physicalStock)) {
                    differenceCell.textContent = '-';
                    differenceCell.className = 'difference-cell';
                    return;
                }

                const difference = physicalStock - systemStock;
                differenceCell.textContent = difference > 0 ? `+${difference}` : difference;

                differenceCell.classList.remove('difference-positive', 'difference-negative', 'difference-zero');
                if (difference > 0) differenceCell.classList.add('difference-positive');
                else if (difference < 0) differenceCell.classList.add('difference-negative');
                else differenceCell.classList.add('difference-zero');
            }

            // Pasang event listener ke semua input stok
            document.querySelectorAll('.physical-stock-input').forEach(input => {
                input.addEventListener('input', function() {
                    calculateDifference(this);
                });
            });

            // Konfirmasi SweetAlert sebelum submit form
            const btnSimpan = document.getElementById('btnSimpan');
            if (btnSimpan) {
                btnSimpan.addEventListener('click', function(e) {
                    e.preventDefault();

                    Swal.fire({
                        title: 'Simpan Hasil Opname?',
                        text: "Data stok akan diperbarui ke sistem secara permanen.",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Ya, Simpan!',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Tampilkan loading state pada tombol
                            btnSimpan.innerHTML =
                                '<span class="spinner-border spinner-border-sm me-2"></span>Menyimpan...';
                            btnSimpan.disabled = true;

                            // Submit form
                            document.getElementById('stockOpnameForm').submit();
                        }
                    });
                });
            }
        });
    </script>
@endsection
