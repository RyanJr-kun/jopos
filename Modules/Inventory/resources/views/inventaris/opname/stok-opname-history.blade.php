@extends('layouts/contentNavbarLayout')

@section('title', 'Cards basic - UI elements')
@section('content')
    <div class="row g-3">
        <div class="col-12 col-md-8 col-xl-9">
            <div class="card h-100 shadow-sm">
                <div class="card-body d-flex align-items-center">
                    <div class="row g-3 align-items-center justify-content-start w-100 m-0">
                        <div class="col-md-5">
                            <input type="text" name="search" id="searchInput" class="form-control"
                                placeholder="Cari kode opname atau user..." value="{{ request('search') }}">
                        </div>
                        <div class="col-md-3 col-6">
                            <select id="statusFilter" name="status" class="form-select select2"
                                data-placeholder="Semua Status">
                                <option value="">semua status</option>
                                @foreach ($statuses as $status)
                                    <option value="{{ $status }}" @selected(request('status') == $status)>
                                        {{ $status }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @if ($canViewAllStores)
                            <div class="col-md-4 col-6">
                                <select id="storeFilter" name="store" class="form-select select2"
                                    data-placeholder="Semua Toko">
                                    <option value="">Semua Toko</option>
                                    @foreach ($stores as $store)
                                        <option value="{{ $store->id }}" @selected(request('store_id') == $store->id)>
                                            {{ $store->name_toko }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card rounded-2">
                <div class="card-header pb-0 mb-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-n1 fw-bold">Riwayat Stock Opname</h6>
                            <p class="text-sm mb-0">Daftar semua sesi stok opname.</p>
                        </div>
                        <a href="{{ route('stok-opname.index') }}" class="btn btn-outline-info px-2"
                            title="Tambah Stok Opname" data-bs-toggle="tooltip" data-bs-placement="top">
                            <i class="bx bx-plus-circle"></i>
                        </a>
                    </div>
                </div>
                <div class="card-body px-0 pt-0 pb-2">
                    <div id="stokOpnameTableContainer">
                        @include('inventory::inventaris.opname._stok_opname_table', [
                            'stokOpnames' => $stokOpnames,
                        ])
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('page-script')
    {{-- Script Module untuk Select2 --}}
    <script type="module">
        const initSelect2 = () => {
            if (typeof $ !== 'undefined' && $.fn.select2) {
                $('.select2').each(function() {
                    const $this = $(this);
                    $this.select2({
                        placeholder: $this.data('placeholder') || "Pilih...",
                        allowClear: $this.find('option[value=""]').length > 0,
                        width: '100%',
                        minimumResultsForSearch: 10
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
            // --- AJAX FILTER & SEARCH ---
            if (typeof $ !== 'undefined') {
                $(document).ready(function() {
                    // Fungsi untuk menunda eksekusi (debounce)
                    function debounce(func, delay) {
                        let timeout;
                        return function(...args) {
                            clearTimeout(timeout);
                            timeout = setTimeout(() => func.apply(this, args), delay);
                        };
                    }

                    // Fungsi untuk mengambil data dengan AJAX
                    function fetchData(page = 1) {
                        let search = $('#searchInput').val();
                        let store = $('#storeFilter').val(); // BARU
                        let status = $('#statusFilter').val(); // BARU
                        let url = '{{ route('stok-opname.history') }}';

                        $('#stokOpnameTableContainer').css('opacity', 0.5);

                        $.ajax({
                            url: url,
                            type: 'GET',
                            data: {
                                search: search,
                                store_id: store,
                                status: status,
                                page: page
                            }, // BARU
                            success: function(data) {
                                $('#stokOpnameTableContainer').html(data).css('opacity', 1);

                                let newParams = new URLSearchParams();
                                if (page > 1) newParams.append('page', page);
                                if (search) newParams.append('search', search);
                                if (store) newParams.append('store_id', store); // BARU
                                if (status) newParams.append('status', status); // BARU

                                let newUrl = url + (newParams.toString() ? '?' + newParams
                                    .toString() : '');
                                window.history.pushState({
                                    path: newUrl
                                }, '', newUrl);
                            },
                            error: function() {
                                $('#stokOpnameTableContainer').css('opacity', 1);
                                alert('Gagal memuat data. Silakan coba lagi.');
                            }
                        });
                    }

                    // Event Listener Search (Ketik)
                    $('#searchInput').on('keyup', debounce(function() {
                        fetchData(1);
                    }, 500));

                    $('#storeFilter').on('change', function() {
                        fetchData(1);
                    });

                    $('#statusFilter').on('change', function() {
                        fetchData(1);
                    });

                    // Event Listener Pagination
                    $(document).on('click', '#stokOpnameTableContainer .pagination a', function(e) {
                        e.preventDefault();
                        let urlObj = new URL($(this).attr('href'));
                        let page = urlObj.searchParams.get('page');
                        if (page) fetchData(page);
                    });
                });
            }
        });
    </script>
@endsection
