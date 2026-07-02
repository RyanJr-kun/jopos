{{-- resources/views/content/hrd/store/_toko-cards.blade.php --}}
@forelse ($tokos as $toko)
    <div class="col-md-6 col-lg-4">
        <div class="loc-card loc-card-toko">
            <div class="loc-card-body">

                <div class="loc-card-header">
                    <div class="d-flex align-items-center" style="min-width:0">
                        <div class="loc-logo-wrap me-3">
                            <img src="{{ $toko->logo ? Storage::url($toko->logo) : asset('assets/img/default-store.png') }}"
                                alt="Logo" class="loc-logo">
                            <span class="loc-type-dot"></span>
                        </div>
                        <div class="loc-name-block">
                            <h6>{{ $toko->name_toko }}</h6>
                            @if ($toko->is_active)
                                <span class="loc-status-badge status-active">Aktif</span>
                            @else
                                <span class="loc-status-badge status-inactive">Non-Aktif</span>
                            @endif
                        </div>
                    </div>

                    <div class="dropdown ms-2">
                        <div class="dropdown">
                            <button class="btn p-0" type="button" data-bs-toggle="dropdown" aria-haspopup="true"
                                aria-expanded="false">
                                <i class="bx bx-dots-vertical-rounded"></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-end">
                                <button type="button"
                                    class="dropdown-item btn-members d-flex text-blue align-item-center"
                                    data-id="{{ $toko->id }}">
                                    <i class="bx bx-user-plus fs-5 me-1"></i> <span class="align-middle">Atur
                                        Anggota</span>
                                </button>
                                <a class="dropdown-item btn-edit text-secondary" href="javascript:void(0);"
                                    data-store='@json($toko)'
                                    data-logo-url="{{ $toko->logo ? Storage::url($toko->logo) : '' }}">
                                    <i class="bx bx-edit-alt me-1 "></i> Edit
                                </a>
                                <button type="button" class="dropdown-item btn-delete-trigger text-danger"
                                    data-bs-toggle="modal" data-bs-target="#modalDeleteStore"
                                    data-title="{{ $toko->name_toko }}"
                                    data-action="{{ route('toko.destroy', $toko->id) }}">
                                    <i class="bx bx-trash me-1 "></i> Hapus
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="loc-info-row">
                    <i class="bx bx-map loc-info-icon"></i>
                    <span>{{ $toko->full_region }}</span>
                </div>
                <hr class="mx-2 mt-0 mb-2">
                <div class="loc-info-row d-flex align-items-center justify-content-between">

                    <div class="d-flex align-items-center gap-3" style="min-width:0">
                        {{-- BUG FIX: sebelumnya $toko->pic->name / ->email diakses tanpa
                                 null-safe operator (?->), jadi error "Attempt to read property
                                 on null" kalau toko belum punya PIC (pic_id kosong). --}}
                        @if ($toko->pic)
                            @if ($toko->pic->avatar)
                                <img src="{{ Storage::url($toko->pic->avatar) }}" class="rounded-circle shadow-sm"
                                    style="width:35px;height:35px;object-fit:cover;flex-shrink:0;"
                                    alt="{{ $toko->pic->name }}">
                            @else
                                <div class="rounded-circle bg-label-primary d-flex align-items-center justify-content-center shadow-sm"
                                    style="width:35px;height:35px;font-size:12px;font-weight:700;flex-shrink:0;">
                                    {{ strtoupper(substr($toko->pic->name, 0, 2)) }}
                                </div>
                            @endif
                            <div style="min-width:0">
                                <h6 class="mb-0 fw-bold text-truncate" style="max-width: 140px;">
                                    {{ $toko->pic->name }}</h6>
                                <small class="text-muted text-truncate d-block"
                                    style="max-width: 140px;">{{ $toko->pic->email }}</small>
                            </div>
                        @else
                            <div class="rounded-circle bg-label-secondary d-flex align-items-center justify-content-center shadow-sm"
                                style="width:35px;height:35px;font-size:12px;font-weight:700;flex-shrink:0;">
                                -
                            </div>
                            <div>
                                <h6 class="mb-0 fw-bold text-truncate" style="max-width: 140px;">
                                    Belum Ditentukan</h6>
                            </div>
                        @endif
                    </div>

                    {{-- Trigger popover detail anggota --}}
                    <button type="button" class="loc-anggota-trigger" data-id="{{ $toko->id }}"
                        data-store-name="{{ $toko->name_toko }}" title="Lihat anggota">
                        <i class="bx bx-group"></i>
                        <span>{{ $toko->employees->count() }}</span>
                        <i class="bx bx-chevron-down loc-anggota-caret"></i>
                    </button>

                </div>
            </div>
        </div>
    </div>
@empty
    <div class="col-12">
        <div class="loc-empty-state">
            <img class="loc-empty-img" src="{{ asset('assets/img/illustrations/man-with-laptop.png') }}" width="140"
                alt="Kosong">
            <h6 class="mt-2">Belum ada data Toko</h6>
        </div>
    </div>
@endforelse
