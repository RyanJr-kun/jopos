{{-- resources/views/content/finance/partials/cash-flow-offcanvas.blade.php --}}
{{-- Include sekali saja di layout/index pemasukan, pengeluaran, dan transfer --}}

<div class="offcanvas offcanvas-end" tabindex="-1" id="cashFlowOffcanvas" aria-labelledby="cashFlowOffcanvasLabel">
    <div class="offcanvas-header">
        <h5 id="cashFlowOffcanvasLabel">Cash Flow</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body" id="cashFlowOffcanvasBody">
        <div class="text-center text-muted py-5">Memuat...</div>
    </div>
</div>

<script>
    (function() {
        const offcanvasEl = document.getElementById('cashFlowOffcanvas');
        const bsOffcanvas = new bootstrap.Offcanvas(offcanvasEl);
        const body = document.getElementById('cashFlowOffcanvasBody');
        const title = document.getElementById('cashFlowOffcanvasLabel');
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

        async function loadForm(url, label) {
            title.textContent = label;
            body.innerHTML = '<div class="text-center text-muted py-5">Memuat...</div>';
            bsOffcanvas.show();

            const res = await fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    Accept: 'application/json'
                },
            });
            const data = await res.json();
            body.innerHTML = data.html;
            bindFormSubmit();
        }

        function bindFormSubmit() {
            const form = document.getElementById('cashFlowForm');
            if (!form) return;

            form.addEventListener('submit', async function(e) {
                e.preventDefault();
                const errorsBox = document.getElementById('cashFlowFormErrors');
                errorsBox.innerHTML = '';

                const formData = new FormData(form);
                const submitBtn = form.querySelector('button[type="submit"]');
                submitBtn.disabled = true;

                try {
                    const res = await fetch(form.action, {
                        method: 'POST', // Laravel baca _method=PUT dari form data, fetch tetap kirim POST untuk file upload
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            Accept: 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                        },
                        body: formData,
                    });

                    const data = await res.json();

                    if (res.status === 422) {
                        errorsBox.innerHTML = Object.values(data.errors).flat().join('<br>');
                        submitBtn.disabled = false;
                        return;
                    }

                    if (!res.ok) {
                        errorsBox.innerHTML = data.message ?? 'Terjadi kesalahan.';
                        submitBtn.disabled = false;
                        return;
                    }

                    bsOffcanvas.hide();
                    refreshTable();
                    // Ganti dengan toast kalau JOPOS sudah punya komponen notifikasi
                    alert(data.message);
                } catch (err) {
                    errorsBox.innerHTML = 'Gagal terhubung ke server.';
                    submitBtn.disabled = false;
                }
            });
        }

        async function refreshTable() {
            const tableArea = document.getElementById('cash-flow-table-area');
            if (!tableArea) return;

            const url = new URL(window.location.href);
            url.searchParams.set('fragment', 'table');

            const res = await fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    Accept: 'application/json'
                },
            });
            const data = await res.json();
            tableArea.outerHTML = data.html;
        }

        // Tombol "+ Tambah" — kasih atribut data-create-url berisi route create untuk type ini
        document.addEventListener('click', function(e) {
            const createBtn = e.target.closest('[data-cash-flow-create]');
            if (createBtn) {
                loadForm(createBtn.dataset.cashFlowCreate, createBtn.dataset.label ?? 'Tambah Transaksi');
            }

            // Tombol edit per baris — kasih atribut data-edit-url berisi route edit + referensi record
            const editBtn = e.target.closest('[data-cash-flow-edit]');
            if (editBtn) {
                loadForm(editBtn.dataset.cashFlowEdit, editBtn.dataset.label ?? 'Edit Transaksi');
            }
        });
    })();
</script>
