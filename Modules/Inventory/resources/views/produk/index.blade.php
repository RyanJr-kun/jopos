@extends('layouts/contentNavbarLayout')

@section('title', 'Manajemen Produk')
@section('content')

    <div class="row g-3 align-items-stretch">
        <div class="col-12 col-md-4 col-xl-3 mb-md-0">
            <div class="card h-100 border-0 shadow-sm" style="background: linear-gradient(135deg, #4d50eb 0%, #8592ff 100%);">
                <div class="card-body d-flex align-items-center">
                    <div class="avatar avatar-md me-3">
                        <span class="avatar-initial rounded bg-white text-primary shadow-sm">
                            <i class="bx bx-product fs-4"></i>
                        </span>
                    </div>
                    <div>
                        <p class="text-white mb-0 text-sm">Total Produk</p>
                        <h3 class="text-white mb-0 fw-bold" id="resumeTotalProduk">
                            {{ method_exists($produk, 'total') ? $produk->total() : $produk->count() }}
                        </h3>
                    </div>
                </div>
            </div>
        </div>

        {{-- Filter & Tombol Tambah --}}
        <div class="col-12 col-md-8 col-xl-9">
            <div class="card h-100 shadow-sm">
                <div class="card-body d-flex align-items-center">
                    <div class="row g-3 align-items-center justify-content-start w-100 m-0">
                        <div class="col-md-4">
                            <input type="text" name="search" id="searchInput" class="form-control"
                                placeholder="Cari Nama Produk..." value="{{ request('search') }}">
                        </div>
                        <div class="col-md-4 me-3">
                            <select id="kategoriFilter" name="kategori" class="form-select select2"
                                data-placeholder="Semua Kategori">
                                <option value="">Semua Kategori</option>
                                @foreach ($kategoris as $kategori)
                                    <option value="{{ $kategori->id }}" @selected(request('kategori') == $kategori->id)>
                                        {{ $kategori->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-auto ms-md-auto">
                            <a href="{{ route('produk.create') }}" class="btn btn-outline-info mb-0">
                                <i class="bx bx-plus me- cursor-pointer"></i> Product
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12">
            <div class="card">
                <div class="card-header pb-0 px-3 pt-2 mb-3">
                    <h5 class="mb-n1 fw-bolder">Data Product</h5>
                    <p class="text-sm mb-0">
                        Kelola Data Productmu
                    </p>
                </div>
                <div class="card-body px-0 pt-0 pb-2">
                    {{-- Container untuk tabel yang akan di-refresh oleh AJAX --}}
                    <div id="produk-table-container">
                        @include('inventory::produk._produk_table', ['produk' => $produk])
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- modal-delete --}}
    <div class="modal fade" id="deleteConfirmationModal" tabindex="-1" aria-labelledby="deleteConfirmationModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body text-center mt-3 mx-n5">
                    <i class="bx bx-trash fa-2x text-danger mb-3"></i>
                    <p class="mb-0">apakah kamu yakin ingin menghapus produk ini?</p>
                    <h6 class="mt-2" id="productNameToDelete"></h6>
                    <div class="mt-4">
                        <form id="deleteProductForm" method="POST" class="d-inline" data-base-url="{{ url('produk') }}">
                            @method('delete')
                            @csrf
                            <button class="btn btn-danger btn-sm">Ya, Hapus</button>
                        </form>
                        <button type="button" class="btn btn-outline-secondary btn-sm ms-2"
                            data-bs-dismiss="modal">Batalkan</button>
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
            //scrollbar
            var win = navigator.platform.indexOf('Win') > -1;
            if (win && document.querySelector('#sidenav-scrollbar')) {
                var options = {
                    damping: '0.5'
                }
                Scrollbar.init(document.querySelector('#sidenav-scrollbar'), options);
            }

            // --- MODAL DELETE (AJAX) ---
            const deleteModal = document.getElementById('deleteConfirmationModal');
            const deleteForm = document.getElementById('deleteProductForm');
            let productRowToDelete = null;

            if (deleteModal && deleteForm) {
                deleteModal.addEventListener('show.bs.modal', function(event) {
                    const button = event.relatedTarget;
                    const productSlug = button.getAttribute('data-product-slug');
                    const productName = button.getAttribute('data-product-name');

                    productRowToDelete = button.closest('tr');

                    const modalBodyName = deleteModal.querySelector('#productNameToDelete');
                    modalBodyName.textContent = productName;

                    const baseUrl = deleteForm.getAttribute('data-base-url');
                    deleteForm.action = `${baseUrl}/${productSlug}`;
                });

                deleteForm.addEventListener('submit', function(e) {
                    e.preventDefault();

                    const url = this.action;
                    const token = this.querySelector('input[name="_token"]').value;
                    const submitBtn = this.querySelector('button');
                    const originalText = submitBtn.innerHTML;

                    submitBtn.disabled = true;
                    submitBtn.innerHTML = 'Menghapus...';

                    fetch(url, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': token,
                                'Accept': 'application/json'
                            }
                        })
                        .then(response => {
                            bootstrap.Modal.getInstance(deleteModal).hide();
                            return response.json();
                        })
                        .then(data => {
                            submitBtn.disabled = false;
                            submitBtn.innerHTML = originalText;

                            if (data.success) {
                                productRowToDelete.remove();
                                window.showToast('success', data.message);
                            } else {
                                window.showToast('error', data.message || 'Gagal menghapus produk.');
                            }
                        })
                        .catch(error => {
                            submitBtn.disabled = false;
                            submitBtn.innerHTML = originalText;
                            bootstrap.Modal.getInstance(deleteModal).hide();
                            window.showToast('error', 'Terjadi kesalahan jaringan.');
                        });
                });
            } // TUTUP BLOK IF DELETE MODAL

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
                        let kategori = $('#kategoriFilter').val();
                        let url = '{{ route('produk.index') }}';

                        $('#produk-table-container').css('opacity', 0.5); // Efek loading

                        $.ajax({
                            url: url,
                            type: 'GET',
                            data: {
                                search: search,
                                kategori: kategori,
                                page: page
                            },
                            success: function(data) {
                                $('#produk-table-container').html(data).css('opacity', 1);

                                // Update URL browser agar rapi (tanpa parameter kosong)
                                let newParams = new URLSearchParams();
                                if (page > 1) newParams.append('page', page);
                                if (search) newParams.append('search', search);
                                if (kategori) newParams.append('kategori', kategori);

                                let newUrl = url + (newParams.toString() ? '?' + newParams
                                    .toString() : '');
                                window.history.pushState({
                                    path: newUrl
                                }, '', newUrl);
                            },
                            error: function() {
                                $('#produk-table-container').css('opacity', 1);
                                alert('Gagal memuat data. Silakan coba lagi.');
                            }
                        });
                    }

                    // Event Listener Search (Ketik)
                    $('#searchInput').on('keyup', debounce(function() {
                        fetchData(1);
                    }, 500));

                    // Event Listener Filter Kategori (Select2)
                    $('#kategoriFilter').on('change', function() {
                        fetchData(1);
                    });

                    // Event Listener Pagination
                    $(document).on('click', '#produk-table-container .pagination a', function(e) {
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
