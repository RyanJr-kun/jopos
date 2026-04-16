<div class="table-responsive p-0 mt-3">
    <table class="table table-hover align-items-center mb-0" id="tableData">
        <thead>
            <tr class="table-secondary">
                <th class="text-uppercase text-dark text-xs font-weight-bolder">Nama</th>
                <th class="text-uppercase text-dark text-xs font-weight-bolder ps-2">Singkatan</th>
                <th class="text-uppercase text-dark text-xs font-weight-bolder ps-2">Jumlah Product</th>
                <th class="text-uppercase text-dark text-xs font-weight-bolder ps-2">Dibuat Tanggal</th>
                <th class="text-center text-uppercase text-dark text-xs font-weight-bolder">Status</th>
                <th class="text-dark"></th>
            </tr>
        </thead>
        <tbody id="isiTable">
            @forelse ($units as $unit)
                <tr id="unit-row-{{ $unit->slug }}">
                    <td>
                        <div class="d-flex ms-2 px-2 py-1 align-items-center">
                            <p class="mb-0 text-xs text-dark fw-bold">{{ $unit->name }}</p>
                        </div>
                    </td>
                    <td>
                        <p class="text-xs text-dark fw-bold mb-0">{{ $unit->singkat }}</p>
                    </td>
                    <td>
                        <p class="text-xs text-dark fw-bold mb-0">{{ $unit->products_count }}</p>
                    </td>
                    <td>
                        <p class="text-xs text-dark fw-bold mb-0">{{ $unit->created_at->translatedFormat('d M Y') }}</p>
                    </td>

                    <td class="align-middle text-center text-sm">
                        @if ($unit->status)
                            <span class="badge bg-label-success">Aktif</span>
                        @else
                            <span class="badge bg-label-secondary">Tidak Aktif</span>
                        @endif
                    </td>

                    <td class="align-middle">
                        <a href="#" class="text-dark fw-bold px-3 text-xs" data-bs-toggle="modal"
                            data-bs-target="#editModal" data-url="{{ route('unit.getjson', $unit->slug) }}"
                            data-update-url="{{ route('unit.update', $unit->slug) }}" title="Edit Satuan">
                            <i class="bx bx-edit text-dark text-sm opacity-10"></i>
                        </a>
                        <a href="#" class="text-dark delete-btn" data-bs-toggle="modal"
                            data-bs-target="#deleteConfirmationModal" data-unit-slug="{{ $unit->slug }}"
                            data-unit-name="{{ $unit->name }}" title="Hapus Satuan">
                            <i class="bx bx-trash"></i>
                        </a>
                    </td>
                </tr>
            @empty
                <tr id="unit-row-empty">
                    <td colspan="6" class="text-center py-4">
                        <p class="text-dark text-sm fw-bold mb-0">Belum ada data satuan.</p>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
    <div class="my-3 ms-3">{{ $units->onEachSide(1)->links() }}</div>
</div>
