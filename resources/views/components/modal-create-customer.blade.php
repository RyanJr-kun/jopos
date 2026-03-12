@props(['id', 'action', 'title' => 'Buat Customer Baru', 'formId' => 'createCustomerForm'])

<div class="modal fade" id="{{ $id }}" tabindex="-1" aria-labelledby="{{ $id }}Label"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="{{ $id }}Label">{{ $title }}</h6>
                <button type="button" class="btn-close bg-danger" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                {{-- Form bisa di-handle oleh AJAX atau submit biasa --}}
                <form id="{{ $formId }}" action="{{ $action }}" method="post">
                    @csrf
                    <div class="mb-3">
                        <label for="{{ $id }}_name" class="form-label">Nama Customer</label>
                        <input id="{{ $id }}_name" name="name" type="text" class="form-control"
                            required>
                        <div class="invalid-feedback" id="{{ $id }}_name_error"></div>
                    </div>
                    <div class="mb-3">
                        <label for="{{ $id }}_kontak" class="form-label">Nomor Telepon</label>
                        <input id="{{ $id }}_kontak" name="kontak" type="text" class="form-control"
                            required>
                        <div class="invalid-feedback" id="{{ $id }}_kontak_error"></div>
                    </div>
                    <div class="mb-3">
                        <label for="{{ $id }}_email" class="form-label">Email (Opsional)</label>
                        <input id="{{ $id }}_email" name="email" type="email" class="form-control"
                            placeholder="example@gmail.com">
                        <div class="invalid-feedback" id="{{ $id }}_email_error"></div>
                    </div>
                    <div class="mb-3">
                        <label for="{{ $id }}_alamat" class="form-label">Alamat (Opsional)</label>
                        <textarea id="{{ $id }}_alamat" name="alamat" class="form-control" rows="2"></textarea>
                        <div class="invalid-feedback" id="{{ $id }}_alamat_error"></div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                {{-- Tombol ini akan men-submit form dengan ID yang sesuai --}}
                <button type="submit" form="{{ $formId }}" class="btn btn-primary">Simpan</button>
            </div>
        </div>
    </div>
</div>
