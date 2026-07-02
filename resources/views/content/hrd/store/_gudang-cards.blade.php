{{-- resources/views/content/hrd/store/_gudang-cards.blade.php --}}
@forelse ($gudangs as $gudang)
    <div class="col-md-6 col-lg-4">
        <div class="loc-card loc-card-gudang">
            <div class="loc-card-body">

                <div class="loc-card-header">
                    <div class="d-flex align-items-center" style="min-width:0">
                        <div class="loc-logo-wrap me-3">
                            <img src="{{ $gudang->logo ? Storage::url($gudang->logo) : asset('assets/img/default-store.png') }}"
                                alt="Logo" class="loc-logo">
                            <span class="loc-type-dot"></span>
                        </div>
                        <div class="loc-name-block">
                            <h6>{{ $gudang->name_toko }}</h6>
                            @if ($gudang->is_active)
                                <span class="loc-status-badge status-active">Aktif</span>
                            @else
                                <span class="loc-status-badge status-inactive">Non-Aktif</span>
                            @endif
                        </div>
                    </div>

                    <div class="dropdown ms-2">
                        <button class="loc-dropdown-btn" type="button" data-bs-toggle="dropdown">
                            <i class="bx bx-dots-vertical-rounded"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-end loc-dropdown-menu">
                            <a class="dropdown-item btn-edit" href="javascript:void(0);"
                                data-store='@json($gudang)'
                                data-logo-url="{{ $gudang->logo ? Storage::url($gudang->logo) : '' }}">
                                <i class="bx bx-edit-alt me-1 text-info"></i> Edit
                            </a>
                            <button type="button" class="dropdown-item btn-delete-trigger" data-bs-toggle="modal"
                                data-bs-target="#modalDeleteStore" data-title="{{ $gudang->name_toko }}"
                                data-action="{{ route('toko.destroy', $gudang->id) }}">
                                <i class="bx bx-trash text-danger"></i> Hapus
                            </button>
                        </div>
                    </div>
                </div>

                <div class="loc-info-row">
                    <i class="bx bx-map loc-info-icon"></i>
                    <span>{{ $gudang->full_region }}</span>
                </div>
                <div class="loc-info-row d-flex align-items-center justify-content-between gap-2">
                    <div class="d-flex align-items-center gap-2" style="min-width:0">
                        <i class="bx bx-user loc-info-icon"></i>
                        {{-- Halaman Gudang --}}
                        <span class="text-truncate">PIC:
                            <strong>{{ $gudang->pic?->name ?? 'Belum Ditentukan' }}</strong>
                        </span>
                    </div>

                    {{-- Trigger popover detail anggota --}}
                    <button type="button" class="loc-anggota-trigger" data-id="{{ $gudang->id }}"
                        data-store-name="{{ $gudang->name_toko }}" title="Lihat anggota">
                        <i class="bx bx-group"></i>
                        <span>{{ $gudang->employees->count() }}</span>
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
            <h6 class="mt-2">Belum ada data Gudang</h6>
        </div>
    </div>
@endforelse
