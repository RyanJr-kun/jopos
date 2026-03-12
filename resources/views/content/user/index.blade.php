@extends('layouts/contentNavbarLayout')

@section('title', 'Cards basic - UI elements')
@section('content')
    {{-- breadcrumb --}}
@section('breadcrumb')
    @php
        // Definisikan item breadcrumb dalam bentuk array
        $breadcrumbItems = [
            ['name' => 'Page', 'url' => '/dashboard'],
            ['name' => 'Manajemen User', 'url' => route('users.index')],
        ];
    @endphp

    {{-- Panggil component breadcrumb dan kirim datanya --}}
    <x-breadcrumb :items="$breadcrumbItems" />
@endsection

<div class="container-fluid p-3 ">
    <div class="card rounded-2">
        <div class="card-header d-flex pb-0">
            <div class="mb-3 mt-n2">
                <h5 class="mb-0">Data Pengguna</h5>
                <p class="text-sm mb-0">Kelola Data Penggunamu</p>
            </div>
            <div class="ms-auto mt-n2">
                <a href="{{ route('users.create') }}" class="btn btn-outline-info">
                    <i class="bx bx-plus-lg pe-2"></i>Buat User
                </a>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="row justify-content-between px-4 mb-3">
                <div class="col-md-3 col-6">
                    <input type="text" id="searchInput" class="form-control" placeholder="Cari pengguna...">
                </div>
                <div class="col-md-2 col-6">
                    <select id="posisiFilter" class="form-select">
                        <option value="">Semua Posisi</option>
                    </select>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-items-center justify-content-start mb-0" id="tableData">
                    <thead>
                        <tr class="table-secondary">
                            <th class="text-uppercase text-dark text-xs font-weight-bolder">Nama</th>
                            <th class="text-uppercase text-dark text-xs font-weight-bolder ps-2">kontak</th>
                            <th class="text-uppercase text-dark text-xs font-weight-bolder ps-2">Posisi</th>
                            <th class="text-uppercase text-dark text-xs font-weight-bolder text-center">Mulai Bekerja
                            </th>
                            <th class="text-uppercase text-dark text-xs font-weight-bolder text-center">status</th>
                            <th class="text-dark"></th>
                        </tr>
                    </thead>
                    <tbody id="isiTable">
                        @foreach ($users as $user)
                            <tr>
                                <td>
                                    <div title="image & Nama User" class="d-flex align-items-center px-2 py-1">
                                        @if ($user->img_user)
                                            <img src="{{ asset('storage/' . $user->img_user) }}"
                                                class="avatar avatar-sm me-3" alt="{{ $user->name }}">
                                        @else
                                            <img src="{{ asset('assets/img/user.webp') }}" class="avatar avatar-sm me-3"
                                                alt="Gambar produk default">
                                        @endif
                                        <h6 class="mb-0 text-sm">{{ $user->name }}</h6>
                                    </div>
                                </td>
                                <td>
                                    <p title="nomer whatsapp" class="text-xs text-dark fw-bold mb-0">{{ $user->kontak }}
                                    </p>
                                    <p title="email" class="text-xs text-dark mb-0">{{ $user->email }}</p>
                                </td>

                                <td>
                                    @if ($user->role->id == 1)
                                        <span class="badge badge-info" title="Role user">{{ $user->role->name }}</span>
                                    @else
                                        <span class="badge badge-warning"
                                            title="Role user">{{ $user->role->name }}</span>
                                    @endif
                                </td>

                                <td class="align-middle text-center">
                                    <span
                                        class="text-dark text-xs fw-bold">{{ $user->mulai_kerja?->translatedFormat('d M Y') }}</span>
                                </td>

                                <td class="align-middle text-center text-sm">
                                    @if ($user->status)
                                        <span class="badge badge-success">Aktif</span>
                                    @else
                                        <span class="badge badge-secondary">Tidak Aktif</span>
                                    @endif
                                </td>

                                <td class="align-middle">
                                    <a href="{{ route('users.edit', $user->username) }}"
                                        class="text-dark fw-bold px-3 text-xs" data-toggle="tooltip"
                                        data-original-title="Edit user">
                                        <i class="bx bx-pencil-square text-dark text-sm opacity-10"></i>
                                    </a>
                                    <a href="#" class="text-dark delete-user-btn" data-bs-toggle="modal"
                                        data-bs-target="#deleteConfirmationModal"
                                        data-user-username="{{ $user->username }}"
                                        data-user-name="{{ $user->name }}" title="Hapus User">
                                        <i class="bx bx-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="my-3 ms-3">{{ $users->onEachSide(1)->links() }}</div>
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
</div>
@section('page-script')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const deleteModal = document.getElementById('deleteConfirmationModal');
            if (deleteModal) {
                deleteModal.addEventListener('show.bs.modal', function(event) {
                    const button = event.relatedTarget;

                    // Ambil 'username' dari atribut data-*
                    const userUsername = button.getAttribute('data-user-username'); // <-- DIUBAH DI SINI
                    const userName = button.getAttribute('data-user-name');

                    const modalBodyName = deleteModal.querySelector('#userNameToDelete');
                    const deleteForm = deleteModal.querySelector('#deleteUserForm');

                    modalBodyName.textContent = userName;

                    // Atur action form menggunakan username
                    deleteForm.action = `/users/${userUsername}`; // <-- DIUBAH DI SINI
                });
            }

            const searchInput = document.getElementById('searchInput');
            const posisiFilter = document.getElementById('posisiFilter');
            const tableBody = document.getElementById('isiTable');
            const rows = tableBody.getElementsByTagName('tr');

            function populatePosisiFilter() {
                const posisiSet = new Set();
                for (let row of rows) {
                    // Kolom ke-3 (indeks 2) adalah Posisi
                    const posisiCell = row.getElementsByTagName('td')[2];
                    if (posisiCell) {
                        const posisiText = posisiCell.textContent.trim();
                        posisiSet.add(posisiText);
                    }
                }

                posisiSet.forEach(posisi => {
                    const option = document.createElement('option');
                    option.value = posisi;
                    option.textContent = posisi;
                    posisiFilter.appendChild(option);
                });
            }

            function filterTable() {
                const searchText = searchInput.value.toLowerCase();
                const posisiValue = posisiFilter.value;

                for (let i = 0; i < rows.length; i++) {
                    const row = rows[i];
                    // Kolom pertama (indeks 0) adalah Nama
                    const nameCell = row.getElementsByTagName('td')[0];
                    // Kolom ketiga (indeks 2) adalah Posisi
                    const posisiCell = row.getElementsByTagName('td')[2];

                    if (nameCell && posisiCell) {
                        // Mengambil teks dari dalam tag <h6>
                        const nameElement = nameCell.querySelector('h6'); // Nama ada di dalam h6

                        if (nameElement) {
                            const nameText = nameElement.textContent.toLowerCase();
                            const posisiText = posisiCell.textContent.trim(); // Posisi adalah text content dari sel

                            // Cek kondisi filter
                            const nameMatch = nameText.includes(searchText);
                            const posisiMatch = (posisiValue === "" || posisiText === posisiValue);

                            // Tampilkan atau sembunyikan baris
                            if (nameMatch && posisiMatch) {
                                row.style.display = "";
                            } else {
                                row.style.display = "none";
                            }
                        }
                    }
                }
            }

            // Panggil fungsi untuk mengisi dropdown saat halaman dimuat
            populatePosisiFilter();

            // Tambahkan event listener ke input pencarian dan dropdown
            searchInput.addEventListener('keyup', filterTable);
            posisiFilter.addEventListener('change', filterTable);


        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {

        });
    </script>
@endsection
@endsection
