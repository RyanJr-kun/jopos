<div class="table-responsive p-0 mt-3">
    <table class="table table-hover align-items-center justify-content-start mb-0" id="tableData">
        <thead>
            <tr class="table-secondary">
                <th class="text-uppercase text-dark text-xs font-weight-bolder">Nama</th>
                <th class="text-uppercase text-dark text-xs font-weight-bolder ps-2">Perusahaan</th>
                <th class="text-uppercase text-dark text-xs font-weight-bolder ps-2">Kontak</th>
                <th class="text-uppercase text-dark text-xs font-weight-bolder ps-2">Alamat</th>
                <th class="text-uppercase text-dark text-xs font-weight-bolder ps-2">Catatan</th>
                <th class="text-center text-uppercase text-dark text-xs font-weight-bolder">Status</th>
                <th class="text-dark"></th>
            </tr>
        </thead>
        <tbody id="isiTable">
            @forelse ($pemasoks as $pemasok)
                <tr id="pemasok-row-{{ $pemasok->id }}">
                    <td>
                        <p title="Nama Pemasok" class="ms-3 text-uppercase text-xs text-dark fw-bold mb-0">
                            {{ $pemasok->nama }}</p>
                    </td>
                    <td>
                        <p title="Nama Perusahaan" class="text-uppercase text-xs text-dark fw-bold mb-0">
                            {{ $pemasok->perusahaan }}</p>
                    </td>
                    <td>
                        <div class="d-block">
                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $pemasok->kontak) }}" target="_blank"
                                title="Kontak" class="text-xs text-dark fw-bold mb-0">{{ $pemasok->kontak }}</a>
                            <p title="Email" class="text-xs text-dark fw-bold mb-0">{{ $pemasok->email }}</p>
                        </div>
                    </td>
                    <td>
                        <p title="Alamat" class="text-xs text-dark fw-bold mb-0">{{ Str::limit($pemasok->alamat, 40) }}
                        </p>
                    </td>
                    <td>
                        <p title="Note" class="text-xs text-dark fw-bold mb-0">{{ Str::limit($pemasok->note, 40) }}
                        </p>
                    </td>

                    <td class="align-middle text-center text-sm">
                        @if ($pemasok->status)
                            <span class="badge badge-success">Aktif</span>
                        @else
                            <span class="badge badge-secondary">Tidak Aktif</span>
                        @endif
                    </td>

                    <td class="text-center align-middle">
                        <a href="#" class="text-dark fw-bold px-3 text-xs" data-bs-toggle="modal"
                            data-bs-target="#editModal" data-url="{{ route('pemasok.getjson', $pemasok->id) }}"
                            data-update-url="{{ route('pemasok.update', $pemasok->id) }}" title="Edit Pemasok">
                            <i class="bx bx-pencil-square text-dark text-sm opacity-10"></i>
                        </a>
                        <a href="#" class="text-dark delete-btn me-md-4" data-bs-toggle="modal"
                            data-bs-target="#deleteConfirmationModal" data-pemasok-id="{{ $pemasok->id }}"
                            data-pemasok-name="{{ $pemasok->nama }}" title="Hapus Pemasok">
                            <i class="bx bx-trash"></i>
                        </a>
                    </td>
                </tr>
            @empty
                <tr id="pemasok-row-empty">
                    <td colspan="7" class="text-center py-4">
                        <p class="text-dark text-sm fw-bold mb-0">Belum ada data pemasok.</p>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
    <div class="my-3 ms-3">{{ $pemasoks->onEachSide(1)->links() }}</div>
</div>
