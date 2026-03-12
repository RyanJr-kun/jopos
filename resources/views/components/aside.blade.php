<aside class="sidenav bg-white navbar navbar-vertical navbar-expand-xs fixed-start " id="sidenav-main">

    {{-- logo --}}
    <div class="sidenav-header">
        <a class="navbar-brand m-0" href="/dashboard" target="_blank">
            <img src="{{ asset('assets/img/logo.svg') }}" width="40px" height="40px" class="navbar-brand-img h-100"
                alt="main_logo">
            <span class="ms-1 font-weight-bold">Computer POS</span>
        </a>
        <i class="bx bx-x-lg p-3 cursor-pointer text-dark opacity-5 position-absolute end-0 top-0 d-xl-none"
            aria-hidden="true" id="iconSidenav"></i>
    </div>
    <hr class="horizontal dark my-2">

    {{-- sidebar content --}}
    <div class="collapse navbar-collapse h-auto pb-5 " id="sidenav-scrollbar">
        <ul class="navbar-nav">

            {{-- Dashboard --}}
            <li class="nav-item">
                <a class="nav-link hover {{ request()->is('dashboard') ? 'active' : '' }} "
                    href="{{ route('dashboard') }}">
                    <div
                        class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="bx bx-tv text-dark text-sm opacity-10"></i>
                    </div>
                    <span class="nav-link-text ms-1">Dashboard</span>
                </a>
            </li>

            {{-- penjualan --}}
            <li class="nav-item">@php $isSaleActive = request()->routeIs('penjualan.*', 'pelanggan.*'); @endphp
                <a data-bs-toggle="collapse" href="#penjualan" class="nav-link {{ $isSaleActive ? 'active' : '' }}"
                    aria-controls="penjualan" role="button" aria-expanded="{{ $isSaleActive ? 'true' : 'false' }}">
                    <div
                        class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="bx bx-bag-dash text-dark text-sm opacity-10"></i>
                    </div>
                    <span class="nav-link-text ms-1">Sale</span>
                </a>
                <div class="collapse {{ $isSaleActive ? 'show' : '' }}" id="penjualan">
                    <ul class="nav ms-4">
                        <li class="nav-item ">
                            <a class="nav-link {{ request()->routeIs('penjualan.create') ? 'active' : '' }}"
                                href="{{ route('penjualan.create') }}">
                                <span class="sidenav-normal"> Kasir </span>
                            </a>
                        </li>
                        <li class="nav-item ">
                            <a class="nav-link {{ request()->routeIs('penjualan.index', 'penjualan.show') ? 'active' : '' }}"
                                href="{{ route('penjualan.index') }}">

                                <span class="sidenav-normal"> Invoice Sale </span>
                            </a>
                        </li>
                        <li class="nav-item ">
                            <a class="nav-link {{ request()->routeIs('pelanggan.*') ? 'active' : '' }}"
                                href="{{ route('pelanggan.index') }}">
                                <span class="sidenav-normal"> Customer </span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>


            @can('is-admin')
                {{-- pembelian --}}
                <li class="nav-item">@php $isPurchaseActive = request()->routeIs('pembelian.*', 'pemasok.*'); @endphp
                    <a data-bs-toggle="collapse" href="#pembelian" class="nav-link {{ $isPurchaseActive ? 'active' : '' }}"
                        aria-controls="pembelian" role="button"
                        aria-expanded="{{ $isPurchaseActive ? 'true' : 'false' }}">
                        <div
                            class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                            <i class="bx bx-bag-plus-fill text-dark text-sm opacity-10"></i>
                        </div>
                        <span class="nav-link-text ms-1">Purchase</span>
                    </a>
                    <div class="collapse {{ $isPurchaseActive ? 'show' : '' }}" id="pembelian">
                        <ul class="nav ms-4">
                            <li class="nav-item ">
                                <a class="nav-link {{ request()->routeIs('pembelian.create') ? 'active' : '' }}"
                                    href="{{ route('pembelian.create') }}">
                                    <span class="sidenav-normal"> Transaksi Baru </span>
                                </a>
                            </li>
                            <li class="nav-item ">
                                <a class="nav-link {{ request()->routeIs('pembelian.index', 'pembelian.show') ? 'active' : '' }}"
                                    href="{{ route('pembelian.index') }}">
                                    <span class="sidenav-normal"> Invoice Purchase </span>
                                </a>
                            </li>
                            <li class="nav-item ">
                                <a class="nav-link {{ request()->routeIs('pemasok.*') ? 'active' : '' }}"
                                    href="{{ route('pemasok.index') }}">
                                    <span class="sidenav-normal"> Supplier </span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
            @endcan

            <hr class="horizontal dark my-2">
            <li class="nav-item">
                <p class="ps-4 mb-0 text-uppercase text-xs font-weight-bolder">Inventaris & Product</p>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('produk.*') ? 'active' : '' }} "
                    href="{{ route('produk.index') }}">
                    <div
                        class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="bx bx-box-fill text-dark text-sm opacity-10"></i>
                    </div>
                    <span class="nav-link-text ms-1">Product</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('kategoriproduk.*') ? 'active' : '' }}"
                    href="{{ route('kategoriproduk.index') }}">
                    <div
                        class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="bx bx-bookmarks text-dark text-sm opacity-10"></i>
                    </div>
                    <span class="nav-link-text ms-1">Kategori Product</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('brand.*') ? 'active' : '' }}"
                    href="{{ route('brand.index') }}">
                    <div
                        class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="bx bx-shop-window text-dark text-sm opacity-10"></i>
                    </div>
                    <span class="nav-link-text ms-1">Brand</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('unit.*') ? 'active' : '' }} "
                    href="{{ route('unit.index') }}">
                    <div
                        class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="bx bx-view-list text-dark text-sm opacity-10"></i>
                    </div>
                    <span class="nav-link-text ms-1">Satuan</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('garansi.*') ? 'active' : '' }}"
                    href="{{ route('garansi.index') }}">
                    <div
                        class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="bx bx-award-fill text-dark text-sm opacity-10"></i>
                    </div>
                    <span class="nav-link-text ms-1">Warrantie</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('serialNumber.*') ? 'active' : '' }}"
                    href="{{ route('serialNumber.index') }}">
                    <div
                        class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="bx bx-tags-fill text-dark text-sm opacity-10"></i>
                    </div>
                    <span class="nav-link-text ms-1">Serial Number</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('stok-penyesuaian.*') ? 'active' : '' }}"
                    href="{{ route('stok-penyesuaian.index') }}">
                    <div
                        class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="bx bx-boxes text-dark text-sm opacity-10"></i>
                    </div>
                    <span class="nav-link-text ms-1">Penyesuaian Stock</span>
                </a>
            </li>
            <li class="nav-item">@php $isStockTakeActive = request()->routeIs('stok-opname.*'); @endphp
                <a data-bs-toggle="collapse" href="#stokOpname"
                    class="nav-link {{ $isStockTakeActive ? 'active' : '' }}" aria-controls="stokOpname"
                    role="button" aria-expanded="{{ $isStockTakeActive ? 'true' : 'false' }}">
                    <div
                        class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="bx bx-menu-button-wide text-dark text-sm opacity-10"></i>
                    </div>
                    <span class="nav-link-text ms-1">Stock Opname</span>
                </a>
                <div class="collapse {{ $isStockTakeActive ? 'show' : '' }}" id="stokOpname">
                    <ul class="nav ms-4">
                        <li class="nav-item ">
                            <a class="nav-link {{ request()->routeIs('stok-opname.index') ? 'active' : '' }}"
                                href="{{ route('stok-opname.index') }}">
                                <span class="sidenav-normal"> Buat Baru </span>
                            </a>
                        </li>
                        <li class="nav-item ">
                            <a class="nav-link {{ request()->routeIs('stok-opname.history', 'stok-opname.show') ? 'active' : '' }}"
                                href="{{ route('stok-opname.history') }}">
                                <span class="sidenav-normal"> Riwayat </span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>

            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('stok.rendah') ? 'active' : '' }}"
                    href="{{ route('stok.rendah') }}">
                    <div
                        class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="bx bx-graph-down-arrow text-dark text-sm opacity-10"></i>
                    </div>
                    <span class="nav-link-text ms-1">Stock Rendah</span>
                </a>
            </li>
            <hr class="horizontal dark my-2">

            <li class="nav-item">
                <p class="ps-4 mb-0 text-uppercase text-xs font-weight-bolder">Branding & Promotion</p>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('promo.*') ? 'active' : '' }} "
                    href="{{ route('promo.index') }}">
                    <div
                        class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="bx bx-percent text-dark text-sm opacity-10"></i>
                    </div>
                    <span class="nav-link-text ms-1">Promotion & Diskon</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('banner.*') ? 'active' : '' }} "
                    href="{{ route('banner.index') }}">
                    <div
                        class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="bx bx-images text-dark text-sm opacity-10"></i>
                    </div>
                    <span class="nav-link-text ms-1">Banner</span>
                </a>
            </li>
            <hr class="horizontal dark my-2">


            {{-- Keuangan dan Laporan --}}
            <li class="nav-item">
                <p class="ps-4 mb-0 text-uppercase text-xs font-weight-bolder">Keuangan dan Laporan</p>
            </li>
            <li class="nav-item">@php $isIncomeActive = request()->routeIs('keuangan', 'income.*', 'kategoritransaksi.*', 'expense.*'); @endphp
                <a data-bs-toggle="collapse" href="#Income" class="nav-link {{ $isIncomeActive ? 'active' : '' }}"
                    aria-controls="Income" role="button" aria-expanded="{{ $isIncomeActive ? 'true' : 'false' }}">
                    <div
                        class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="bx bx-cash-coin text-dark text-sm opacity-10"></i>
                    </div>
                    <span class="nav-link-text ms-1">Administrasi</span>
                </a>
                <div class="collapse {{ $isIncomeActive ? 'show' : '' }}" id="Income">
                    <ul class="nav ms-4">
                        <li class="nav-item ">
                            <a class="nav-link {{ request()->routeIs('keuangan') ? 'active' : '' }}"
                                href="{{ route('keuangan') }}">
                                <span class="sidenav-normal">Administrasi</span>
                            </a>
                        </li>
                        <li class="nav-item ">
                            <a class="nav-link {{ request()->routeIs('income.*') ? 'active' : '' }}"
                                href="{{ route('income.index') }}">
                                <span class="sidenav-normal">Income</span>
                            </a>
                        </li>
                        <li class="nav-item ">
                            <a class="nav-link {{ request()->routeIs('expense.*') ? 'active' : '' }}"
                                href="{{ route('expense.index') }}">
                                <span class="sidenav-normal">Expense</span>
                            </a>
                        </li>
                        <li class="nav-item ">
                            <a class="nav-link {{ request()->routeIs('kategoritransaksi.*') ? 'active' : '' }}"
                                href="{{ route('kategoritransaksi.index') }}">
                                <span class="sidenav-normal">Kategori</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>
            <li class="nav-item">@php $isLaporanActive = request()->routeIs('laporan.*', 'stok.rendah'); @endphp
                <a data-bs-toggle="collapse" href="#laporan" class="nav-link {{ $isLaporanActive ? 'active' : '' }}"
                    aria-controls="laporan" role="button"
                    aria-expanded="{{ $isLaporanActive ? 'true' : 'false' }}">
                    <div
                        class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="bx bx-bar-chart-fill text-dark text-sm opacity-10"></i>
                    </div>
                    <span class="nav-link-text ms-1">Laporan</span>
                </a>
                <div class="collapse {{ $isLaporanActive ? 'show' : '' }}" id="laporan">
                    <ul class="nav ms-4 ps-0">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('laporan.inventaris') ? 'active' : '' }}"
                                href="{{ route('laporan.inventaris') }}">
                                <span class="sidenav-normal"> Pergerakan Stock</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('laporan.penjualan') ? 'active' : '' }}"
                                href="{{ route('laporan.penjualan') }}">
                                <span class="sidenav-normal"> Laporan Sale </span>
                            </a>
                        </li>
                        @can('is-admin')
                            <li class="nav-item ">
                                <a class="nav-link {{ request()->routeIs('laporan.pembelian') ? 'active' : '' }}"
                                    href="{{ route('laporan.pembelian') }}">
                                    <span class="sidenav-normal">Laporan Purchase</span>
                                </a>
                            </li>
                            <li class="nav-item ">
                                <a class="nav-link {{ request()->routeIs('laporan.laba-rugi') ? 'active' : '' }}"
                                    href="{{ route('laporan.laba-rugi') }}">
                                    <span class="sidenav-normal"> Laporan Laba Rugi </span>
                                </a>
                            </li>
                        @endcan
                    </ul>
                </div>
            </li>

            @can('is-admin')
                <hr class="horizontal dark my-2">
                {{-- Autentikasi --}}
                <li class="nav-item">
                    <p class=" ps-4 mb-0 text-uppercase text-xs font-weight-bolder">Autentikasi</p>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }} "
                        href="{{ route('users.index') }}">
                        <div
                            class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                            <i class="bx bx-people-fill text-dark text-sm opacity-10"></i>
                        </div>
                        <span class="nav-link-text ms-1">Users</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('pengaturan.profil-toko.edit*') ? 'active' : '' }}"
                        href="{{ route('pengaturan.profil-toko.edit') }}">
                        <div
                            class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                            <i class="bx bx-gear-fill text-dark text-sm opacity-10"></i>
                        </div>
                        <span class="nav-link-text ms-1">Setting</span>
                    </a>
                </li>
            @endcan
        </ul>
    </div>
</aside>
