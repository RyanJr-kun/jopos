<div class="table-responsive p-0 mt-2">
    <table class="table table-hover align-items-center justify-content-start mb-0" id="tableData">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Promotion</th>
                <th>Kode Promotion</th>
                <th>Tipe Diskon</th>
                <th>Nilai Diskon</th>
                <th>Periode</th>
                <th>Hitung Mundur</th>
                <th class="text-center">Status</th>
                <th></th>
            </tr>
        </thead>
        <tbody id="isiTable">
            @forelse ($promotions as $key => $promo)
                <tr id="promo-row-{{ $promo->id }}">
                    <td>{{ ++$key }}</td>
                    <td>
                        <p>{{ $promo->name }}</p>
                    </td>
                    <td>
                        <p>{{ $promo->code ?? '-' }}</p>
                    </td>
                    <td>
                        <small>
                            @if ($promo->type == 'percentage')
                                Persentase
                            @else
                                Jumlah Tetap
                            @endif
                        </small>
                    </td>
                    <td>
                        <small>
                            @if ($promo->type == 'percentage')
                                {{ $promo->nilai_diskon }}%
                            @else
                                @money($promo->nilai_diskon)
                            @endif
                        </small>
                    </td>
                    <td>
                        <small>
                            {{ \Carbon\Carbon::parse($promo->tanggal_mulai)->format('d M Y') }} -
                            {{ \Carbon\Carbon::parse($promo->tanggal_berakhir)->format('d M Y') }}
                        </small>
                    </td>
                    <td id="countdown-{{ $promo->id }}"
                        data-end-time="{{ $promo->tanggal_berakhir->toIso8601String() }}"
                        data-promo-id="{{ $promo->id }}">
                        <div class="spinner-border spinner-border-sm" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </td>
                    <td class="align-middle text-center text-sm" id="status-container-{{ $promo->id }}">
                        @if ($promo->status)
                            <span class="badge bg-label-success">Aktif</span>
                        @else
                            <span class="badge bg-label-secondary">Tidak Aktif</span>
                        @endif
                    </td>
                    <td class="align-middle">
                        <a href="{{ route('promo.show', $promo->id) }}" class="action-btn text-info"
                            title="Lihat Detail">
                            <i class="bx bx-show"></i>
                        </a>
                        <a href="{{ route('promo.edit', $promo->id) }}" class="action-btn text-secondary"
                            title="Edit Promotion">
                            <i class="bx bx-edit"></i>
                        </a>
                        <a href="#" class="action-btn text-danger" data-bs-toggle="modal"
                            data-bs-target="#deleteConfirmationModal" data-promo-id="{{ $promo->id }}"
                            data-promo-name="{{ $promo->name }}" title="Hapus Promotion">
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
    <div class="my-3 ms-3">{{ $promotions->onEachSide(1)->links() }}</div>
</div>
