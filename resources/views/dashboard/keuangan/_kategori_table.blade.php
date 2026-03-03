<div class="table-responsive p-0 mt-4">
    <table class="table table-hover align-items-center justify-content-start mb-0" id="tableData">
        <thead>
            <tr class="table-secondary">
                <th class="text-uppercase text-dark text-xs font-weight-bolder">Kategori</th>
                <th class="text-uppercase text-dark text-xs font-weight-bolder ps-2">Jenis</th>
                <th class="text-uppercase text-dark text-xs font-weight-bolder ps-2">Deskripsi</th>
                <th class="text-center text-uppercase text-dark text-xs font-weight-bolder">Status</th>
                <th class="text-dark"></th>
            </tr>
        </thead>
        <tbody id="isiTable">
            @forelse ($kategoris as $kategori)
                <tr>
                    <td>
                        <p title="kategori" class="ms-3 text-xs text-dark fw-bold mb-0">{{ $kategori->nama }}</p>
                    </td>

                    <td>
                        @if ($kategori->jenis == 'pemasukan')
                            <span class="badge badge-success text-capitalize">{{ $kategori->jenis }}</span>
                        @elseif ($kategori->jenis == 'pengeluaran')
                            <span class="badge badge-warning text-capitalize">{{ $kategori->jenis }}</span>
                        @endif
                    </td>

                    <td>
                        <p title="Deskripsi" class=" text-xs text-dark fw-bold mb-0">
                            {{ Str::limit(strip_tags($kategori->deskripsi), 60) }}</p>
                    </td>

                    <td class="align-middle text-center text-sm">
                        @if ($kategori->status)
                            <span class="badge badge-success">Aktif</span>
                        @else
                            <span class="badge badge-secondary">Tidak Aktif</span>
                        @endif
                    </td>

                    <td class="text-center">
                        <a href="#" class="text-dark fw-bold px-3 text-xs" data-bs-toggle="modal"
                            data-bs-target="#editModal"
                            data-url="{{ route('kategoritransaksi.getjson', $kategori->slug) }}"
                            data-update-url="{{ route('kategoritransaksi.update', $kategori->slug) }}"
                            title="Edit kategori">
                            <i class="bx bx-pencil-square text-dark text-sm opacity-10"></i>
                        </a>
                        <a href="#" class="text-dark delete-user-btn me-md-4" data-bs-toggle="modal"
                            data-bs-target="#deleteConfirmationModal" data-kategori-slug="{{ $kategori->slug }}"
                            data-kategori-name="{{ $kategori->nama }}" title="Hapus kategori">
                            <i class="bx bx-trash"></i>
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center py-3 ">
                        <p class=" text-dark text-sm fw-bold mb-0">Belum ada data Kategori Transaksi.</p>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
    <div class="my-3 ms-3">{{ $kategoris->onEachSide(1)->links() }}</div>
</div>
