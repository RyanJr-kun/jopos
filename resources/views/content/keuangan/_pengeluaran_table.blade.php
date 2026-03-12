<div class="table-responsive p-0 my-3">
    <table class="table table-hover align-items-center justify-content-start mb-0" id="tableData">
        <thead>
            <tr class="table-secondary">
                <th class="text-uppercase text-dark text-xs font-weight-bolder">Referensi</th>
                <th class="text-uppercase text-dark text-xs font-weight-bolder ps-2">Expense</th>
                <th class="text-uppercase text-dark text-xs font-weight-bolder ps-2">Kategori</th>
                {{-- <th class="text-uppercase text-dark text-xs font-weight-bolder ps-2">Detail</th> --}}
                <th class="text-uppercase text-dark text-xs font-weight-bolder ps-2">Tanggal</th>
                <th class="text-uppercase text-dark text-xs font-weight-bolder ps-2">Jumlah</th>
                <th class="text-uppercase text-dark text-xs font-weight-bolder ps-3">Pembuat</th>
                <th class="text-dark"></th>
            </tr>
        </thead>
        <tbody id="isiTable">
            @forelse ($expenses as $expense)
                <tr id="expense-row-{{ $expense->id }}">
                    <td>
                        <p title="referensi" class="ms-3 text-xs text-dark fw-bold mb-0">
                            {{ $expense->referensi ?? '-' }}</p>
                    </td>

                    <td>
                        <p title="keterangan expense" class="text-xs text-dark fw-bold mb-0">
                            {{ $expense->keterangan }}</p>
                    </td>
                    <td>
                        <p title="kategori expense" class="text-xs text-dark fw-bold mb-0">
                            {{ $expense->transaction_category->name }}</p>
                    </td>
                    {{-- <td>
                    <p title="Description" class=" text-xs text-dark fw-bold mb-0">{{ $expense->description ? Str::limit(strip_tags($expense->description), 40) : '-' }}</p>
                </td> --}}
                    <td>
                        <p title="tanggal expense" class="text-xs text-dark fw-bold mb-0">
                            {{ \Carbon\Carbon::parse($expense->tanggal)->isoFormat('D MMM Y') }}</p>
                    </td>
                    <td>
                        <p title="jumlah expense" class="text-xs text-danger fw-bold mb-0">- @money($expense->jumlah)</p>
                    </td>
                    <td>
                        <div title="foto & name user" class="d-flex align-items-center px-2 py-1">
                            @if ($expense->user->img_user)
                                <img src="{{ asset('storage/' . $expense->user->img_user) }}"
                                    class="avatar avatar-sm me-3" alt="user_img">
                            @else
                                <img src="{{ asset('assets/img/user.webp') }}" class="avatar avatar-sm me-3"
                                    alt="Gambar User default">
                            @endif
                            <h6 class="mb-0 text-sm">{{ $expense->user->name }}</h6>
                        </div>
                    </td>

                    <td class="text-center">
                        <a href="#" class="text-dark fw-bold text-xs" data-bs-toggle="modal"
                            data-bs-target="#viewModal" data-url="{{ route('expense.getjson', $expense->referensi) }}"
                            title="Lihat Detail Expense">
                            <i class="bx bx-eye-fill text-dark text-sm opacity-10"></i>
                        </a>
                        <a href="#" class="text-dark fw-bold px-3 text-xs" data-bs-toggle="modal"
                            data-bs-target="#editModal" data-url="{{ route('expense.getjson', $expense->referensi) }}"
                            data-update-url="{{ route('expense.update', $expense->referensi) }}" title="Edit expense">
                            <i class="bx bx-pencil-square text-dark text-sm opacity-10"></i>
                        </a>
                        <a href="#" class="text-dark delete-expense-btn me-md-4" data-bs-toggle="modal"
                            data-bs-target="#deleteConfirmationModal"
                            data-expense-referensi="{{ $expense->referensi }}" {{-- Pastikan ini sudah benar --}}
                            data-expense-name="{{ $expense->keterangan }}" title="Hapus expense">
                            <i class="bx bx-trash"></i>
                        </a>
                    </td>
                </tr>
            @empty
                <tr id="expense-row-empty">
                    <td colspan="7" class="text-center py-3">
                        <p class="text-sm text-dark fw-bold mb-0">Belum ada data expense.</p>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
    <div class="my-3 ms-3">{{ $expenses->onEachSide(1)->links() }}</div>
</div>
