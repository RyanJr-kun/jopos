@extends('layouts/contentNavbarLayout')

@section('title', 'Cards basic - UI elements')
@section('content')
    <div class="container-fluid p-3">
        <div class="card rounded-2">
            <div class="card-header pb-0 px-3 pt-2 mb-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-n1">Riwayat Stock Opname</h6>
                        <p class="text-sm mb-0">Daftar semua sesi stok opname yang telah selesai.</p>
                    </div>
                    <div class="ms-md-auto mt-2">
                        <a href="{{ route('stok-opname.index') }}" class="btn btn-outline-info mb-0">
                            <i class="bx bx-plus-lg me-2"></i>Buat Opname Baru
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body px-0 pt-0 pb-2">
                <div class=" p-3">
                    <form action="{{ route('stok-opname.history') }}" method="GET">
                        <div class="row g-3 align-items-center">
                            <div class="col-md-4">
                                <input type="text" name="search" class="form-control"
                                    placeholder="Cari kode opname atau user..." value="{{ request('search') }}">
                            </div>
                            <div class="col-md-2 mb-n3">
                                <button type="submit" class="btn btn-dark w-100">Cari</button>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="table-responsive p-0">
                    <table class="table table-hover align-items-center mb-0">
                        <thead class="table-secondary">
                            <tr>
                                <th class="text-uppercase text-dark text-xs font-weight-bolder ps-4">Kode Opname</th>
                                <th class="text-uppercase text-dark text-xs font-weight-bolder">Tanggal</th>
                                <th class="text-uppercase text-dark text-xs font-weight-bolder">User</th>
                                <th class="text-uppercase text-dark text-xs font-weight-bolder">Catatan</th>
                                <th class="text-center text-uppercase text-dark text-xs font-weight-bolder">Status</th>
                                <th class="text-center text-uppercase text-dark text-xs font-weight-bolder">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($stokOpnames as $opname)
                                <tr>
                                    <td class="ps-4">
                                        <p class="text-sm font-weight-bold mb-0">{{ $opname->kode_opname }}</p>
                                    </td>
                                    <td>
                                        <p class="text-sm mb-0">
                                            {{ $opname->tanggal_opname->translatedFormat('d M Y, H:i') }}</p>
                                    </td>
                                    <td>
                                        <p class="text-sm mb-0">{{ $opname->user->username ?? 'N/A' }}</p>
                                    </td>
                                    <td>
                                        <p class="text-sm mb-0 text-truncate" style="max-width: 250px;">
                                            {{ $opname->catatan ?: '-' }}</p>
                                    </td>
                                    <td class="text-center"><span
                                            class="badge badge-sm bg-label-success">{{ $opname->status }}</span></td>
                                    <td class="text-center">
                                        <a href="{{ route('stok-opname.show', $opname->kode_opname) }}"
                                            class="btn btn-link text-dark px-3 mb-0" data-bs-toggle="tooltip"
                                            data-bs-placement="top" title="Lihat Detail">
                                            <i class="bx bx-show" aria-hidden="true"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4">
                                        <p class="text-sm fw-bold mb-0">Tidak ada riwayat stok opname ditemukan.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-center my-4">
                    {{ $stokOpnames->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
