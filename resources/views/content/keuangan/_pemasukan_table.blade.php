<div class="table-responsive p-0 mt-3">
    <table class="table table-hover align-items-center justify-content-start mb-0" id="tableData">
        <thead>
            <tr class="table-secondary">
                <th class="text-uppercase text-dark text-xs font-weight-bolder">Referensi</th>
                <th class="text-uppercase text-dark text-xs font-weight-bolder ps-2">Income</th>
                <th class="text-uppercase text-dark text-xs font-weight-bolder ps-2">Kategori</th>
                <th class="text-uppercase text-dark text-xs font-weight-bolder ps-2">Tanggal</th>
                <th class="text-uppercase text-dark text-xs font-weight-bolder ps-2">Jumlah</th>
                <th class="text-uppercase text-dark text-xs font-weight-bolder ps-3">Pembuat</th>
                <th class="text-dark"></th>
            </tr>
        </thead>
        <tbody id="isiTable">
            @forelse ($incomes as $income)
                <tr id="income-row-{{ $income->id }}">
                    <td>
                        <p title="referensi" class="ms-3 text-xs text-dark fw-bold mb-0">
                            {{ $income->referensi ?? '-' }}</p>
                    </td>

                    <td>
                        <p title="keterangan income" class="text-xs text-dark fw-bold mb-0">
                            {{ $income->keterangan }}</p>
                    </td>
                    <td>
                        <p title="kategori income" class="text-xs text-dark fw-bold mb-0">
                            {{ $income->transaction_category->name }}</p>
                    </td>
                    <td>
                        <p title="tanggal income" class="text-xs text-dark fw-bold mb-0">
                            {{ \Carbon\Carbon::parse($income->tanggal)->isoFormat('D MMM Y') }}</p>
                    </td>
                    <td>
                        <p title="jumlah income" class="text-xs text-success fw-bold mb-0">+ @money($income->jumlah)</p>
                    </td>
                    <td>
                        <div title="foto & name user" class="d-flex align-items-center px-2 py-1">
                            @if ($income->user->img_user)
                                <img src="{{ asset('storage/' . $income->user->img_user) }}"
                                    class="avatar avatar-sm me-3" alt="user_img">
                            @else
                                <img src="{{ asset('assets/img/user.webp') }}" class="avatar avatar-sm me-3"
                                    alt="Gambar User default">
                            @endif
                            <h6 class="mb-0 text-sm">{{ $income->user->name }}</h6>
                        </div>

                    </td>

                    <td class="text-center">
                        <a href="#" class="text-dark fw-bold text-xs" data-bs-toggle="modal"
                            data-bs-target="#viewModal" data-url="{{ route('income.getjson', $income->referensi) }}"
                            title="Lihat Detail Income">
                            <i class="bx bx-eye-fill text-dark text-sm opacity-10"></i>
                        </a>
                        <a href="#" class="text-dark fw-bold px-3 text-xs" data-bs-toggle="modal"
                            data-bs-target="#editModal" data-url="{{ route('income.getjson', $income->referensi) }}"
                            data-update-url="{{ route('income.update', $income->referensi) }}" title="Edit income">
                            <i class="bx bx-edit text-dark text-sm opacity-10"></i>
                        </a>
                        <a href="#" class="text-dark delete-income-btn me-md-4" data-bs-toggle="modal"
                            data-bs-target="#deleteConfirmationModal" data-income-referensi="{{ $income->referensi }}"
                            {{-- Pastikan ini sudah benar --}} data-income-name="{{ $income->keterangan }}" title="Hapus income">
                            <i class="bx bx-trash"></i>
                        </a>
                    </td>
                </tr>
            @empty
                <tr id="income-row-empty">
                    <td colspan="7" class="text-center py-3">
                        <p class=" text-dark text-sm fw-bold mb-0">Data income tidak ditemukan.</p>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
    <div class="my-3 ms-3">{{ $incomes->onEachSide(1)->links() }}</div>
</div>
