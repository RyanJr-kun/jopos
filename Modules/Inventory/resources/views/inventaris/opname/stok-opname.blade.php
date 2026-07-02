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
        <div class="card-header pb-0 px-3 pt-3">
            <h6 class="mb-0">Tahap 1: Pilih Ruang Lingkup (Scope) Opname</h6>
            <p class="text-sm">Tentukan lokasi toko dan kategori produk yang akan dihitung.</p>
        </div>
        <div class="card-body p-3">
            <form method="GET" action="{{ route('stok-opname.index') }}">
                <div class="row g-3 align-items-end">
                    {{-- DROPDOWN PILIH TOKO --}}
                    <div class="col-md-4">
                        <label class="form-label">Lokasi Toko / Cabang <span class="text-danger">*</span></label>
                        <select name="store_id" id="storeFilter" class="form-select" required>
                            <option value="">-- Pilih Toko --</option>
                            @foreach ($tokos as $toko)
                                <option value="{{ $toko->id }}" @selected(request('store_id') == $toko->id)>
                                    {{ $toko->name_toko ?? '-' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    {{-- DROPDOWN PILIH KATEGORI --}}
                    <div class="col-md-4">
                        <label class="form-label">Kategori Produk <span class="text-danger">*</span></label>
                        <select name="kategori" id="categoryFilter" class="form-select" required>
                            <option value="">-- Pilih Kategori --</option>
                            @foreach ($kategoris as $kategori)
                                <option value="{{ $kategori->id }}" @selected(request('kategori') == $kategori->id)>
                                    {{ $kategori->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary w-100">
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
            <div class="card-header pb-0 px-3 pt-3 mb-3 d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="mb-0">Tahap 2: Lembar Kerja Opname</h6>
                    <p class="text-sm text-warning mb-0"><i class="bx bx-info-circle"></i> Selesaikan perhitungan
                        sebelum menutup halaman ini.</p>
                </div>
                <a href="{{ route('stok-opname.history') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bx bx-clock-history me-1"></i>Riwayat
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
                                    @php
                                        // Ambil total quantity dari baris stok toko yang terfilter
                                        $currentStock = $produk->stocks->first()?->qty ?? 0;
                                    @endphp
                                    <tr class="opname-row">
                                        <td>
                                            <div class="d-flex px-2 py-1">
                                                <div><img src="{{ $produk->image_url ?? asset('assets/img/produk.png') }}"
                                                        class="avatar avatar-sm me-3" alt="product image"></div>
                                                <div class="d-flex flex-column justify-content-center">
                                                    <h6 class="mb-0 text-sm">{{ $produk->name_product }}</h6>
                                                    <p class="text-xs text-secondary mb-0">
                                                        {{ $produk->sku ?: 'No SKU' }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="align-middle text-center text-sm">
                                            <span class="fw-bold system-stock">{{ $currentStock }}</span>
                                            {{-- Input hidden untuk pengecekan bentrok data --}}
                                            <input type="hidden" name="items[{{ $produk->id }}][stok_sistem_awal]"
                                                value="{{ $currentStock }}">
                                        </td>
                                        <td class="align-middle text-center">
                                            <input type="number" name="items[{{ $produk->id }}][stok_fisik]"
                                                class="form-control form-control-sm physical-stock-input mx-auto"
                                                value="{{ $currentStock }}" min="0" autocomplete="off">
                                        </td>
                                        <td class="align-middle text-center text-sm">
                                            <span class="difference-cell difference-zero">0</span>
                                        </td>
                                        <td class="align-middle">
                                            <input type="text" name="items[{{ $produk->id }}][keterangan]"
                                                class="form-control form-control-sm" placeholder="Catatan jika selisih...">
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4">
                                            <p class="text-dark text-sm fw-bold mb-0">Tidak ada produk di kategori ini.
                                            </p>
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
            <i class="bx bx-box text-muted mb-3" style="font-size: 3rem;"></i>
            <h5>Pilih Kategori Terlebih Dahulu</h5>
            <p class="text-muted">Untuk memulai stok opname dan meminimalisir kesalahan, silakan pilih kategori produk
                yang ingin dihitung pada form di atas.</p>
        </div>
    @endif
@endsection

@section('page-script')
    <script>
        document.addEventListener('DOMContentLoaded', function() {

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
