{{-- resources/views/content/finance/_canvas.blade.php --}}
{{-- Include sekali saja di halaman pemasukan, pengeluaran, dan transfer --}}

<div class="offcanvas offcanvas-end" tabindex="-1" id="cashFlowOffcanvas" aria-labelledby="cashFlowOffcanvasLabel"
    style="width:420px">
    <div class="offcanvas-header border-bottom">
        <div class="d-flex align-items-center gap-2">
            <div id="cashFlowOffcanvasIcon" class="cf-canvas-type-dot"></div>
            <h5 id="cashFlowOffcanvasLabel" class="mb-0 fw-semibold fs-6">Cash Flow</h5>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body" id="cashFlowOffcanvasBody">
        <div class="cf-canvas-loading">
            <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
            <span class="ms-2 text-muted">Memuat formulir...</span>
        </div>
    </div>
</div>

{{-- Toast Container --}}
<div class="position-fixed top-0 end-0 p-3" style="z-index:9999" id="cfToastContainer">
    <div id="cfToast" class="toast align-items-center border-0" role="alert" aria-live="assertive"
        aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body d-flex align-items-center gap-2" id="cfToastBody"></div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"
                aria-label="Close"></button>
        </div>
    </div>
</div>

@push('page-script')
    {{--
    type="module" → otomatis defer (jalan setelah semua script dimuat).
    Kita ambil $ dan bootstrap dari window yang sudah di-set oleh vendor scripts Vite.
--}}
    <script type="module">
        /* global $, bootstrap */
        const $ = window.jQuery;
        const {
            Offcanvas,
            Toast
        } = window.bootstrap;

        $(function() {

            /* ── Inisialisasi Offcanvas ─────────────────────────────────── */
            const $offcanvasEl = $('#cashFlowOffcanvas');
            if (!$offcanvasEl.length) return;

            const bsOffcanvas = new Offcanvas($offcanvasEl[0]);
            const $body = $('#cashFlowOffcanvasBody');
            const $title = $('#cashFlowOffcanvasLabel');
            const csrfToken = $('meta[name="csrf-token"]').attr('content');

            /* ── Toast helper ───────────────────────────────────────────── */
            function showToast(message, type = 'success') {
                const $toast = $('#cfToast');
                const icon = type === 'success' ? 'bx-check-circle' : 'bx-x-circle';
                const bg = type === 'success' ? 'bg-success' : 'bg-danger';

                $toast
                    .attr('class', `toast align-items-center border-0 text-white ${bg}`)
                    .find('#cfToastBody')
                    .html(`<i class="bx ${icon} fs-5 me-2"></i>${message}`);

                Toast.getOrCreateInstance($toast[0], {
                    delay: 4000
                }).show();
            }

            /* ── Loading state ──────────────────────────────────────────── */
            function setBodyLoading() {
                $body.html(`
            <div class="cf-canvas-loading">
                <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
                <span class="ms-2 text-muted">Memuat formulir...</span>
            </div>`);
            }

            /**
             * Select2 di-load async oleh Vite wrapper.
             * Kita polling sampai $.fn.select2 tersedia sebelum init.
             */
            function waitForSelect2(callback, maxWait) {
                maxWait = maxWait || 5000;
                var interval = 50;
                var elapsed = 0;

                if ($.fn.select2) {
                    callback();
                    return;
                }

                var timer = setInterval(function() {
                    elapsed += interval;
                    if ($.fn.select2) {
                        clearInterval(timer);
                        callback();
                    } else if (elapsed >= maxWait) {
                        clearInterval(timer);
                        console.warn('Select2 tidak tersedia untuk form offcanvas.');
                    }
                }, interval);
            }

            /* ── Load form via $.ajax ───────────────────────────────────── */
            function loadForm(url, label) {
                $title.text(label);
                setBodyLoading();
                bsOffcanvas.show();

                $.ajax({
                    url,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        Accept: 'application/json'
                    },
                    success(data) {
                        $body.html(data.html).addClass('cf-form-fade-in');
                        setTimeout(() => $body.removeClass('cf-form-fade-in'), 400);

                        // URUTAN PENTING:
                        // 1. Init Select2 dulu (agar wrapping DOM selesai)
                        // 2. Init bank toggle (agar jQuery event listener dipasang ke Select2)
                        // 3. Bind form submit
                        waitForSelect2(function() {
                            initSelect2();
                            // Panggil toggle bank setelah Select2 terpasang
                            if (typeof window.initCfBankToggle === 'function') {
                                window.initCfBankToggle();
                            }
                        });
                        bindFormSubmit();
                    },
                    error() {
                        $body.html(`
                    <div class="text-center py-5 text-danger">
                        <i class="bx bx-wifi-off d-block fs-1 mb-2"></i>
                        <p class="mb-0">Gagal memuat formulir. Coba lagi.</p>
                    </div>`);
                    },
                });

            }

            function initSelect2() {
                if ($.fn.select2) {
                    $('#cashFlowOffcanvas .select2').each(function() {
                        const $this = $(this);
                        // Jika sudah diinisialisasi, skip
                        if ($this.hasClass('select2-hidden-accessible')) return;

                        $this.select2({
                            placeholder: $this.data('placeholder') || "— Pilih —",
                            allowClear: $this.find('option[value=""]').length > 0,
                            width: '100%',
                            // PENTING: Wajib agar dropdown tidak ngumpet di belakang Offcanvas
                            dropdownParent: $('#cashFlowOffcanvas')
                        });
                    });
                }
            }

            /* ── Bind form submit ───────────────────────────────────────── */
            function bindFormSubmit() {
                const $form = $('#cashFlowForm');
                if (!$form.length) return;

                $form.on('submit', function(e) {
                    e.preventDefault();

                    const $errors = $('#cashFlowFormErrors').empty();
                    const $submitBtn = $form.find('button[type="submit"]');
                    const origHTML = $submitBtn.html();

                    $submitBtn.prop('disabled', true).html(
                        `<span class="spinner-border spinner-border-sm me-1" role="status"></span>Menyimpan...`
                    );

                    $.ajax({
                        url: $form.attr('action'),
                        method: 'POST',
                        data: new FormData(this),
                        processData: false,
                        contentType: false,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            Accept: 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                        },
                        success(data) {
                            bsOffcanvas.hide();
                            showToast(data.message, 'success');
                            refreshTable();
                        },
                        error(xhr) {
                            $submitBtn.prop('disabled', false).html(origHTML);

                            if (xhr.status === 422) {
                                const msgs = Object.values(xhr.responseJSON?.errors ?? {}).flat();
                                $errors.html(
                                    msgs.map(m =>
                                        `<div class="cf-error-item"><i class="bx bx-error-circle"></i>${m}</div>`
                                    ).join('')
                                );
                            } else {
                                $errors.html(`
                            <div class="cf-error-item">
                                <i class="bx bx-error-circle"></i>
                                ${xhr.responseJSON?.message ?? 'Terjadi kesalahan.'}
                            </div>`);
                            }
                        },
                    });
                });
            }

            /* ── Refresh tabel via AJAX ─────────────────────────────────── */
            function refreshTable() {
                const $tableArea = $('#cash-flow-table-area');
                if (!$tableArea.length) return;

                const url = new URL(window.location.href);
                url.searchParams.set('fragment', 'table');

                $.ajax({
                    url: url.toString(),
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        Accept: 'application/json'
                    },
                    success(data) {
                        $tableArea.replaceWith(data.html);
                    },
                    error() {
                        window.location.reload();
                    },
                });
            }

            /* ── Event delegation ───────────────────────────────────────── */
            $(document).on('click', '[data-cash-flow-create]', function() {
                loadForm(
                    $(this).data('cash-flow-create'),
                    $(this).data('label') || 'Tambah Transaksi'
                );
            });

            $(document).on('click', '[data-cash-flow-edit]', function() {
                loadForm(
                    $(this).data('cash-flow-edit'),
                    $(this).data('label') || 'Edit Transaksi'
                );
            });

        }); // end $(function)
    </script>
@endpush

