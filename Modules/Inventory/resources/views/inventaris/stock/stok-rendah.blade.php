@extends('layouts/contentNavbarLayout')

@section('title', 'Laporan Stock Rendah')
@section('content')

    <div class="card">
        <div class="card-header pb-2">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-0 fw-bold text-poppins">Stock Rendah</h5>
                    <span class="mb-0 text-sm">List produk dengan stok minim</span>
                </div>
            </div>
        </div>
        <div class="card-body px-0 pb-2">
            <div class="px-5 mb-4">
                <form action="{{ route('stok.rendah') }}" method="GET" onsubmit="return false;">
                    <div class="row g-2">
                        <div class="col-md-5">
                            <input type="text" id="searchInput" name="search" class="form-control"
                                placeholder="Cari nama, SKU, atau barcode produk..." value="{{ request('search') }}">
                        </div>

                        <div class="col-md-4">
                            <select id="kategoriFilter" name="kategori" class="form-select select2"
                                data-placeholder="Filter Kategori">
                                <option value="">Semua Kategori</option>
                                @foreach ($kategoris->whereNull('parent_id') as $parent)
                                    <option value="{{ $parent->id }}" data-level="parent">{{ $parent->name }}</option>
                                    @foreach ($kategoris->where('parent_id', $parent->id) as $child)
                                        <option value="{{ $child->id }}" data-level="child">{{ $child->name }}</option>
                                    @endforeach
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3">
                            <select id="supplierFilter" name="supplier" class="form-select select2"
                                data-placeholder="Filter Supplier">
                                <option value="">Semua Supplier</option>
                                @foreach ($pemasoks as $pemasok)
                                    <option value="{{ $pemasok->id }}">{{ $pemasok->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </form>
            </div>

            <div id="tableContainer">
                @include('inventory::inventaris.stock.partials.tabel-stok-rendah')
            </div>
        </div>
    </div>

@endsection

@section('page-script')
    <script type="module">
        let timer;

        // 1. Fungsi AJAX Utama
        function fetchProducts(url = null) {
            let search = $('#searchInput').val();
            let kategori = $('#kategoriFilter').val();
            let supplier = $('#supplierFilter').val(); // ✅ Ambil value supplier
            let targetUrl = url ? url : '{{ route('stok.rendah') }}';

            $.ajax({
                url: targetUrl,
                type: "GET",
                data: {
                    search: search,
                    kategori: kategori,
                    supplier: supplier // ✅ Kirim ke controller
                },
                beforeSend: function() {
                    $('#tableContainer').css('opacity', '0.5');
                },
                success: function(response) {
                    $('#tableContainer').html(response);
                    $('#tableContainer').css('opacity', '1');
                },
                error: function(xhr) {
                    console.log(xhr.responseText);
                    $('#tableContainer').css('opacity', '1');
                }
            });
        }

        // 2. Fungsi Modifikasi Tampilan Dropdown Select2 (Pola Folder)
        function formatKategori(state) {
            if (!state.id) {
                return state.text;
            }

            let $element = $(state.element);

            // Jika elemen memiliki data-level="child"
            if ($element.data('level') === 'child') {
                return $(
                    '<div style="padding-left: 1.5rem; display: flex; align-items: center;">' +
                    '<i class="bx bx-subdirectory-right text-muted me-2" style="font-size: 1.1rem;"></i>' +
                    state.text +
                    '</div>'
                );
            }

            // Jika elemen adalah Parent
            return $('<div class="fw-bold text-dark">' + state.text + '</div>');
        }

        // 3. Fungsi Inisialisasi Terproteksi
        const initScripts = () => {
            if (typeof $ !== 'undefined' && $.fn.select2) {

                // Inisialisasi Select2 Kategori
                $('#kategoriFilter').select2({
                    placeholder: "Filter Kategori",
                    allowClear: true,
                    width: '100%',
                    minimumResultsForSearch: 10,
                    templateResult: formatKategori
                });

                // ✅ Inisialisasi Select2 Supplier
                $('#supplierFilter').select2({
                    placeholder: "Filter Supplier",
                    allowClear: true,
                    width: '100%',
                    minimumResultsForSearch: 10
                });

                // --- Pemasangan Event Listeners ---

                // ✅ Event khusus Select2 (saat kategori ATAU supplier dipilih/di-clear)
                $('#kategoriFilter, #supplierFilter').on('select2:select select2:clear', function(e) {
                    fetchProducts();
                });

                // Event ketik di kotak pencarian
                $('#searchInput').on('keyup', function() {
                    clearTimeout(timer);
                    timer = setTimeout(function() {
                        fetchProducts();
                    }, 500);
                });

                // Event klik pagination AJAX
                $(document).off('click', '.custom-pagination a').on('click', '.custom-pagination a', function(e) {
                    e.preventDefault();
                    let url = $(this).attr('href');
                    fetchProducts(url);
                });

            } else {
                setTimeout(initScripts, 100);
            }
        };

        // Jalankan pelindung script saat dokumen siap
        $(document).ready(function() {
            initScripts();
        });
    </script>
@endsection
