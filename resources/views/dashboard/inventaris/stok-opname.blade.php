@extends('layouts/contentNavbarLayout')

@section('title', 'Cards basic - UI elements')
@section('content')
@section('vendor-style')
    {{-- Select2 untuk filter yang lebih baik --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" />
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
    <style>
        /* Style untuk input stok fisik agar lebih rapi */
        .physical-stock-input {
            width: 100px;
            text-align: center;
        }

        /* Style untuk selisih agar lebih jelas */
        .difference-positive {
            color: #2dce89;
            /* green */
            font-weight: bold;
        }

        .difference-negative {
            color: #f5365c;
            /* red */
            font-weight: bold;
        }

        .difference-zero {
            color: #8898aa;
            /* gray */
        }
    </style>
@endsection
{{-- breadcrumb --}}
@section('breadcrumb')
    @php
        // Asumsi route 'stok-opname.index' sudah ada
        $breadcrumbItems = [
            ['name' => 'Dashboard', 'url' => '/dashboard'],
            ['name' => 'Stok Opname', 'url' => route('stok-opname.index')],
        ];
    @endphp
    <x-breadcrumb :items="$breadcrumbItems" />
@endsection

<div class="container-fluid p-3">
    {{-- Asumsi controller akan mengirimkan variabel $produks dan $kategoris --}}
    @php
        $produks = $produks ?? collect();
        $kategoris = $kategoris ?? collect();
    @endphp
    <div class="card rounded-2">
        <div class="card-header pb-0 px-3 pt-2 mb-3">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="mb-n1">Stok Opname</h6>
                    <p class="text-sm mb-0">Sesuaikan stok fisik dengan stok sistem.</p>
                </div>
                <div class="ms-md-auto mt-2">
                    {{-- Tombol ini bisa digunakan untuk melihat riwayat stok opname --}}
                    <a href="{{ route('stok-opname.history') }}" class="btn btn-outline-secondary mb-0">
                        <i class="bx bx-clock-history me-2"></i>Riwayat
                    </a>
                </div>
            </div>
        </div>
        <div class="card-body px-0 pt-0 pb-2">
            {{-- Form untuk filter --}}
            <form method="GET" action="{{ route('stok-opname.index') }}" id="filterForm">
                <div class="filter-container p-3">
                    <div class="row g-3 align-items-center justify-content-start">
                        <div class="col-md-4">
                            <input type="text" name="search" id="searchInput" class="form-control"
                                placeholder="Cari nama atau SKU produk..." value="{{ request('search') }}">
                        </div>
                        <div class="col-md-3">
                            <select name="kategori" id="categoryFilter" class="form-select">
                                <option value="">Semua Kategori</option>
                                @foreach ($kategoris as $kategori)
                                    <option value="{{ $kategori->id }}" @selected(request('kategori') == $kategori->id)>
                                        {{ $kategori->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </form>

            <form action="{{ route('stok-opname.store') }}" method="POST" id="stockOpnameForm">
                <div class="p-3">
                    <div class="form-group">
                        <label for="catatan_opname" class="form-label">Catatan Umum (Opsional)</label>
                        <textarea name="catatan_opname" id="catatan_opname" class="form-control" rows="2"
                            placeholder="Contoh: Stok opname bulanan untuk gudang utama"></textarea>
                    </div>
                </div>

                @csrf
                <div id="stock-opname-table-container" class="mt-3 table-responsive">
                    <table class="table table-hover align-items-center mb-0">
                        <thead class="table-secondary">
                            <tr>
                                <th class="text-uppercase text-dark text-xs font-weight-bolder ps-4">Produk</th>
                                <th class="text-center text-uppercase text-dark text-xs font-weight-bolder">Stok Sistem
                                </th>
                                <th class="text-center text-uppercase text-dark text-xs font-weight-bolder">Stok Fisik
                                </th>
                                <th class="text-center text-uppercase text-dark text-xs font-weight-bolder">Selisih</th>
                                <th class="text-uppercase text-dark text-xs font-weight-bolder">Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($produks as $produk)
                                <tr class="opname-row">
                                    <td>
                                        <div class="d-flex px-2 py-1">
                                            <div>
                                                <img src="{{ $produk->img_produk ? asset('storage/' . $produk->img_produk) : asset('assets/img/produk.webp') }}"
                                                    class="avatar avatar-sm me-3" alt="product image">
                                            </div>
                                            <div class="d-flex flex-column justify-content-center">
                                                <h6 class="mb-0 text-sm">{{ $produk->nama_produk }}</h6>
                                                <p class="text-xs text-secondary mb-0">
                                                    {{ $produk->sku ?: 'SKU tidak ada' }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="align-middle text-center text-sm">
                                        <span class="fw-bold system-stock">{{ $produk->qty }}</span>
                                    </td>
                                    <td class="align-middle text-center">
                                        <input type="number" name="items[{{ $produk->id }}][stok_fisik]"
                                            class="form-control form-control-sm physical-stock-input mx-auto"
                                            value="{{ $produk->qty }}" min="0">
                                    </td>
                                    <td class="align-middle text-center text-sm">
                                        <span class="difference-cell difference-zero">0</span>
                                    </td>
                                    <td class="align-middle">
                                        <input type="text" name="items[{{ $produk->id }}][keterangan]"
                                            class="form-control form-control-sm" placeholder="Catatan...">
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4">
                                        <p class="text-dark text-sm fw-bold mb-0">Tidak ada produk untuk ditampilkan.
                                        </p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    <div class="d-flex justify-content-center my-4">
                        {{ $produks->links() }}
                    </div>
                </div>

                <div class="card-footer text-end">
                    <button type="submit" class="btn btn-info" {{ $produks->isEmpty() ? 'disabled' : '' }}>
                        <i class="bx bx-save me-2"></i>Simpan Hasil Opname
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@section('page-script')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Inisialisasi Select2
            $('#categoryFilter').select2({
                theme: 'bootstrap-5',
                placeholder: 'Semua Kategori',
                allowClear: true
            });

            // Fungsi untuk menghitung selisih
            function calculateDifference(row) {
                const systemStockEl = row.querySelector('.system-stock');
                const physicalStockInput = row.querySelector('.physical-stock-input');
                const differenceCell = row.querySelector('.difference-cell');

                const systemStock = parseInt(systemStockEl.textContent, 10);
                const physicalStock = parseInt(physicalStockInput.value, 10);

                if (isNaN(physicalStock)) {
                    differenceCell.textContent = '-';
                    differenceCell.className = 'difference-cell';
                    return;
                }

                const difference = physicalStock - systemStock;
                differenceCell.textContent = difference > 0 ? `+${difference}` : difference;

                // Hapus kelas warna sebelumnya dan tambahkan yang baru
                differenceCell.classList.remove('difference-positive', 'difference-negative', 'difference-zero');
                if (difference > 0) {
                    differenceCell.classList.add('difference-positive');
                } else if (difference < 0) {
                    differenceCell.classList.add('difference-negative');
                } else {
                    differenceCell.classList.add('difference-zero');
                }
            }

            // Tambahkan event listener ke semua input stok fisik
            document.querySelectorAll('.physical-stock-input').forEach(input => {
                input.addEventListener('input', function() {
                    const row = this.closest('.opname-row');
                    calculateDifference(row);
                });
                // Hitung selisih awal saat halaman dimuat
                calculateDifference(input.closest('.opname-row'));
            });

            // --- LOGIKA UNTUK SUBMIT FORM FILTER SECARA OTOMATIS ---
            const filterForm = document.getElementById('filterForm');
            const searchInput = document.getElementById('searchInput');
            let searchTimeout;

            // Submit form setelah user berhenti mengetik selama 500ms
            searchInput.addEventListener('keyup', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    filterForm.submit();
                }, 500); // 500ms delay
            });

            // Submit form saat kategori diubah
            $('#categoryFilter').on('change', function() {
                filterForm.submit();
            });
        });
    </script>
@endsection
@endsection
