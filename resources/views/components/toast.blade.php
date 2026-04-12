{{-- Toast Notification --}}
<div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 1100;">
    {{-- Success Toast --}}
    <div id="successToast" class="bs-toast bg-success toast fade hide" role="alert" aria-live="assertive"
        aria-atomic="true">
        <div class="toast-header">
            <i class="icon-base bx bx-bell icon-xs me-2"></i>
            <span class="fw-medium me-auto">Berhasil</span>
            <small>Baru Saja!</small>
            <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body" id="successToastBody">
            {{ session('success') ?? '' }}
        </div>
    </div>

    {{-- Error Toast --}}
    <div id="errorToast" class="bs-toast bg-danger toast fade hide" role="alert" aria-live="assertive"
        aria-atomic="true">
        <div class="toast-header">
            <i class="icon-base bx bx-bell icon-xs me-2"></i>
            <span class="fw-medium me-auto">Gagal</span>
            <small>Baru Saja!</small>
            <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body" id="errorToastBody">
            {{ session('error') ?? '' }}
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        @if (session('success'))
            showToast('success', "{{ session('success') }}");
        @endif

        @if (session('error'))
            showToast('error', "{{ session('error') }}");
        @endif
    });

    // Fungsi global untuk menampilkan toast secara dinamis (via AJAX)
    window.showToast = function(type, message) {
        const toastId = type === 'success' ? 'successToast' : 'errorToast';
        const bodyId = type === 'success' ? 'successToastBody' : 'errorToastBody';
        
        const toastEl = document.getElementById(toastId);
        const bodyEl = document.getElementById(bodyId);
        
        if (toastEl && bodyEl) {
            bodyEl.textContent = message;
            const toast = new bootstrap.Toast(toastEl, {
                delay: 4000
            });
            toast.show();
        }
    };
</script>
