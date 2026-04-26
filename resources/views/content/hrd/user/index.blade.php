@extends('layouts/contentNavbarLayout')

@section('title', 'Cards basic - UI elements')
@section('content')

<div class="row g-3 align-items-stretch">
    <div class="col-12 col-md-4 col-xl-3 mb-md-0">
        <div class="card h-100 border-0 shadow-sm" style="background: linear-gradient(135deg, #4d50eb 0%, #8592ff 100%);">
            <div class="card-body d-flex align-items-center">
                <div class="avatar avatar-md me-3">
                    <span class="avatar-initial rounded bg-white text-primary shadow-sm">
                        <i class="bx bx-user fs-4"></i>
                    </span>
                </div>
                <div>
                    <p class="text-white mb-0 text-sm">Total Pengguna</p>
                    <h3 class="text-white mb-0 fw-bold" id="resumeTotaluser">
                        {{ method_exists($data, 'total') ? $data->total() : $data->count() }}
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
                        <label class="form-label">Pencarian</label>
                        <input type="text" name="search" id="searchInput" class="form-control"
                            placeholder="Cari Pengguna..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Filter Role</label>
                        <select name="role" class="form-select select2" data-placeholder="Semua Role">
                            <option value="">Semua Role</option>
                            @foreach ($roles as $roleName)
                                <option value="{{ $roleName }}" {{ request('role') == $roleName ? 'selected' : '' }}>
                                    {{ ucfirst($roleName) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Filter Status</label>
                        <select name="status" id="statusFilter" class="form-select select2"
                            data-placeholder="Semua Status">
                            <option value="">Semua Status</option>
                            <option value="Aktif" @selected(request('status') == 'Aktif')>Aktif</option>
                            <option value="Tidak Aktif" @selected(request('status') == 'Tidak Aktif')>Tidak Aktif</option>
                        </select>
                    </div>
                    <div class="col-md-auto ms-md-auto">
                        <a href="{{ route('users.create') }}" class="btn btn-outline-info mt-md-4 mb-0">
                        <i class="bx bx-plus me-2"></i>User
                    </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-12">
        <div class="card rounded-2">
            <div class="card-header pb-0 px-3 pt-2 mb-3">            
                <h5 class="mb-n1 fw-bolder">Data Pengguna</h5>
                <p class="text-sm mb-0">Kelola Data Penggunamu</p>                              
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-items-center justify-content-start mb-0" id="tableData">
                        <thead>
                            <tr>
                                <th width="5%">No</th>
                                <th>Nama</th>
                                <th>kontak</th>
                                <th>Posisi</th>
                                <th>status</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody id="isiTable">
                            @fragment('user-table-body')
                                @forelse ($data as $key => $user)
                                    <tr>
                                        <td>{{ ++$key }}</td>
                                        <td>
                                            <div title="image & Nama User" class="d-flex align-items-center px-2 py-1">
                                                @if ($user->avatar)
                                                    <img src="{{ asset('storage/' . $user->avatar) }}"
                                                        class="avatar avatar-sm me-3" alt="{{ $user->name }}">
                                                @else
                                                    <img src="{{ asset('assets/img/user.webp') }}" class="avatar avatar-sm me-3"
                                                        alt="Gambar produk default">
                                                @endif
                                                <h6 class="mb-0 text-sm">{{ $user->name }}</h6>
                                            </div>
                                        </td>
                                        <td class="d-flex flex-column">
                                            <small title="nomer whatsapp" class="text-dark mb-0">{{ $user->kontak }}
                                            </small>
                                            <span title="email" class="text-dark mb-0">{{ $user->email }}</span>
                                        </td>

                                        <td>
                                            @php
                                                $roleColors = [
                                                    'admin' => 'danger',
                                                    'kasir' => 'secondary',
                                                    'teknisi' => 'warning',
                                                    'staff' => 'info',
                                                    'sales' => 'primary',
                                                    'pelanggan' => 'success',
                                                ];
                                            @endphp
                                            @forelse($user->getRoleNames() as $role)
                                                @php $colorClass = $roleColors[strtolower($role)] ?? 'primary'; @endphp
                                                <p class="badge mb-1 bg-label-{{ $colorClass }} me-1">{{ $role }} </p>
                                            @empty
                                                <span class="text-muted small">Tanpa Role</span>
                                            @endforelse
                                        </td>
                                        <td class="align-middle text-center text-sm">
                                            @if ($user->status)
                                                <span class="badge bg-label-success">Aktif</span>
                                            @else
                                                <span class="badge bg-label-secondary">Tidak Aktif</span>
                                            @endif
                                        </td>

                                        <td class="align-middle">
                                            <a href="{{ route('users.edit', $user->username) }}"
                                                class="text-dark fw-bold px-3 text-xs" data-toggle="tooltip"
                                                data-original-title="Edit user">
                                                <i class="bx bx-edit text-dark text-sm opacity-10"></i>
                                            </a>
                                            <a href="#" class="text-dark delete-user-btn" data-bs-toggle="modal"
                                                data-bs-target="#deleteConfirmationModal"
                                                data-user-username="{{ $user->username }}"
                                                data-user-name="{{ $user->name }}" title="Hapus User">
                                                <i class="bx bx-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4">
                                            <span class="text-muted">Data pengguna tidak ditemukan.</span>
                                        </td>
                                    </tr>
                                @endforelse
                            @endfragment
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="deleteConfirmationModal" tabindex="-1" aria-labelledby="deleteConfirmationModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center mt-3 mx-n5">
                <i class="bx bx-trash fa-3x text-danger mb-3"></i>
                <p class="mb-0">Apakah Anda yakin ingin menghapus user ini?</p>
                <h6 class="mt-2" id="userNameToDelete"></h6>
                <div class="mt-4">
                    <form id="deleteUserForm" method="POST" action="#">
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
    
@endsection
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
        // 1. Script Modal Delete (Tetap dipertahankan)
        const deleteModal = document.getElementById('deleteConfirmationModal');
        if (deleteModal) {
            deleteModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const userUsername = button.getAttribute('data-user-username'); 
                const userName = button.getAttribute('data-user-name');

                const modalBodyName = deleteModal.querySelector('#userNameToDelete');
                const deleteForm = deleteModal.querySelector('#deleteUserForm');

                modalBodyName.textContent = userName;
                deleteForm.action = `/users/${userUsername}`; 
            });
        }

        // 2. Script AJAX Live Filter & Search
        const searchInput = $('#searchInput');
        const roleFilter = $('select[name="role"]');
        const statusFilter = $('#statusFilter');
        const tableBody = $('#isiTable');
        const totalUserText = $('#resumeTotaluser');
        
        let typingTimer;
        const doneTypingInterval = 500; // Delay 500ms agar server tidak berat saat user mengetik cepat

        function fetchUsers() {
            $.ajax({
                url: "{{ route('users.index') }}",
                type: "GET",
                data: {
                    search: searchInput.val(),
                    role: roleFilter.val(),
                    status: statusFilter.val()
                },
                headers: {
                    'X-Requested-With': 'XMLHttpRequest' // WAJIB untuk mendeteksi $request->ajax() di controller
                },
                beforeSend: function() {
                    // Animasi opacity saat data sedang diambil
                    tableBody.css('opacity', '0.4');
                },
                success: function(response) {
                    // Ganti isi tabel dan update total penggunanya secara live!
                    tableBody.html(response.html);
                    totalUserText.text(response.total);
                    tableBody.css('opacity', '1');
                },
                error: function(xhr) {
                    console.error("Gagal mengambil data AJAX", xhr);
                    tableBody.css('opacity', '1');
                }
            });
        }

        // Event Listener Pencarian (Menggunakan Debounce)
        searchInput.on('keyup', function() {
            clearTimeout(typingTimer);
            typingTimer = setTimeout(fetchUsers, doneTypingInterval);
        });

        // Event Listener Select2 (Role & Status)
        roleFilter.on('change', fetchUsers);
        statusFilter.on('change', fetchUsers);
    });
</script>
@endsection
