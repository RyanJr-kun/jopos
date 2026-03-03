<div class="table-responsive p-0 mt-3">
    <table class="table table-hover align-items-center justify-content-start mb-0" id="tableData">
        <thead>
            <tr class="table-secondary">
                <th class="text-uppercase text-dark text-xs font-weight-bolder">Referensi</th>
                <th class="text-uppercase text-dark text-xs font-weight-bolder ps-2">Pemasukan</th>
                <th class="text-uppercase text-dark text-xs font-weight-bolder ps-2">Kategori</th>
                <th class="text-uppercase text-dark text-xs font-weight-bolder ps-2">Tanggal</th>
                <th class="text-uppercase text-dark text-xs font-weight-bolder ps-2">Jumlah</th>
                <th class="text-uppercase text-dark text-xs font-weight-bolder ps-3">Pembuat</th>
                <th class="text-dark"></th>
            </tr>
        </thead>
        <tbody id="isiTable">
            @forelse ($pemasukans as $pemasukan)
                <tr id="pemasukan-row-{{ $pemasukan->id }}">
                    <td>
                        <p title="referensi" class="ms-3 text-xs text-dark fw-bold mb-0">
                            {{ $pemasukan->referensi ?? '-' }}</p>
                    </td>

                    <td>
                        <p title="keterangan pemasukan" class="text-xs text-dark fw-bold mb-0">
                            {{ $pemasukan->keterangan }}</p>
                    </td>
                    <td>
                        <p title="kategori pemasukan" class="text-xs text-dark fw-bold mb-0">
                            {{ $pemasukan->kategori_transaksi->nama }}</p>
                    </td>
                    <td>
                        <p title="tanggal pemasukan" class="text-xs text-dark fw-bold mb-0">
                            {{ \Carbon\Carbon::parse($pemasukan->tanggal)->isoFormat('D MMM Y') }}</p>
                    </td>
                    <td>
                        <p title="jumlah pemasukan" class="text-xs text-success fw-bold mb-0">+ @money($pemasukan->jumlah)</p>
                    </td>
                    <td>
                        <div title="foto & nama user" class="d-flex align-items-center px-2 py-1">
                            @if ($pemasukan->user->img_user)
                                <img src="{{ asset('storage/' . $pemasukan->user->img_user) }}"
                                    class="avatar avatar-sm me-3" alt="user_img">
                            @else
                                <img src="{{ asset('assets/img/user.webp') }}" class="avatar avatar-sm me-3"
                                    alt="Gambar User default">
                            @endif
                            <h6 class="mb-0 text-sm">{{ $pemasukan->user->nama }}</h6>
                        </div>

                    </td>

                    <td class="text-center">
                        <a href="#" class="text-dark fw-bold text-xs" data-bs-toggle="modal"
                            data-bs-target="#viewModal"
                            data-url="{{ route('pemasukan.getjson', $pemasukan->referensi) }}"
                            title="Lihat Detail Pemasukan">
                            <i class="bx bx-eye-fill text-dark text-sm opacity-10"></i>
                        </a>
                        <a href="#" class="text-dark fw-bold px-3 text-xs" data-bs-toggle="modal"
                            data-bs-target="#editModal"
                            data-url="{{ route('pemasukan.getjson', $pemasukan->referensi) }}"
                            data-update-url="{{ route('pemasukan.update', $pemasukan->referensi) }}"
                            title="Edit pemasukan">
                            <i class="bx bx-pencil-square text-dark text-sm opacity-10"></i>
                        </a>
                        <a href="#" class="text-dark delete-pemasukan-btn me-md-4" data-bs-toggle="modal"
                            data-bs-target="#deleteConfirmationModal"
                            data-pemasukan-referensi="{{ $pemasukan->referensi }}" {{-- Pastikan ini sudah benar --}}
                            data-pemasukan-name="{{ $pemasukan->keterangan }}" title="Hapus pemasukan">
                            <i class="bx bx-trash"></i>
                        </a>
                    </td>
                </tr>
            @empty
                <tr id="pemasukan-row-empty">
                    <td colspan="7" class="text-center py-3">
                        <p class=" text-dark text-sm fw-bold mb-0">Data pemasukan tidak ditemukan.</p>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
    <div class="my-3 ms-3">{{ $pemasukans->onEachSide(1)->links() }}</div>
</div>
