@extends('layouts/contentNavbarLayout')

@section('title', 'Cards basic - UI elements')
@section('content')
<div class="row g-3 align-items-stretch">
    <div class="col-12 col-md-4 col-xl-3 mb-md-0">
            <div class="card h-100 border-0 shadow-sm" style="background: linear-gradient(135deg, #4d50eb 0%, #8592ff 100%);">
                <div class="card-body d-flex align-items-center">
                    <div class="avatar avatar-md me-3">
                        <span class="avatar-initial rounded bg-white text-primary shadow-sm">
                            <i class="bx bx-percent fs-4"></i>
                        </span>
                    </div>
                    <div>
                        <p class="text-white mb-0 text-sm">Total Promo</p>
                        <h3 class="text-white mb-0 fw-bold" id="resumeTotalPromo">
                            {{ method_exists($promotions, 'total') ? $promotions->total() : $promotions->count() }}
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
                                placeholder="Cari promo..." value="{{ request('search') }}">
                        </div>
                        <div class="col-md-4 me-3">
                            <select name="status" id="statusFilter" class="form-select select2"
                                data-placeholder="Semua Status">
                                <option value="">Semua Status</option>
                                <option value="Aktif" @selected(request('status') == 'Aktif')>Aktif</option>
                                <option value="Tidak Aktif" @selected(request('status') == 'Tidak Aktif')>Tidak Aktif</option>
                            </select>
                        </div>
                        <div class="col-md-auto ms-md-auto">
                            <a href="{{ route('promo.create') }}" class="btn btn-outline-info mb-0">
                            <i class="bx bx-plus me-2"></i>Promotion
                        </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <div class="col-12">
        <div class="card rounded-2">
            <div class="card-header pb-0 px-3 pt-2">
                <h5 class="mb-n1 fw-bolder">Daftar Promotion & Diskon</h5>
                <p class="text-sm mb-0">Kelola semua promotionsi dan diskon <br class="d-sm-none"> Anda di sini.</p>
            </div>
            <div class="card-body px-0 pt-0 pb-2">
                
                <div id="promo-table-container">
                    @include('content.promo._promo_table')
                </div>
            </div>
        </div>
    </div>
</div>

    {{-- Modal Delete Confirmation --}}
    <div class="modal fade" id="deleteConfirmationModal" tabindex="-1" aria-labelledby="deleteConfirmationModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body text-center mt-3 mx-n5">
                    <i class="bx bx-trash fa-2x text-danger mb-3"></i>
                    <p class="mb-0">Apakah Anda yakin ingin menghapus promo ini?</p>
                    <h6 class="mt-2" id="promoNameToDelete"></h6>
                    <div class="mt-4">
                        <form id="deletePromotionForm" method="POST" action="#">
                            @method('delete')
                            @csrf
                            <button type="submit" class="btn btn-danger btn-sm">Ya, Hapus</button>
                            <button type="button" class="btn btn-outline-secondary btn-sm ms-2"
                                data-bs-dismiss="modal">Batal</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    

@section('page-script')
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
            // --- MODAL DELETE ---
            const deleteModal = document.getElementById('deleteConfirmationModal');
            if (deleteModal) {
                deleteModal.addEventListener('show.bs.modal', function(event) {
                    const button = event.relatedTarget;
                    const promoId = button.getAttribute('data-promo-id');
                    const promoName = button.getAttribute('data-promo-name');
                    const modalBodyName = deleteModal.querySelector('#promoNameToDelete');
                    const deleteForm = deleteModal.querySelector('#deletePromotionForm');

                    modalBodyName.textContent = promoName;
                    deleteForm.action = `{{ url('promo') }}/${promoId}`;
                });
            }

            // --- AJAX FILTER & SEARCH ---
            function debounce(func, delay) {
                let timeout;
                return function(...args) {
                    clearTimeout(timeout);
                    timeout = setTimeout(() => func.apply(this, args), delay);
                };
            }

            function fetchData(page = 1) {
                let search = $('#searchInput').val();
                let status = $('#statusFilter').val();
                let url = '{{ route('promo.index') }}';

                $('#promo-table-container').css('opacity', 0.5);

                $.ajax({
                    url: url,
                    data: {
                        search: search,
                        status: status,
                        page: page
                    },
                    success: function(data) {
                        $('#promo-table-container').html(data).css('opacity', 1);
                        window.history.pushState({
                                path: url + '?page=' + page + '&search=' + search + '&status=' +
                                    status
                            }, '', url + '?page=' + page + '&search=' + search + '&status=' +
                            status);
                    },
                    error: function() {
                        $('#promo-table-container').css('opacity', 1);
                        window.showToast('error', 'Gagal memuat data. Silakan coba lagi.');
                    }
                });
            }

            $('#searchInput').on('keyup', debounce(function() {
                fetchData(1);
            }, 500));

            $('#statusFilter').on('change', function() {
                fetchData(1);
            });

            $(document).on('click', '#promo-table-container .pagination a', function(e) {
                e.preventDefault();
                let page = $(this).attr('href').split('page=')[1];
                if (page) {
                    fetchData(page);
                }
            });
            // --- PROMO COUNTDOWN LOGIC ---
            let countdownInterval;

            function initializeCountdowns() {
                // Hentikan interval sebelumnya jika ada
                if (countdownInterval) {
                    clearInterval(countdownInterval);
                }

                const countdownElements = document.querySelectorAll('[id^="countdown-"]');
                if (countdownElements.length === 0) return;

                function updateAllCountdowns() {
                    countdownElements.forEach(el => {
                        const endTime = new Date(el.dataset.endTime).getTime();
                        const promoId = el.dataset.promoId;
                        const now = new Date().getTime();
                        const distance = endTime - now;

                        const statusContainer = document.getElementById(`status-container-${promoId}`);
                        const statusBadge = statusContainer ? statusContainer.querySelector('.badge') :
                            null;

                        if (distance > 0) {
                            const days = Math.floor(distance / (1000 * 60 * 60 * 24));
                            const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                            const seconds = Math.floor((distance % (1000 * 60)) / 1000);

                            el.innerHTML = `${days}h ${hours}j ${minutes}m ${seconds}d`;

                            // Pastikan statusnya 'Aktif' jika masih berjalan
                            if (statusBadge && statusBadge.textContent.trim() === 'Tidak Aktif') {
                                statusBadge.className = 'badge bg-label-success';
                                statusBadge.textContent = 'Aktif';
                            }

                        } else {
                            el.innerHTML = `<span class="text-danger">Berakhir</span>`;
                            // Jika status masih 'Aktif', ubah dan panggil AJAX
                            if (statusBadge && statusBadge.textContent.trim() === 'Aktif') {
                                statusBadge.className = 'badge bg-label-secondary';
                                statusBadge.textContent = 'Tidak Aktif';
                                updatePromotionStatus(promoId);
                            }
                        }
                    });
                }

                countdownInterval = setInterval(updateAllCountdowns, 1000);
                updateAllCountdowns(); // Panggil sekali saat inisialisasi
            }

            async function updatePromotionStatus(promoId) {
                try {
                    const response = await fetch(`/promo/${promoId}/update-status`, {
                        method: 'PATCH',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        }
                    });
                    const result = await response.json();
                    if (!result.success) {
                        console.error(`Gagal update status promo ${promoId}:`, result.message);
                    }
                } catch (error) {
                    console.error('Error saat update status promo:', error);
                }
            }

            // Inisialisasi countdown saat halaman dimuat
            initializeCountdowns();

            // Inisialisasi ulang setelah AJAX selesai
            $(document).ajaxComplete(function() {
                initializeCountdowns();
            });
        });
    </script>
@endsection
@endsection
