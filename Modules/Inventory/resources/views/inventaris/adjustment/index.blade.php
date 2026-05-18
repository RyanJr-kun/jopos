@extends('layouts/contentNavbarLayout')

@section('title', 'Cards basic - UI elements')
@section('content')

    <div class="row g-3 align-items-stretch">

        <div class="col-12 col-md-4 col-xl-3 mb-md-0">
            <div class="card h-100 border-0 shadow-sm" style="background: linear-gradient(135deg, #4d50eb 0%, #8592ff 100%);">
                <div class="card-body d-flex align-items-center">
                    <div class="avatar avatar-md me-3">
                        <span class="avatar-initial rounded bg-white text-primary shadow-sm">
                            <i class="bx bx-brand fs-4"></i>
                        </span>
                    </div>
                    <div>
                        <p class="text-white mb-0 text-sm">Total Brand</p>
                        <h3 class="text-white mb-0 fw-bold" id="resumeTotalBrand">
                            {{ method_exists($stocks, 'total') ? $stocks->total() : $stocks->count() }}
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
                                placeholder="Cari kategori..." value="{{ request('search') }}">
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
                            <a href="{{ route('stok-penyesuaian.create') }}" class="btn btn-outline-info mb-0">
                                <i class="bx bx-plus me-2"></i>
                                Adjust Stock</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12">
            <div class="card">
                <div class="card-header pb-0 px-3 pt-2 mb-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="me-2 ">
                            <h5 class="mb-n1 fw-bolder text-poppins">Informasi Stock</h5>
                            <p class="text-sm mb-0">Riwayat penyesuaian stock produk</p>
                        </div>
                        <div class="ms-md-auto mt-2">

                        </div>
                    </div>
                </div>
                <div class="card-body px-0 pt-0 pb-2">
                    <div class=" p-3">
                        <form action="{{ route('stok-penyesuaian.index') }}" method="GET">
                            <div class="row g-3 align-items-end">
                                <div class="col-md-4">
                                    <label for="search" class="form-label">Pencarian</label>
                                    <input type="text" id="search" name="search" class="form-control"
                                        placeholder="Cari kode atau user..." value="{{ request('search') }}">
                                </div>
                                <div class="col-md-3">
                                    <label for="start_date" class="form-label">Tanggal Mulai</label>
                                    <input type="date" id="start_date" name="start_date" class="form-control"
                                        value="{{ request('start_date') }}">
                                </div>
                                <div class="col-md-3">
                                    <label for="end_date" class="form-label">Tanggal Akhir</label>
                                    <input type="date" id="end_date" name="end_date" class="form-control"
                                        value="{{ request('end_date') }}">
                                </div>
                                <div class="col-md-2 d-flex mb-n3">
                                    <button type="submit" class="btn btn-dark me-2 w-100">Filter</button>
                                    <a href="{{ route('stok-penyesuaian.index') }}"
                                        class="btn btn-secondary w-100">Reset</a>
                                </div>
                            </div>
                        </form>
                    </div>

                    <div class="table-responsive p-0">
                        <table class="table table-hover align-items-center mb-0">
                            <thead class="table-secondary">
                                <tr>
                                    <th class="text-uppercase text-dark text-xs font-weight-bolder ps-4">Kode Penyesuaian
                                    </th>
                                    <th class="text-uppercase text-dark text-xs font-weight-bolder">Tanggal</th>
                                    <th class="text-uppercase text-dark text-xs font-weight-bolder">User</th>
                                    <th class="text-uppercase text-dark text-xs font-weight-bolder">Catatan</th>
                                    <th class="text-center text-uppercase text-dark text-xs font-weight-bolder">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($stocks as $penyesuaian)
                                    <tr>
                                        <td class="ps-4">
                                            <p class="text-sm font-weight-bold mb-0">{{ $penyesuaian->kode_penyesuaian }}
                                            </p>
                                        </td>
                                        <td>
                                            <p class="text-sm mb-0">
                                                {{ $penyesuaian->tanggal_penyesuaian->translatedFormat('d M Y, H:i') }}</p>
                                        </td>
                                        <td>
                                            <p class="text-sm mb-0">{{ $penyesuaian->user->username ?? 'N/A' }}</p>
                                        </td>
                                        <td>
                                            <p class="text-sm mb-0 text-truncate" style="max-width: 250px;">
                                                {{ $penyesuaian->catatan ?: '-' }}</p>
                                        </td>
                                        <td class="text-center">
                                            <a href="{{ route('stok-penyesuaian.show', $penyesuaian) }}"
                                                class="btn btn-link text-dark mb-0 pe-0" data-bs-toggle="tooltip"
                                                data-bs-placement="top" title="Lihat Detail">
                                                <i class="bx bx-eye-fill" aria-hidden="true"></i>
                                            </a>
                                            <button type="button" class="btn btn-link text-danger mb-0"
                                                data-bs-toggle="modal" data-bs-target="#cancelConfirmationModal"
                                                data-kode-penyesuaian="{{ $penyesuaian->kode_penyesuaian }}">
                                                <i class="bx bx-trash3-fill" aria-hidden="true"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4">
                                            <p class="text-sm fw-bold mb-0">Tidak ada riwayat penyesuaian stok ditemukan.
                                            </p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-center my-4">
                        {{ $stocks->links() }}
                    </div>
                </div>
            </div>
        </div>

        {{-- Modal Konfirmasi Pembatalan --}}
        <div class="modal fade" id="cancelConfirmationModal" tabindex="-1"
            aria-labelledby="cancelConfirmationModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-body text-center mt-3 mx-n5">
                        <i class="bx bx-exclamation-triangle-fill fa-3x text-warning mb-3"></i>
                        <h5 class="mb-2">Batalkan Penyesuaian?</h5>
                        <p class="mb-0">Anda yakin ingin membatalkan penyesuaian <br> <strong
                                id="kodePenyesuaianToCancel"></strong>?</p>
                        <small class="text-danger ">Tindakan ini akan mengembalikan stok produk <br> ke keadaan semula dan
                            tidak
                            dapat diurungkan.</small>
                        <div class="mt-4">
                            <form id="cancelForm" method="POST" action="#">
                                @method('DELETE')
                                @csrf
                                <button type="submit" class="btn btn-danger">Ya, Batalkan</button>
                                <button type="button" class="btn btn-outline-secondary ms-2"
                                    data-bs-dismiss="modal">Tutup</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    @endsection
    @section('page-script')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const cancelModal = document.getElementById('cancelConfirmationModal');
                if (cancelModal) {
                    cancelModal.addEventListener('show.bs.modal', function(event) {
                        const button = event.relatedTarget;
                        const kodePenyesuaian = button.getAttribute('data-kode-penyesuaian');
                        const form = cancelModal.querySelector('#cancelForm');
                        const text = cancelModal.querySelector('#kodePenyesuaianToCancel');

                        if (text) text.textContent = kodePenyesuaian;
                        if (form) form.action = `{{ url('stok-penyesuaian') }}/${kodePenyesuaian}`;
                    });
                }
            });
        </script>
    @endsection
