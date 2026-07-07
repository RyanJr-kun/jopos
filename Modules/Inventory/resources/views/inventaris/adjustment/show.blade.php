@extends('layouts/contentNavbarLayout')
@section('title', 'Detail Penyesuaian Stok - Inventory')

@section('content')

    <div class="card rounded-2 mb-4">
        <div class="card-header pb-2 mb-3">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="mb-0 fw-bold text-uppercase">Detail Penyesuaian Stok</h6>
                    <p class="text-sm mb-0">Rincian penyesuaian untuk
                        <span class="fw-bold">{{ $penyesuaian->kode_penyesuaian }}</span>
                    </p>
                </div>
                <div class="ms-md-auto">
                    <a href="{{ route('stok-penyesuaian.index') }}" class="btn btn-outline-secondary px-2"
                        title="Kembali ke Daftar Penyesuaian Stok" data-bs-toggle="tooltip" data-bs-placement="top">
                        <i class="bx bx-arrow-back"></i>
                    </a>
                </div>
            </div>
        </div>
        <div class="card-body pt-0">
            <div class="row">
                <div class="col-md-4">
                    <p class="text-sm mb-1"><strong class="text-dark">Toko &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
                            &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; :</strong>
                        {{ $penyesuaian->store->name_toko ?? 'N/A' }}</p>
                    <p class="text-sm mb-1"><strong class="text-dark">Dilakukan oleh &nbsp; &nbsp;:</strong>
                        {{ $penyesuaian->user->username ?? 'N/A' }}</p>
                </div>
                <div class="col-md-3">
                    <p class="text-sm mb-1"><strong class="text-dark">Tanggal:</strong></p>
                    <p class="text-sm">{{ $penyesuaian->tanggal_penyesuaian->translatedFormat('l, d F Y H:i') }}</p>
                </div>
                <div class="col-md-5">
                    <p class="text-sm mb-1"><strong class="text-dark">Catatan Umum:</strong></p>
                    <p class="text-sm">{{ $penyesuaian->catatan ?: '-' }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="card rounded-2">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h6 class="mb-0 fw-bold text-uppercase">Item yang Disesuaikan</h6>
            @can('delete-stok-penyesuaian')
                <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal"
                    data-bs-target="#cancelConfirmationModal">
                    <i class="bx bx-undo fs-5 me-2"></i> Batalkan Penyesuaian
                </button>
            @endcan
        </div>
        <div class="card-body px-0">
            <div class="table-responsive p-0">
                <table class="table table-hover align-items-center mb-0">
                    <thead class="table-secondary">
                        <tr>
                            <th class="text-uppercase text-dark text-xs font-weight-bolder ps-4">Produk</th>
                            <th class="text-center text-uppercase text-dark text-xs font-weight-bolder">Kategori</th>
                            <th class="text-center text-uppercase text-dark text-xs font-weight-bolder">Arah</th>
                            <th class="text-center text-uppercase text-dark text-xs font-weight-bolder">Jumlah</th>
                            <th class="text-center text-uppercase text-dark text-xs font-weight-bolder">Stok Sebelum</th>
                            <th class="text-center text-uppercase text-dark text-xs font-weight-bolder">Stok Setelah</th>
                            <th class="text-uppercase text-dark text-xs font-weight-bolder">Alasan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($penyesuaian->details as $detail)
                            <tr>
                                <td>
                                    <div class="d-flex px-2 py-1">
                                        <div>
                                            <img src="{{ $detail->produk->img_produk ? Storage::url($detail->produk->img_produk) : asset('assets/img/produk.png') }}"
                                                class="avatar avatar-sm me-3" alt="product image">
                                        </div>
                                        <div class="d-flex flex-column justify-content-center">
                                            <h6 class="mb-0 text-sm">
                                                {{ $detail->produk->name_product ?? 'Produk Dihapus' }}
                                                @if ($detail->variant)
                                                    <span class="text-xs text-secondary">-
                                                        {{ $detail->variant->name ?? '' }}</span>
                                                @endif
                                            </h6>
                                            <p class="text-xs text-secondary mb-0">{{ $detail->produk->sku ?? 'N/A' }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="align-middle text-center text-sm">
                                    {{ \Modules\Inventory\Http\Controllers\stok\StockAdjustmentController::TYPES[$detail->type] ?? $detail->type }}
                                </td>
                                <td class="align-middle text-center text-sm">
                                    {!! $detail->arah_formatted !!}
                                </td>
                                <td class="align-middle text-center text-sm">
                                    {!! $detail->jumlah_formatted !!}
                                </td>
                                <td class="align-middle text-center text-sm">
                                    <span class="fw-bold">{{ $detail->stok_sebelum }}</span>
                                </td>
                                <td class="align-middle text-center text-sm">
                                    <span class="fw-bold">{{ $detail->stok_setelah }}</span>
                                </td>
                                <td class="align-middle text-sm">{{ $detail->alasan ?: '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4">Tidak ada detail item untuk penyesuaian ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>


    {{-- Modal Konfirmasi Pembatalan --}}
    @can('delete-stok-penyesuaian')
        <div class="modal fade" id="cancelConfirmationModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-body text-center mt-3 mx-n5">
                        <i class="bx bx-exclamation-triangle-fill fa-3x text-warning mb-3"></i>
                        <h5 class="mb-2">Batalkan Penyesuaian?</h5>
                        <p class="mb-0">Anda yakin ingin membatalkan
                            <strong>{{ $penyesuaian->kode_penyesuaian }}</strong>?
                        </p>
                        <small class="text-danger">Tindakan ini akan mengembalikan stok produk ke keadaan semula
                            dan tidak dapat diurungkan.</small>
                        <div class="mt-4">
                            <form method="POST"
                                action="{{ route('stok-penyesuaian.destroy', $penyesuaian->kode_penyesuaian) }}">
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
    @endcan
@endsection
