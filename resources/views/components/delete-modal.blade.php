<div class="modal fade" id="{{ $modalId }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center mt-3">
                <i class="bx bx-trash icon-xl text-danger mb-3"></i>
                <p class="mb-0">{{ $message }}</p>
                <h6 class="mt-2" id="{{ $itemTitleId }}"></h6>
                <div class="mt-4">
                    <button type="button" id="{{ $confirmBtnId }}" class="btn btn-danger btn-sm">
                        Ya, Hapus
                    </button>
                    <button type="button" class="btn btn-outline-secondary btn-sm ms-2"
                        data-bs-dismiss="modal">Batal</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('{{ $modalId }}').addEventListener('show.bs.modal', function(event) {
        const trigger = event.relatedTarget;
        if (trigger) {
            document.getElementById('{{ $itemTitleId }}').textContent =
                trigger.getAttribute('data-title') || '';
        }
    });
</script>
