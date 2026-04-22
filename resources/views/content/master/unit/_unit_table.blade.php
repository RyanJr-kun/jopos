<div class="table-responsive p-0 mt-3">
    <table class="table table-hover align-items-center mb-0" id="tableData">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th>Nama</th>
                <th>Singkatan</th>
                <th class="text-center">Jumlah Product</th>
                <th class="text-center">Status</th>
                <th></th>
            </tr>
        </thead>
        <tbody id="isiTable">
            @forelse ($units as $key => $unit)
                <tr id="unit-row-{{ $unit->slug }}">
                    <td>
                        {{ ++$key }}
                    <td>
                        <p>{{ $unit->name }}</p>
                        
                    </td>
                    <td>
                        <p>{{ $unit->singkat }}</p>
                    </td>
                    <td>
                        <p class="text-center">{{ $unit->products_count }}</p>
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
