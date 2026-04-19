<div class="table-responsive p-0 my-3">
    <table class="table table-hover align-items-center justify-content-start mb-0" id="tableData">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="20%">
                    {{ request('type', 'utama') === 'utama' ? 'Kategori Utama' : 'Sub Kategori' }}
                </th>
                <th width="20%">Slug</th>
                <th width="15%">
                    {{ request('type') === 'utama' ? 'Sub Kategori' : 'Kategori Utama' }}
                </th>
                <th width="10%" class="text-center">Jml Produk</th>
                <th width="10%" class="text-center">Status</th>
                <th width="10%"></th>
            </tr>
        </thead>
        <tbody id="isiTable">
            @forelse ($kategoris as $key => $kategori)
                <tr id="kategori-row-{{ $kategori->slug }}">
                    <td>{{ ++$key }}</td>
                    {{-- Kolom 1: Gambar & Nama Kategori --}}
                    <td>
                        <div title="image & Nama Kategori" class="d-flex align-items-center">
                            @if ($kategori->img_kategori)
                                <img src="{{ asset('storage/' . $kategori->img_kategori) }}"
                                    class="avatar avatar-sm me-3" alt="{{ $kategori->name }}">
                            @else
                                <img src="{{ asset('assets/img/produk.png') }}" class="avatar avatar-sm me-3"
                                    alt="Gambar produk default">
                            @endif
                            <div>
                                <h6 class="mb-0 text-sm">{{ $kategori->name }}</h6>
                                {{-- Label kecil di bawah nama untuk memperjelas --}}
                                @if ($kategori->parent_id)
                                    <small class="text-muted">Sub Kategori</small>
                                @else
                                    <small class="text-primary">Kategori Utama</small>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td>
                        <p class="text-xs text-dark fw-bold mb-0">{{ $kategori->slug }}</p>
                    </td>
                    <td>
                        @if (request('type') === 'utama')
                            {{-- TAMPILAN UNTUK KATEGORI UTAMA: Menampilkan daftar anak (Sub) --}}
                            <div class="d-flex flex-wrap gap-1">
                                @forelse ($kategori->children->take(10) as $sub)
                                    {{-- Tampilkan 3 saja --}}
                                    <span class="badge rounded-pill bg-label-secondary text-xs">
                                        #{{ $sub->name }}
                                    </span>
                                @empty
                                    <span class="text-muted text-xs">Tidak ada sub</span>
                                @endforelse

                                @if ($kategori->children->count() > 10)
                                    <span class="text-xs text-muted">... +{{ $kategori->children->count() - 3 }}
                                        lainnya</span>
                                @endif
                            </div>
                        @else
                            {{-- TAMPILAN STANDAR: Menampilkan Parent jika dia adalah sub --}}
                            @if ($kategori->parent)
                                <span class="badge bg-label-primary">{{ $kategori->parent->name }}</span>
                            @else
                                <span class="text-muted text-xs">—</span>
                            @endif
                        @endif
                    </td>
                    <td class="text-center">{{ $kategori->products_count }}</td>
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
                            title="Edit kategori">
                            <i class="bx bx-edit text-dark text-sm opacity-10"></i>
                        </a>
                        <a href="#" class="text-dark delete-btn" data-bs-toggle="modal"
                            data-bs-target="#deleteConfirmationModal" data-kategori-slug="{{ $kategori->slug }}"
                            data-kategori-name="{{ $kategori->name }}" title="Hapus kategori">
                            <i class="bx bx-trash"></i>
                        </a>
                    </td>
                </tr>
            @empty
                <tr id="kategori-row-empty">
                    <td colspan="7" class="text-center py-4">
                        <p class="text-dark text-sm fw-bold mb-0">Belum ada data kategori.</p>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
    <div class="my-3 ms-3">{{ $kategoris->onEachSide(2)->links() }}</div>
</div>
