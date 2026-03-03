<div class="table-responsive p-0 mt-2">
    <table class="table table-hover align-items-center justify-content-start mb-0" id="tableData">
        <thead>
            <tr class="table-secondary">
                <th class="text-uppercase text-dark text-xs font-weight-bolder">Nama Promo</th>
                <th class="text-uppercase text-dark text-xs font-weight-bolder ps-2">Kode Promo</th>
                <th class="text-uppercase text-dark text-xs font-weight-bolder ps-2">Tipe Diskon</th>
                <th class="text-uppercase text-dark text-xs font-weight-bolder ps-2">Nilai Diskon</th>
                <th class="text-uppercase text-dark text-xs font-weight-bolder ps-2">Periode</th>
                <th class="text-uppercase text-dark text-xs font-weight-bolder ps-2">Hitung Mundur</th>
                <th class="text-center text-uppercase text-dark text-xs font-weight-bolder">Status</th>
                <th class="text-dark"></th>
            </tr>
        </thead>
        <tbody id="isiTable">
            @forelse ($promos as $promo)
                <tr id="promo-row-{{ $promo->id }}">
                    <td>
                        <p title="Nama Promo" class="ms-3 text-xs text-dark fw-bold mb-0">{{ $promo->nama_promo }}</p>
                    </td>
                    <td>
                        <p title="Kode Promo" class="text-xs text-dark fw-bold mb-0">{{ $promo->kode_promo ?? '-' }}</p>
                    </td>
                    <td>
                        <p title="Tipe Diskon" class="text-xs text-dark fw-bold mb-0">
                            @if ($promo->tipe_diskon == 'percentage')
                                Persentase
                            @else
                                Jumlah Tetap
                            @endif
                        </p>
                    </td>
                    <td>
                        <p title="Nilai Diskon" class="text-xs text-dark fw-bold mb-0">
                            @if ($promo->tipe_diskon == 'percentage')
                                {{ $promo->nilai_diskon }}%
                            @else
                                @money($promo->nilai_diskon)
                            @endif
                        </p>
                    </td>
                    <td>
                        <p title="Periode Promo" class="text-xs text-dark fw-bold mb-0">
                            {{ \Carbon\Carbon::parse($promo->tanggal_mulai)->format('d M Y') }} -
                            {{ \Carbon\Carbon::parse($promo->tanggal_berakhir)->format('d M Y') }}
                        </p>
                    </td>
                    <td class="text-xs text-dark fw-bold mb-0" id="countdown-{{ $promo->id }}"
                        data-end-time="{{ $promo->tanggal_berakhir->toIso8601String() }}"
                        data-promo-id="{{ $promo->id }}">
                        <div class="spinner-border spinner-border-sm" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </td>
                    <td class="align-middle text-center text-sm" id="status-container-{{ $promo->id }}">
                        @if ($promo->status)
                            <span class="badge badge-success">Aktif</span>
                        @else
                            <span class="badge badge-secondary">Tidak Aktif</span>
                        @endif
                    </td>
                    <td class="align-middle">
                        <a href="{{ route('promo.show', $promo->id) }}" class="text-dark fw-bold px-2 text-xs"
                            title="Lihat Detail">
                            <i class="bx bx-eye-fill text-dark text-sm opacity-10"></i>
                        </a>
                        <a href="{{ route('promo.edit', $promo->id) }}" class="text-dark fw-bold px-2 text-xs"
                            title="Edit Promo">
                            <i class="bx bx-pencil-square text-dark text-sm opacity-10"></i>
                        </a>
                        <a href="#" class="text-dark delete-btn px-2" data-bs-toggle="modal"
                            data-bs-target="#deleteConfirmationModal" data-promo-id="{{ $promo->id }}"
                            data-promo-name="{{ $promo->nama_promo }}" title="Hapus Promo">
                            <i class="bx bx-trash"></i>
                        </a>
                    </td>
                </tr>
            @empty
                <tr id="promo-row-empty">
                    <td colspan="8" class="text-center py-4">
                        <p class="text-dark text-sm fw-bold mb-0">Belum ada data promo.</p>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
    <div class="my-3 ms-3">{{ $promos->onEachSide(1)->links() }}</div>
</div>
