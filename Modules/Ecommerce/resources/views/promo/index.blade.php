@extends('layouts/contentNavbarLayout')

@section('title', 'Manajemen Promo & Diskon')

@section('content')

    {{-- Stat Cards --}}
    <div class="row g-3 mb-4">
        {{-- Total Promo --}}
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #4d50eb 0%, #8592ff 100%);">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="avatar avatar-md flex-shrink-0">
                        <span class="avatar-initial rounded-circle bg-white" style="opacity:.95;">
                            <i class="bx bx-tag text-primary fs-5"></i>
                        </span>
                    </div>
                    <div>
                        <p class="text-white mb-0" style="font-size:.78rem; opacity:.85;">Total Promo</p>
                        <h4 class="text-white mb-0 fw-bold lh-1 mt-1">
                            {{ method_exists($promotions, 'total') ? $promotions->total() : $promotions->count() }}
                        </h4>
                    </div>
                </div>
            </div>
        </div>

        {{-- Promo Aktif --}}
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100"
                style="background: linear-gradient(135deg, #28a745 0%, #5cb85c 100%);">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="avatar avatar-md flex-shrink-0">
                        <span class="avatar-initial rounded-circle bg-white" style="opacity:.95;">
                            <i class="bx bx-check-circle text-success fs-5"></i>
                        </span>
                    </div>
                    <div>
                        <p class="text-white mb-0" style="font-size:.78rem; opacity:.85;">Promo Aktif</p>
                        <h4 class="text-white mb-0 fw-bold lh-1 mt-1">
                            {{ \Modules\Ecommerce\Models\Promotion::where('status', true)->count() }}
                        </h4>
                    </div>
                </div>
            </div>
        </div>

        {{-- Promo Persentase --}}
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100"
                style="background: linear-gradient(135deg, #fd7e14 0%, #ffc107 100%);">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="avatar avatar-md flex-shrink-0">
                        <span class="avatar-initial rounded-circle bg-white" style="opacity:.95;">
                            <i class="bx bx-percent text-warning fs-5"></i>
                        </span>
                    </div>
                    <div>
                        <p class="text-white mb-0" style="font-size:.78rem; opacity:.85;">Tipe Persentase</p>
                        <h4 class="text-white mb-0 fw-bold lh-1 mt-1">
                            {{ \Modules\Ecommerce\Models\Promotion::where('type', 'percentage')->count() }}
                        </h4>
                    </div>
                </div>
            </div>
        </div>

        {{-- Promo Fixed --}}
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100"
                style="background: linear-gradient(135deg, #dc3545 0%, #e83e8c 100%);">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="avatar avatar-md flex-shrink-0">
                        <span class="avatar-initial rounded-circle bg-white" style="opacity:.95;">
                            <i class="bx bx-money text-danger fs-5"></i>
                        </span>
                    </div>
                    <div>
                        <p class="text-white mb-0" style="font-size:.78rem; opacity:.85;">Tipe Nominal</p>
                        <h4 class="text-white mb-0 fw-bold lh-1 mt-1">
                            {{ \Modules\Ecommerce\Models\Promotion::where('type', 'fixed')->count() }}
                        </h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Card --}}
    <div class="card shadow-sm border-0">
        {{-- Card Header --}}
        <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2 py-3">
            <div>
                <h5 class="mb-0 fw-bold">Daftar Promo & Diskon</h5>
                <p class="text-muted text-sm mb-0">Kelola semua promosi dan diskon di sini.</p>
            </div>
            {{-- Filter & Search --}}
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <div class="input-group input-group-sm" style="width:220px;">
                    <span class="input-group-text bg-transparent border-end-0">
                        <i class="bx bx-search text-muted"></i>
                    </span>
                    <input type="text" id="searchInput" class="form-control border-start-0 ps-0"
                        placeholder="Cari nama / kode..." value="{{ request('search') }}">
                </div>
                <select id="statusFilter" class="form-select form-select-sm" style="width:150px;">
                    <option value="">Semua Status</option>
                    <option value="1" @selected(request('status') == '1')>Aktif</option>
                    <option value="0" @selected(request('status') === '0')>Tidak Aktif</option>
                </select>
                @can('create-promo')
                    <a href="{{ route('promo.create') }}" class="btn btn-primary px-2 d-flex align-items-center gap-2 shadow-sm"
                        title="Tambah Promo" data-bs-toggle="tooltip" data-bs-placement="top">
                        <i class="bx bx-plus-circle"></i>
                    </a>
                @endcan
            </div>
        </div>

        {{-- Table --}}
        <div class="card-body p-0">
            <div id="promo-table-container">
                @include('ecommerce::promo._promo_table')
            </div>
        </div>
    </div>

    {{-- Modal Delete Confirmation --}}
    <div class="modal fade" id="deleteConfirmationModal" tabindex="-1" aria-labelledby="deleteConfirmationModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content border-0 shadow">
                <div class="modal-body text-center py-4 px-3">
                    <div class="mb-3">
                        <span class="avatar avatar-lg bg-label-danger rounded-circle">
                            <i class="bx bx-trash fs-3 text-danger"></i>
                        </span>
                    </div>
                    <h6 class="fw-bold mb-1">Hapus Promo?</h6>
                    <p class="text-muted text-sm mb-1">Tindakan ini tidak dapat dibatalkan.</p>
                    <p class="fw-semibold text-dark mb-3" id="promoNameToDelete"></p>
                    <form id="deletePromotionForm" method="POST" action="#">
                        @method('delete')
                        @csrf
                        <div class="d-flex gap-2 justify-content-center">
                            <button type="button" class="btn btn-outline-secondary btn-sm px-3"
                                data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-danger btn-sm px-3">
                                <i class="bx bx-trash me-1"></i>Ya, Hapus
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('page-script')
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            // --- MODAL DELETE ---
            const deleteModal = document.getElementById('deleteConfirmationModal');
            if (deleteModal) {
                deleteModal.addEventListener('show.bs.modal', function(event) {
                    const btn = event.relatedTarget;
                    document.getElementById('promoNameToDelete').textContent = btn.dataset.promoName;
                    document.getElementById('deletePromotionForm').action =
                        `{{ url('promo') }}/${btn.dataset.promoId}`;
                });
            }

            // --- DEBOUNCE ---
            function debounce(fn, delay) {
                let t;
                return function(...args) {
                    clearTimeout(t);
                    t = setTimeout(() => fn.apply(this, args), delay);
                };
            }

            // --- FETCH / AJAX FILTER ---
            function fetchData(page = 1) {
                const search = document.getElementById('searchInput').value;
                const status = document.getElementById('statusFilter').value;
                const url = '{{ route('promo.index') }}';
                const container = document.getElementById('promo-table-container');

                container.style.opacity = '0.5';
                container.style.pointerEvents = 'none';

                $.ajax({
                    url,
                    data: {
                        search,
                        status,
                        page
                    },
                    success(data) {
                        container.innerHTML = data;
                        container.style.opacity = '1';
                        container.style.pointerEvents = '';
                        initializeCountdowns();
                        window.history.replaceState({},
                            '',
                            `${url}?page=${page}&search=${encodeURIComponent(search)}&status=${status}`
                        );
                    },
                    error() {
                        container.style.opacity = '1';
                        container.style.pointerEvents = '';
                        if (window.showToast) window.showToast('error', 'Gagal memuat data.');
                    }
                });
            }

            document.getElementById('searchInput').addEventListener('keyup', debounce(() => fetchData(1), 500));
            document.getElementById('statusFilter').addEventListener('change', () => fetchData(1));

            $(document).on('click', '#promo-table-container .pagination a', function(e) {
                e.preventDefault();
                const href = $(this).attr('href');
                const match = href.match(/page=(\d+)/);
                if (match) fetchData(match[1]);
            });

            // --- COUNTDOWN LOGIC ---
            let countdownInterval;

            function initializeCountdowns() {
                if (countdownInterval) clearInterval(countdownInterval);
                const els = document.querySelectorAll('[id^="countdown-"]');
                if (!els.length) return;

                function updateAll() {
                    els.forEach(el => {
                        const endTime = new Date(el.dataset.endTime).getTime();
                        const promoId = el.dataset.promoId;
                        const distance = endTime - Date.now();
                        const statusContainer = document.getElementById(`status-container-${promoId}`);
                        const badge = statusContainer?.querySelector('.badge');

                        if (distance > 0) {
                            const d = Math.floor(distance / 86400000);
                            const h = Math.floor((distance % 86400000) / 3600000);
                            const m = Math.floor((distance % 3600000) / 60000);
                            const s = Math.floor((distance % 60000) / 1000);

                            let html = '';
                            if (d > 0) html +=
                                `<span class="fw-semibold text-primary">${d}</span><small class="text-muted">h </small>`;
                            html +=
                                `<span class="fw-semibold text-primary">${String(h).padStart(2,'0')}</span><small class="text-muted">j </small>`;
                            html +=
                                `<span class="fw-semibold">${String(m).padStart(2,'0')}</span><small class="text-muted">m </small>`;
                            html +=
                                `<span class="fw-semibold">${String(s).padStart(2,'0')}</span><small class="text-muted">d</small>`;
                            el.innerHTML = html;

                            if (badge && badge.textContent.trim().includes('Nonaktif')) {
                                badge.className = 'badge bg-label-success rounded-pill px-2';
                                badge.innerHTML = '<i class="bx bx-check-circle me-1"></i>Aktif';
                            }
                        } else {
                            el.innerHTML =
                                `<span class="badge bg-label-danger rounded-pill"><i class="bx bx-x-circle me-1"></i>Berakhir</span>`;
                            if (badge && !badge.textContent.trim().includes('Nonaktif') && !badge
                                .textContent.trim().includes('Berakhir')) {
                                badge.className = 'badge bg-label-secondary rounded-pill px-2';
                                badge.innerHTML = '<i class="bx bx-x-circle me-1"></i>Nonaktif';
                                updatePromotionStatus(promoId);
                            }
                        }
                    });
                }

                countdownInterval = setInterval(updateAll, 1000);
                updateAll();
            }

            async function updatePromotionStatus(promoId) {
                try {
                    const res = await fetch(`/promo/${promoId}/update-status`, {
                        method: 'PATCH',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        }
                    });
                    const result = await res.json();
                    if (!result.success) console.warn(`Update status promo ${promoId}:`, result.message);
                } catch (e) {
                    console.error('Error update status promo:', e);
                }
            }

            initializeCountdowns();
        });
    </script>
@endsection
