@extends('layouts/contentNavbarLayout')

@section('title', $title ?? 'Detail Stock Opname')

@section('vendor-style')
    <style>
        .so-stat-icon {
            width: 42px;
            height: 42px;
            border-radius: .5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .so-diff-up {
            color: #71dd37;
        }

        .so-diff-down {
            color: #ff3e1d;
        }

        .so-diff-zero {
            color: #a1acb8;
        }

        .so-diff-pill {
            display: inline-flex;
            align-items: center;
            gap: .25rem;
            font-weight: 600;
            font-size: .8125rem;
        }

        .so-product-thumb {
            width: 38px;
            height: 38px;
            border-radius: .5rem;
            object-fit: cover;
            background-color: #f5f5f9;
        }

        .so-meta-label {
            font-size: .75rem;
            text-transform: uppercase;
            letter-spacing: .02em;
            color: #a1acb8;
            margin-bottom: .125rem;
        }

        .so-meta-value {
            font-size: .9375rem;
            font-weight: 500;
            color: #566a7f;
        }

        .so-table thead th {
            font-size: .6875rem;
            text-transform: uppercase;
            letter-spacing: .04em;
            color: #a1acb8;
            border-bottom-width: 1px;
            padding-top: .85rem;
            padding-bottom: .85rem;
        }

        .so-table tbody tr:last-child td {
            border-bottom: none;
        }
    </style>
@endsection

@section('content')

    {{-- Page header --}}
    <div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-4">
        <div>
            <h4 class="mb-1 fw-bold">{{ $stokOpname->kode_opname }}</h4>
            <p class="text-muted mb-0">
                Rincian penyesuaian stok &middot;
                {{ $stokOpname->tanggal_opname->translatedFormat('l, d F Y') }} &middot;
                {{ $stokOpname->tanggal_opname->format('H:i') }}
            </p>
        </div>
        <a href="{{ route('stok-opname.history') }}" class="btn btn-outline-secondary px-2" title="Kembali"
            data-bs-toggle="tooltip" data-bs-placement="top">
            <i class="bx bx-arrow-back"></i>
        </a>
    </div>

    @php
        $details = $stokOpname->details;
        $totalItems = $details->count();
        $totalNaik = $details->where('selisih', '>', 0)->sum('selisih');
        $totalTurun = abs($details->where('selisih', '<', 0)->sum('selisih'));
        $netSelisih = $details->sum('selisih');
    @endphp

    {{-- Summary row --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card rounded-3 h-100">
                <div class="card-body d-flex align-items-center gap-3 py-3">
                    <div class="so-stat-icon bg-label-primary">
                        <i class="bx bx-list-check fs-4"></i>
                    </div>
                    <div>
                        <div class="so-meta-label mb-0">Item Disesuaikan</div>
                        <div class="fw-bold fs-5">{{ $totalItems }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card rounded-3 h-100">
                <div class="card-body d-flex align-items-center gap-3 py-3">
                    <div class="so-stat-icon bg-label-success">
                        <i class="bx bx-trending-up fs-4"></i>
                    </div>
                    <div>
                        <div class="so-meta-label mb-0">Stok Bertambah</div>
                        <div class="fw-bold fs-5 so-diff-up">+{{ $totalNaik }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card rounded-3 h-100">
                <div class="card-body d-flex align-items-center gap-3 py-3">
                    <div class="so-stat-icon bg-label-danger">
                        <i class="bx bx-trending-down fs-4"></i>
                    </div>
                    <div>
                        <div class="so-meta-label mb-0">Stok Berkurang</div>
                        <div class="fw-bold fs-5 so-diff-down">-{{ $totalTurun }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card rounded-3 h-100">
                <div class="card-body d-flex align-items-center gap-3 py-3">
                    <div
                        class="so-stat-icon bg-label-{{ $netSelisih == 0 ? 'secondary' : ($netSelisih > 0 ? 'success' : 'danger') }}">
                        <i class="bx bx-git-compare fs-4"></i>
                    </div>
                    <div>
                        <div class="so-meta-label mb-0">Net Selisih</div>
                        <div
                            class="fw-bold fs-5 {{ $netSelisih == 0 ? 'so-diff-zero' : ($netSelisih > 0 ? 'so-diff-up' : 'so-diff-down') }}">
                            {{ $netSelisih > 0 ? '+' . $netSelisih : $netSelisih }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Info card --}}
    <div class="card rounded-3 mb-4">
        <div class="card-body">
            <div class="row gy-3">
                <div class="col-6 col-md-3">
                    <div class="so-meta-label">Toko</div>
                    <div class="so-meta-value">{{ $stokOpname->store->name_toko ?? '-' }}</div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="so-meta-label">Dilakukan Oleh</div>
                    <div class="so-meta-value">{{ $stokOpname->user->username ?? 'N/A' }}</div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="so-meta-label">Status</div>
                    <span class="badge bg-label-success">{{ $stokOpname->status }}</span>
                </div>
                <div class="col-6 col-md-3">
                    <div class="so-meta-label">Catatan</div>
                    <div class="so-meta-value">{{ $stokOpname->catatan ?: '-' }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Items table --}}
    <div class="card rounded-3">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h6 class="mb-0">Item yang Disesuaikan</h6>
            <span class="text-muted small">{{ $totalItems }} item</span>
        </div>
        <div class="table-responsive">
            <table class="table so-table align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">Produk</th>
                        <th class="text-center">Stok Sistem</th>
                        <th class="text-center">Stok Fisik</th>
                        <th class="text-center">Selisih</th>
                        <th class="pe-4">Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($details as $detail)
                        @php
                            $itemProduk = $detail->produk;
                            $itemVariant = $detail->variant ?? null;

                            $imgUrl =
                                $itemVariant && $itemVariant->img_variant
                                    ? Storage::url($itemVariant->img_variant)
                                    : ($itemProduk && $itemProduk->primaryImage && $itemProduk->primaryImage->path
                                        ? Storage::url($itemProduk->primaryImage->path)
                                        : asset('assets/img/produk.png'));

                            $diffClass =
                                $detail->selisih == 0
                                    ? 'so-diff-zero'
                                    : ($detail->selisih > 0
                                        ? 'so-diff-up'
                                        : 'so-diff-down');

                            $diffIcon =
                                $detail->selisih == 0
                                    ? 'bx-minus'
                                    : ($detail->selisih > 0
                                        ? 'bx-up-arrow-alt'
                                        : 'bx-down-arrow-alt');
                        @endphp
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center gap-3">
                                    <img src="{{ $imgUrl }}" class="so-product-thumb"
                                        alt="{{ $itemProduk->name_product ?? 'produk' }}">
                                    <div>
                                        <div class="fw-medium">{{ $itemProduk->name_product ?? 'Produk Dihapus' }}</div>
                                        <div class="text-muted small">
                                            @if ($itemVariant)
                                                <span class="badge bg-label-info me-1">{{ $itemVariant->label }}</span>
                                            @endif
                                            {{ $itemVariant->sku ?? ($itemProduk->sku ?? 'N/A') }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="text-center">{{ $detail->stok_sistem }}</td>
                            <td class="text-center fw-medium">{{ $detail->stok_fisik }}</td>
                            <td class="text-center">
                                <span class="so-diff-pill {{ $diffClass }}">
                                    <i class="bx {{ $diffIcon }}"></i>
                                    {{ $detail->selisih > 0 ? '+' . $detail->selisih : $detail->selisih }}
                                </span>
                            </td>
                            <td class="pe-4 text-muted">{{ $detail->keterangan ?: '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <i class="bx bx-package fs-1 d-block mb-2"></i>
                                Tidak ada detail item untuk opname ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

@endsection
