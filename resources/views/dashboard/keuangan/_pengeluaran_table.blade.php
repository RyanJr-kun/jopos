<div class="table-responsive p-0 my-3">
    <table class="table table-hover align-items-center justify-content-start mb-0" id="tableData">
        <thead>
            <tr class="table-secondary">
                <th class="text-uppercase text-dark text-xs font-weight-bolder">Referensi</th>
                <th class="text-uppercase text-dark text-xs font-weight-bolder ps-2">Pengeluaran</th>
                <th class="text-uppercase text-dark text-xs font-weight-bolder ps-2">Kategori</th>
                {{-- <th class="text-uppercase text-dark text-xs font-weight-bolder ps-2">Detail</th> --}}
                <th class="text-uppercase text-dark text-xs font-weight-bolder ps-2">Tanggal</th>
                <th class="text-uppercase text-dark text-xs font-weight-bolder ps-2">Jumlah</th>
                <th class="text-uppercase text-dark text-xs font-weight-bolder ps-3">Pembuat</th>
                <th class="text-dark"></th>
            </tr>
        </thead>
        <tbody id="isiTable">
            @forelse ($pengeluarans as $pengeluaran)
                <tr id="pengeluaran-row-{{ $pengeluaran->id }}">
                    <td>
                        <p title="referensi" class="ms-3 text-xs text-dark fw-bold mb-0">
                            {{ $pengeluaran->referensi ?? '-' }}</p>
                    </td>

                    <td>
                        <p title="keterangan pengeluaran" class="text-xs text-dark fw-bold mb-0">
                            {{ $pengeluaran->keterangan }}</p>
                    </td>
                    <td>
                        <p title="kategori pengeluaran" class="text-xs text-dark fw-bold mb-0">
                            {{ $pengeluaran->kategori_transaksi->nama }}</p>
                    </td>
                    {{-- <td>
                    <p title="Deskripsi" class=" text-xs text-dark fw-bold mb-0">{{ $pengeluaran->deskripsi ? Str::limit(strip_tags($pengeluaran->deskripsi), 40) : '-' }}</p>
                </td> --}}
                    <td>
                        <p title="tanggal pengeluaran" class="text-xs text-dark fw-bold mb-0">
                            {{ \Carbon\Carbon::parse($pengeluaran->tanggal)->isoFormat('D MMM Y') }}</p>
                    </td>
                    <td>
                        <p title="jumlah pengeluaran" class="text-xs text-danger fw-bold mb-0">- @money($pengeluaran->jumlah)</p>
                    </td>
                    <td>
                        <div title="foto & nama user" class="d-flex align-items-center px-2 py-1">
                            @if ($pengeluaran->user->img_user)
                                <img src="{{ asset('storage/' . $pengeluaran->user->img_user) }}"
                                    class="avatar avatar-sm me-3" alt="user_img">
                            @else
                                <img src="{{ asset('assets/img/user.webp') }}" class="avatar avatar-sm me-3"
                                    alt="Gambar User default">
                            @endif
                            <h6 class="mb-0 text-sm">{{ $pengeluaran->user->nama }}</h6>
                        </div>
                    </td>

                    <td class="text-center">
                        <a href="#" class="text-dark fw-bold text-xs" data-bs-toggle="modal"
                            data-bs-target="#viewModal"
                            data-url="{{ route('pengeluaran.getjson', $pengeluaran->referensi) }}"
                            title="Lihat Detail Pengeluaran">
                            <i class="bx bx-eye-fill text-dark text-sm opacity-10"></i>
                        </a>
                        <a href="#" class="text-dark fw-bold px-3 text-xs" data-bs-toggle="modal"
                            data-bs-target="#editModal"
                            data-url="{{ route('pengeluaran.getjson', $pengeluaran->referensi) }}"
                            data-update-url="{{ route('pengeluaran.update', $pengeluaran->referensi) }}"
                            title="Edit pengeluaran">
                            <i class="bx bx-pencil-square text-dark text-sm opacity-10"></i>
                        </a>
                        <a href="#" class="text-dark delete-pengeluaran-btn me-md-4" data-bs-toggle="modal"
                            data-bs-target="#deleteConfirmationModal"
                            data-pengeluaran-referensi="{{ $pengeluaran->referensi }}" {{-- Pastikan ini sudah benar --}}
                            data-pengeluaran-name="{{ $pengeluaran->keterangan }}" title="Hapus pengeluaran">
                            <i class="bx bx-trash"></i>
                        </a>
                    </td>
                </tr>
            @empty
                <tr id="pengeluaran-row-empty">
                    <td colspan="7" class="text-center py-3">
                        <p class="text-sm text-dark fw-bold mb-0">Belum ada data pengeluaran.</p>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
    <div class="my-3 ms-3">{{ $pengeluarans->onEachSide(1)->links() }}</div>
</div>
