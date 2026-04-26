<div class="table-responsive p-0 mt-3">
    <table class="table table-hover align-items-center justify-content-start mb-0" id="tableData">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th>Warrantie</th>
                <th>Description</th>
                <th>Durasi</th>
                <th class="text-center">Status</th>
                <th></th>
            </tr>
        </thead>
        <tbody id="isiTable">
            @forelse ($warranties as $key => $garansi)
                <tr id="garansi-row-{{ $garansi->slug }}">
                    <td>{{ ++$key }}</td>
                    <td>
                        <p>{{ $garansi->name }}</p>
                    </td>
                    <td>
                        <p>{{ Str::limit(strip_tags($garansi->description), 60) ?: '-' }}</p>
                    </td>
                    <td>
                        <small>{{ $garansi->formatted_duration }}</small>
                    </td>
                    <td class="align-middle text-center text-sm">
                        @if ($garansi->status)
                            <span class="badge bg-label-success">Aktif</span>
                        @else
                            <span class="badge bg-label-secondary">Tidak Aktif</span>
                        @endif
                    </td>
                    <td class="align-middle">
                        <a href="#" class="text-dark fw-bold px-3 text-xs" data-bs-toggle="modal"
                            data-bs-target="#editModal" data-url="{{ route('garansi.getjson', $garansi->slug) }}"
                            data-update-url="{{ route('garansi.update', $garansi->slug) }}" title="Edit garansi">
                            <i class="bx bx-edit text-dark text-sm opacity-10"></i>
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
