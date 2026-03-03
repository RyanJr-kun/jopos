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
            <li class="nav-item">@php $isPenjualanActive = request()->routeIs('penjualan.*', 'pelanggan.*'); @endphp
                <a data-bs-toggle="collapse" href="#penjualan" class="nav-link {{ $isPenjualanActive ? 'active' : '' }}"
                    aria-controls="penjualan" role="button"
                    aria-expanded="{{ $isPenjualanActive ? 'true' : 'false' }}">
                    <div
                        class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="bx bx-bag-dash text-dark text-sm opacity-10"></i>
                    </div>
                    <span class="nav-link-text ms-1">Penjualan</span>
                </a>
                <div class="collapse {{ $isPenjualanActive ? 'show' : '' }}" id="penjualan">
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

                                <span class="sidenav-normal"> Invoice Penjualan </span>
                            </a>
                        </li>
                        <li class="nav-item ">
                            <a class="nav-link {{ request()->routeIs('pelanggan.*') ? 'active' : '' }}"
                                href="{{ route('pelanggan.index') }}">
                                <span class="sidenav-normal"> Pelanggan </span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>


            @can('is-admin')
                {{-- pembelian --}}
                <li class="nav-item">@php $isPembelianActive = request()->routeIs('pembelian.*', 'pemasok.*'); @endphp
                    <a data-bs-toggle="collapse" href="#pembelian"
                        class="nav-link {{ $isPembelianActive ? 'active' : '' }}" aria-controls="pembelian" role="button"
                        aria-expanded="{{ $isPembelianActive ? 'true' : 'false' }}">
                        <div
                            class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                            <i class="bx bx-bag-plus-fill text-dark text-sm opacity-10"></i>
                        </div>
                        <span class="nav-link-text ms-1">Pembelian</span>
                    </a>
                    <div class="collapse {{ $isPembelianActive ? 'show' : '' }}" id="pembelian">
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
                                    <span class="sidenav-normal"> Invoice Pembelian </span>
                                </a>
                            </li>
                            <li class="nav-item ">
                                <a class="nav-link {{ request()->routeIs('pemasok.*') ? 'active' : '' }}"
                                    href="{{ route('pemasok.index') }}">
                                    <span class="sidenav-normal"> Pemasok </span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
            @endcan

            <hr class="horizontal dark my-2">
            <li class="nav-item">
                <p class="ps-4 mb-0 text-uppercase text-xs font-weight-bolder">Inventaris & Produk</p>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('produk.*') ? 'active' : '' }} "
                    href="{{ route('produk.index') }}">
                    <div
                        class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="bx bx-box-fill text-dark text-sm opacity-10"></i>
                    </div>
                    <span class="nav-link-text ms-1">Produk</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('kategoriproduk.*') ? 'active' : '' }}"
                    href="{{ route('kategoriproduk.index') }}">
                    <div
                        class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="bx bx-bookmarks text-dark text-sm opacity-10"></i>
                    </div>
                    <span class="nav-link-text ms-1">Kategori Produk</span>
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
                    <span class="nav-link-text ms-1">Garansi</span>
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
                    <span class="nav-link-text ms-1">Penyesuaian Stok</span>
                </a>
            </li>
            <li class="nav-item">@php $isStokOpnameActive = request()->routeIs('stok-opname.*'); @endphp
                <a data-bs-toggle="collapse" href="#stokOpname"
                    class="nav-link {{ $isStokOpnameActive ? 'active' : '' }}" aria-controls="stokOpname"
                    role="button" aria-expanded="{{ $isStokOpnameActive ? 'true' : 'false' }}">
                    <div
                        class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="bx bx-menu-button-wide text-dark text-sm opacity-10"></i>
                    </div>
                    <span class="nav-link-text ms-1">Stok Opname</span>
                </a>
                <div class="collapse {{ $isStokOpnameActive ? 'show' : '' }}" id="stokOpname">
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
                    <span class="nav-link-text ms-1">Stok Rendah</span>
                </a>
            </li>
            <hr class="horizontal dark my-2">

            <li class="nav-item">
                <p class="ps-4 mb-0 text-uppercase text-xs font-weight-bolder">Branding & Promo</p>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('promo.*') ? 'active' : '' }} "
                    href="{{ route('promo.index') }}">
                    <div
                        class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="bx bx-percent text-dark text-sm opacity-10"></i>
                    </div>
                    <span class="nav-link-text ms-1">Promo & Diskon</span>
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
            <li class="nav-item">@php $isPemasukanActive = request()->routeIs('keuangan', 'pemasukan.*', 'kategoritransaksi.*', 'pengeluaran.*'); @endphp
                <a data-bs-toggle="collapse" href="#Pemasukan"
                    class="nav-link {{ $isPemasukanActive ? 'active' : '' }}" aria-controls="Pemasukan"
                    role="button" aria-expanded="{{ $isPemasukanActive ? 'true' : 'false' }}">
                    <div
                        class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="bx bx-cash-coin text-dark text-sm opacity-10"></i>
                    </div>
                    <span class="nav-link-text ms-1">Administrasi</span>
                </a>
                <div class="collapse {{ $isPemasukanActive ? 'show' : '' }}" id="Pemasukan">
                    <ul class="nav ms-4">
                        <li class="nav-item ">
                            <a class="nav-link {{ request()->routeIs('keuangan') ? 'active' : '' }}"
                                href="{{ route('keuangan') }}">
                                <span class="sidenav-normal">Administrasi</span>
                            </a>
                        </li>
                        <li class="nav-item ">
                            <a class="nav-link {{ request()->routeIs('pemasukan.*') ? 'active' : '' }}"
                                href="{{ route('pemasukan.index') }}">
                                <span class="sidenav-normal">Pemasukan</span>
                            </a>
                        </li>
                        <li class="nav-item ">
                            <a class="nav-link {{ request()->routeIs('pengeluaran.*') ? 'active' : '' }}"
                                href="{{ route('pengeluaran.index') }}">
                                <span class="sidenav-normal">Pengeluaran</span>
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
                                <span class="sidenav-normal"> Pergerakan Stok</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('laporan.penjualan') ? 'active' : '' }}"
                                href="{{ route('laporan.penjualan') }}">
                                <span class="sidenav-normal"> Laporan Penjualan </span>
                            </a>
                        </li>
                        @can('is-admin')
                            <li class="nav-item ">
                                <a class="nav-link {{ request()->routeIs('laporan.pembelian') ? 'active' : '' }}"
                                    href="{{ route('laporan.pembelian') }}">
                                    <span class="sidenav-normal">Laporan Pembelian</span>
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
