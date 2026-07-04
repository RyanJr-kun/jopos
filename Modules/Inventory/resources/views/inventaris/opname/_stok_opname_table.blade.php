<div class="table-responsive p-0">
    <table class="table table-hover align-items-center mb-0">
        <thead class="bg-label-light">
            <tr>
                <th class="" Width="5%">No</th>
                <th class="text-uppercase text-dark text-xs font-weight-bolder ps-4">Kode Opname</th>
                <th class="text-uppercase text-dark text-xs font-weight-bolder">Tanggal</th>
                <th class="text-uppercase text-dark text-xs font-weight-bolder">User</th>
                <th class="text-uppercase text-dark text-xs font-weight-bolder">Catatan</th>
                <th class="text-center text-uppercase text-dark text-xs font-weight-bolder">Status</th>
                <th class="text-center text-uppercase text-dark text-xs font-weight-bolder">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($stokOpnames as $key => $opname)
                <tr>
                    <td class="">
                        {{ ++$key }}
                    </td>
                    <td class="ps-4">
                        <p class="text-sm font-weight-bold mb-0">{{ $opname->kode_opname }}</p>
                    </td>
                    <td>
                        <p class="text-sm mb-0">
                            {{ $opname->tanggal_opname->translatedFormat('d M Y, H:i') }}</p>
                    </td>
                    <td>
                        <p class="text-sm mb-0">{{ $opname->user->username ?? 'N/A' }}</p>
                    </td>
                    <td>
                        <p class="text-sm mb-0 text-truncate" style="max-width: 250px;">
                            {{ $opname->catatan ?: '-' }}</p>
                    </td>
                    <td class="text-center"><span class="badge badge-sm bg-label-success">{{ $opname->status }}</span>
                    </td>
                    <td class="text-center">
                        <a href="{{ route('stok-opname.show', $opname->kode_opname) }}"
                            class="btn btn-link text-dark px-3 mb-0" data-bs-toggle="tooltip" data-bs-placement="top"
                            title="Lihat Detail">
                            <i class="bx bx-show" aria-hidden="true"></i>
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center py-4">
                        <p class="text-sm fw-bold mb-0">Tidak ada riwayat stok opname ditemukan.</p>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="d-flex justify-content-center my-4">
    {{ $stokOpnames->links() }}
</div>
