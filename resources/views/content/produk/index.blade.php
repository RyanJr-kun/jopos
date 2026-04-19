@extends('layouts/contentNavbarLayout')

@section('title', 'Cards basic - UI elements')
@section('content')

    <div class="card">
        <div class="card-header pb-0 px-3 pt-2 mb-3">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="mb-0">Data Product</h6>
                    <p class="text-sm mb-0">
                        Kelola Data Productmu
                    </p>
                </div>
                <div class="ms-auto mt-2">
                    <a href="{{ route('produk.create') }}">
                        <button class="btn btn-outline-info mb-0">
                            <i class="bx bx-plus-lg cursor-pointer pe-2"></i> Product
                        </button>
                    </a>
                </div>
            </div>
        </div>
        <div class="card-body px-0 pt-0 pb-2">
            <div class="row g-3 align-items-center justify-content-between">
                <div class="col-5 col-lg-3 ms-3">
                    <input type="text" id="searchInput" class="form-control" placeholder="Cari Product...">
                </div>
                <div class="col-5 col-lg-2 me-3">
                    <select id="kategoriFilter" name="kategori" class="form-select">
                        <option value="">Semua Kategori</option>
                        @foreach ($kategoris as $kategori)
                            <option value="{{ $kategori->id }}" @selected(request('kategori') == $kategori->id)>
                                {{ $kategori->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            {{-- Container untuk tabel yang akan di-refresh oleh AJAX --}}
            <div id="produk-table-container">
                @include('content.master._produk_table', ['produk' => $produk])
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

@section('page-script')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
                    let productRowToDelete = null; // Variabel untuk menyimpan baris tabel yang akan dihapus

                    if (deleteModal && deleteForm) {
                        deleteModal.addEventListener('show.bs.modal', function(event) {
                            const button = event.relatedTarget; // Tombol yang memicu modal
                            const productSlug = button.getAttribute('data-product-slug');
                            const productName = button.getAttribute('data-product-name');

                            // Simpan referensi ke baris <tr> untuk dihapus nanti
                            productRowToDelete = button.closest('tr');

                            // Isi konten modal
                            const modalBodyName = deleteModal.querySelector('#productNameToDelete');
                            modalBodyName.textContent = productName;

                            // Atur action form untuk URL fetch
                            const baseUrl = deleteForm.getAttribute('data-base-url');
                            deleteForm.action = `${baseUrl}/${productSlug}`;
                        });

                        deleteForm.addEventListener('submit', function(e) {
                                    e.preventDefault(); // Mencegah submit form tradisional

                                    const url = this.action;
                                    const token = this.querySelector('input[name="_token"]').value;

                                    fetch(url, {
                                            method: 'DELETE',
                                            headers: {
                                                'X-CSRF-TOKEN': token,
                                                'Accept': 'application/json' // Penting agar Laravel merespons dengan JSON
                                            }
                                        })
                                        .then(response => {
                                            bootstrap.Modal.getInstance(deleteModal).hide();
                                            return response.json();
                                        })
                                        .then(data => {
                                                if (data.success) {
                                                    // Hapus baris dari tabel tanpa reload
                                                    productRowToDelete.remove();
                                                    window.showToast('success', data.message);
                                                    else {
                                                        window.showToast('error', data.message);
                                                    })
                                                .catch(error => {
                                                    window.showToast('error', 'Terjadi kesalahan jaringan.');
                                                });
                                            }

                                            // --- AJAX FILTER & SEARCH ---
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
                                                        data: {
                                                            search: search,
                                                            kategori: kategori,
                                                            page: page
                                                        },
                                                        success: function(data) {
                                                            $('#produk-table-container').html(data).css(
                                                                'opacity', 1);
                                                            // Update URL di browser
                                                            let newUrl =
                                                                `${url}?page=${page}&search=${search}&kategori=${kategori}`;
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

                                                $('#searchInput').on('keyup', debounce(function() {
                                                    fetchData(1);
                                                }, 500));
                                                $('#kategoriFilter').on('change', function() {
                                                    fetchData(1);
                                                });
                                                $(document).on('click', '#produk-table-container .pagination a',
                                                    function(e) {
                                                        e.preventDefault();
                                                        let page = $(this).attr('href').split('page=')[1];
                                                        if (page) fetchData(page);
                                                    });
                                            });
                                        });
    </script>
@endsection
@endsection
