<div class="table-responsive p-0">
    <table class="table table-hover align-items-center mb-0">
        <thead>
            <tr class="table-secondary">
                <th class="text-uppercase text-dark text-xs fw-bolder">Supplier</th>
                <th class="text-uppercase text-dark text-xs fw-bolder ps-2">Invoice</th>
                <th class="text-uppercase text-dark text-xs fw-bolder ps-2">Tanggal</th>
                <th class="text-uppercase text-dark text-xs fw-bolder text-center">Status Barang</th>
                <th class="text-uppercase text-dark text-xs fw-bolder ps-2">Total Akhir</th>
                <th class="text-uppercase text-dark text-xs fw-bolder ps-2">Dibayar</th>
                <th class="text-uppercase text-dark text-xs fw-bolder ps-2">Sisa</th>
                <th class="text-uppercase text-dark text-xs fw-bolder text-center">Status Pembayaran</th>
                <th class="text-uppercase text-dark text-xs fw-bolder">Pembuat</th>
                <th class="text-dark"></th>
            </tr>
        </thead>
        <tbody>
            @forelse ($pembelian as $item)
                <tr>
                    <td>
                        <div class="d-flex flex-column justify-content-center ms-3 py-1">
                            <h6 class="mb-0 text-sm">{{ $item->pemasok->name }}</h6>
                        </div>
                    </td>
                    <td>
                        <p class="text-xs font-weight-bold mb-0">{{ $item->referensi }}</p>
                    </td>
                    <td>
                        <p class="text-xs font-weight-bold mb-0">
                            {{ \Carbon\Carbon::parse($item->tanggal_pembelian)->translatedFormat('d M Y') }}</p>
                    </td>
                    <td class="align-middle text-center">
                        <span
                            class="badge badge-sm {{ $item->status_barang == 'Diterima' ? 'badge-success' : ($item->status_barang == 'Dibatalkan' ? 'badge-danger' : 'badge-warning') }}">
                            {{ $item->status_barang }}
                        </span>
                    </td>
                    <td class="align-middle">
                        <span
                            class="text-dark text-xs font-weight-bold">{{ 'Rp ' . number_format($item->total_akhir, 0, ',', '.') }}</span>
                    </td>
                    <td class="align-middle">
                        <span
                            class="text-dark text-xs font-weight-bold">{{ 'Rp ' . number_format($item->jumlah_dibayar, 0, ',', '.') }}</span>
                    </td>
                    <td class="align-middle">
                        <span
                            class="{{ $item->sisa_hutang > 0 ? 'text-danger' : 'text-dark' }} text-xs font-weight-bold">
                            {{ 'Rp ' . number_format($item->sisa_hutang, 0, ',', '.') }}
                        </span>
                    </td>
                    <td class="align-middle text-center text-sm">
                        @php
                            $statusClass = '';
                            if ($item->status_pembayaran == 'Lunas') {
                                $statusClass = 'badge-success';
                            } elseif ($item->status_pembayaran == 'Belum Lunas') {
                                $statusClass = 'badge-warning';
                            } elseif ($item->status_pembayaran == 'Dibatalkan') {
                                $statusClass = 'badge-danger';
                            }
                        @endphp
                        <span class="badge badge-sm {{ $statusClass }}">
                            {{ $item->status_pembayaran }}
                        </span>
                    </td>
                    <td>
                        <div title="foto & name user" class="d-flex align-items-center px-2 py-1">
                            {{-- Tambahkan pengecekan $item->user untuk menghindari error jika user null --}}
                            @if ($item->user && $item->user->img_user)
                                <img src="{{ asset('storage/' . $item->user->img_user) }}"
                                    class="avatar avatar-sm me-3" alt="user_img">
                            @else
                                <img src="{{ asset('assets/img/user.webp') }}" class="avatar avatar-sm me-3"
                                    alt="Gambar User default">
                            @endif
                            <h6 class="mb-0 text-sm">{{ $item->user->name ?? 'User Dihapus' }}</h6>
                        </div>
                    </td>
                    <td class="align-middle text-start">
                        <a href="{{ route('pembelian.show', $item->referensi) }}"
                            class="text-secondary font-weight-bold text-xs px-2" data-bs-toggle="tooltip"
                            data-bs-placement="top" title="Lihat Detail">
                            <i class="fa fa-eye text-dark text-sm opacity-10"></i>
                        </a>
                        <a href="{{ route('pembelian.edit', $item->referensi) }}" class="text-dark mx-3"
                            data-toggle="tooltip" data-original-title="Edit pembelian">
                            <i class="fa fa-pen-to-square text-dark text-sm opacity-10"></i>
                        </a>
                        {{-- Tombol Hapus diubah menjadi Batalkan --}}
                        <a href="#"
                            class="text-dark font-weight-bold text-xs @if ($item->status_pembayaran == 'Dibatalkan') disabled @endif"
                            data-bs-toggle="modal" data-bs-target="#cancelConfirmationModal"
                            data-pembelian-referensi="{{ $item->referensi }}" title="Batalkan Transaksi">
                            <i class="fa fa-ban text-sm opacity-10"></i>
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" class="text-center py-3 ">
                        <p class=" text-dark text-sm fw-bold mb-0">Belum ada data pembelian.</p>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-3 px-3">
    {{ $pembelian->links() }}
</div>
