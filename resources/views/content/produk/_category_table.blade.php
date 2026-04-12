<div class="table-responsive p-0 my-3">
    <table class="table table-hover align-items-center justify-content-start mb-0" id="tableData">
        <thead>
            <tr class="table-secondary">
                <th class="text-uppercase text-dark text-xs font-weight-bolder">Kategori</th>
                <th class="text-uppercase text-dark text-xs font-weight-bolder ps-2">kategori Slug</th>
                <th class="text-uppercase text-dark text-xs font-weight-bolder ps-2">Jumlah Product</th>
                <th class="text-uppercase text-dark text-xs font-weight-bolder ps-2">Dibuat Tanggal</th>
                <th class="text-center text-uppercase text-dark text-xs font-weight-bolder">status</th>
                <th class="text-dark"></th>
            </tr>
        </thead>
        <tbody id="isiTable">
            @forelse ($kategoris as $kategori)
                <tr id="kategori-row-{{ $kategori->slug }}">
                    <td>
                        <div title="image & Nama Kategori" class="d-flex align-items-center px-2 py-1">
                            @if ($kategori->img_kategori)
                                <img src="{{ asset('storage/' . $kategori->img_kategori) }}"
                                    class="avatar avatar-sm me-3" alt="{{ $kategori->name }}">
                            @else
                                <img src="{{ asset('assets/img/produk.png') }}" class="avatar avatar-sm me-3"
                                    alt="Gambar produk default">
                            @endif
                            <h6 class="mb-0 text-sm">{{ $kategori->name }}</h6>
                        </div>
                    </td>
                    <td>
                        <p title="kategori slug" class=" text-xs text-dark fw-bold mb-0">{{ $kategori->slug }}</p>
                    </td>
                    <td>
                        <p class="text-xs text-dark fw-bold mb-0">{{ $kategori->products_count }}</p>
                    </td>
                    <td class="align-middle ">
                        <span
                            class="text-dark text-xs fw-bold">{{ $kategori->created_at?->translatedFormat('d M Y') }}</span>
                    </td>
                    <td class="align-middle text-center text-sm">
                        @if ($kategori->status)
                            <span class="badge bg-label-success">Aktif</span>
                        @else
                            <span class="badge bg-label-secondary">Tidak Aktif</span>
                        @endif
                    </td>
                    <td class="align-middle">
                        <a href="#" class="text-dark fw-bold px-3 text-xs" data-bs-toggle="modal"
                            data-bs-target="#editModal"
                            data-url="{{ route('kategoriproduk.getjson', $kategori->slug) }}"
                            data-update-url="{{ route('kategoriproduk.update', $kategori->slug) }}"
                            title="Edit kategori"><i class="bx bx-edit text-dark text-sm opacity-10"></i></a>
                        <a href="#" class="text-dark delete-btn" data-bs-toggle="modal"
                            data-bs-target="#deleteConfirmationModal" data-kategori-slug="{{ $kategori->slug }}"
                            data-kategori-name="{{ $kategori->name }}" title="Hapus kategori"><i
                                class="bx bx-trash"></i></a>
                    </td>
                </tr>
            @empty
                <tr id="kategori-row-empty">
                    <td colspan="6" class="text-center py-4">
                        <p class="text-dark text-sm fw-bold mb-0">Belum ada data kategori.</p>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
    <div class="my-3 ms-3">{{ $kategoris->onEachSide(2)->links() }}</div>
</div>
