<div class="table-responsive p-0 mt-3">
    <table class="table table-hover align-items-center justify-content-start mb-0" id="tableData">
        <thead>
            <tr class="table-secondary">
                <th class="text-uppercase text-dark text-xs font-weight-bolder">Warrantie</th>
                <th class="text-uppercase text-dark text-xs font-weight-bolder ps-2">Description</th>
                <th class="text-uppercase text-dark text-xs font-weight-bolder ps-2">Durasi</th>
                <th class="text-center text-uppercase text-dark text-xs font-weight-bolder">Status</th>
                <th class="text-dark"></th>
            </tr>
        </thead>
        <tbody id="isiTable">
            @forelse ($warranties as $garansi)
                <tr id="garansi-row-{{ $garansi->slug }}">
                    <td>
                        <p title="garansi" class="ms-3 text-xs text-dark fw-bold mb-0">{{ $garansi->name }}</p>
                    </td>
                    <td>
                        <p title="Description" class=" text-xs text-dark fw-bold mb-0">
                            {{ Str::limit(strip_tags($garansi->description), 60) ?: '-' }}</p>
                    </td>
                    <td class="align-middle ">
                        <span class="text-dark text-xs fw-bold">{{ $garansi->formatted_duration }}</span>
                    </td>
                    <td class="align-middle text-center text-sm">
                        @if ($garansi->status)
                            <span class="badge badge-success">Aktif</span>
                        @else
                            <span class="badge badge-secondary">Tidak Aktif</span>
                        @endif
                    </td>
                    <td class="align-middle">
                        <a href="#" class="text-dark fw-bold px-3 text-xs" data-bs-toggle="modal"
                            data-bs-target="#editModal" data-url="{{ route('garansi.getjson', $garansi->slug) }}"
                            data-update-url="{{ route('garansi.update', $garansi->slug) }}" title="Edit garansi">
                            <i class="bx bx-pencil-square text-dark text-sm opacity-10"></i>
                        </a>
                        <a href="#" class="text-dark delete-user-btn" data-bs-toggle="modal"
                            data-bs-target="#deleteConfirmationModal" data-garansi-slug="{{ $garansi->slug }}"
                            data-garansi-name="{{ $garansi->name }}" title="Hapus garansi">
                            <i class="bx bx-trash"></i>
                        </a>
                    </td>
                </tr>
            @empty
                <tr id="garansi-row-empty">
                    <td colspan="5" class="text-center py-4">
                        <p class="text-dark text-sm fw-bold mb-0">Belum ada data garansi.</p>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
    <div class="my-3 ms-3">{{ $warranties->onEachSide(1)->links() }}</div>
</div>
