<div class="table-responsive p-0 mt-2">
    <table class="table table-hover align-middle mb-0" id="tableData">
        <thead class="table-light">
            <tr>
                <th class="ps-3" style="width:48px;">#</th>
                <th>Promotion</th>
                <th>Kode</th>
                <th>Diskon</th>
                <th>Periode</th>
                <th>Sisa Waktu</th>
                <th class="text-center" style="width:90px;">Status</th>
                <th class="text-center" style="width:80px;">Aksi</th>
            </tr>
        </thead>
        <tbody id="isiTable">
            @forelse ($promotions as $key => $promo)
                <tr id="promo-row-{{ $promo->id }}">
                    {{-- No --}}
                    <td class="ps-3 text-muted fw-semibold text-sm">
                        {{ $promotions->firstItem() + $key }}
                    </td>

                    {{-- Nama + cakupan --}}
                    <td>
                        <div class="fw-semibold text-dark" style="max-width:200px;">
                            {{ $promo->name }}
                        </div>
                        <small class="text-muted">
                            @if ($promo->is_all_products)
                                <i class="bx bx-check-circle text-success me-1"></i>Semua Produk
                            @else
                                <i class="bx bx-package text-info me-1"></i>Produk Tertentu
                            @endif
                        </small>
                    </td>

                    {{-- Kode --}}
                    <td>
                        @if ($promo->code)
                            <span class="badge bg-label-primary font-monospace" style="letter-spacing:.5px;">
                                {{ $promo->code }}
                            </span>
                        @else
                            <span class="text-muted text-sm">—</span>
                        @endif
                    </td>

                    {{-- Tipe & Nilai Diskon --}}
                    <td>
                        @if ($promo->type == 'percentage')
                            <span class="badge bg-label-warning rounded-pill me-1">%</span>
                            <span class="fw-bold text-warning">{{ $promo->nilai_diskon }}%</span>
                        @else
                            <span class="badge bg-label-danger rounded-pill me-1">Rp</span>
                            <span class="fw-bold text-danger">@money($promo->nilai_diskon)</span>
                        @endif
                        @if ($promo->min_pembelian)
                            <div class="text-muted" style="font-size:.72rem;">
                                Min. @money($promo->min_pembelian)
                            </div>
                        @endif
                    </td>

                    {{-- Periode --}}
                    <td>
                        <div class="text-sm">
                            <i class="bx bx-calendar-check text-success me-1"></i>
                            {{ \Carbon\Carbon::parse($promo->tanggal_mulai)->format('d M Y') }}
                        </div>
                        <div class="text-sm text-muted">
                            <i class="bx bx-calendar-x text-danger me-1"></i>
                            {{ \Carbon\Carbon::parse($promo->tanggal_berakhir)->format('d M Y') }}
                        </div>
                    </td>

                    {{-- Countdown --}}
                    <td id="countdown-{{ $promo->id }}"
                        data-end-time="{{ $promo->tanggal_berakhir->toIso8601String() }}"
                        data-promo-id="{{ $promo->id }}">
                        <div class="d-flex align-items-center gap-1">
                            <div class="spinner-border spinner-border-sm text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>
                    </td>

                    {{-- Status --}}
                    <td class="text-center" id="status-container-{{ $promo->id }}">
                        @if ($promo->status)
                            <span class="badge bg-label-success d-flex align-items-center justify-content-center px-2">
                                <i class="bx bx-check-circle me-1"></i>Aktif
                            </span>
                        @else
                            <span
                                class="badge bg-label-secondary d-flex align-items-center justify-content-center px-2">
                                <i class="bx bx-x-circle me-1"></i>Nonaktif
                            </span>
                        @endif
                    </td>

                    {{-- Aksi --}}
                    <td class="text-center">
                        <div class="dropdown">
                            <button class="btn btn-sm" type="button" data-bs-toggle="dropdown" aria-expanded="false"
                                title="Aksi">
                                <i class="bx bx-dots-vertical-rounded"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                                @can('view-promo')
                                    <li>
                                        <a class="dropdown-item text-sm py-2" href="{{ route('promo.show', $promo->id) }}">
                                            <i class="bx bx-show me-2 text-info"></i>Lihat Detail
                                        </a>
                                    </li>
                                @endcan
                                @can('edit-promo')
                                    <li>
                                        <a class="dropdown-item text-sm py-2" href="{{ route('promo.edit', $promo->id) }}">
                                            <i class="bx bx-edit me-2 text-warning"></i>Edit
                                        </a>
                                    </li>
                                @endcan
                                @can('delete-promo')
                                    <li>
                                        <hr class="dropdown-divider my-1">
                                    </li>
                                    <li>
                                        <a class="dropdown-item text-sm py-2 text-danger" href="#"
                                            data-bs-toggle="modal" data-bs-target="#deleteConfirmationModal"
                                            data-promo-id="{{ $promo->id }}" data-promo-name="{{ $promo->name }}">
                                            <i class="bx bx-trash me-2"></i>Hapus
                                        </a>
                                    </li>
                                @endcan
                            </ul>
                        </div>
                    </td>
                </tr>
            @empty
                <tr id="promo-row-empty">
                    <td colspan="8" class="text-center py-5">
                        <div class="d-flex flex-column align-items-center gap-2 text-muted">
                            <i class="bx bx-receipt" style="font-size:3rem; opacity:.35;"></i>
                            <p class="mb-0 fw-semibold">Belum ada data promo</p>
                            <small>Buat promotion pertama Anda sekarang.</small>
                            @can('create-promo')
                                <a href="{{ route('promo.create') }}" class="btn btn-sm btn-primary mt-1">
                                    <i class="bx bx-plus me-1"></i>Buat Promo
                                </a>
                            @endcan
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Pagination --}}
    @if ($promotions->hasPages())
        <div class="d-flex align-items-center justify-content-between px-3 py-3 border-top">
            <small class="text-muted">
                Menampilkan {{ $promotions->firstItem() }}–{{ $promotions->lastItem() }}
                dari {{ $promotions->total() }} data
            </small>
            {{ $promotions->onEachSide(1)->links() }}
        </div>
    @endif
</div>
