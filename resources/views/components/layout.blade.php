<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('assets/img/apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('assets/img/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('assets/img/favicon-16x16.png') }}">
    <link rel="manifest" href="{{ asset('assets/img/site.webmanifest') }}">
    <link rel="canonical" href="https://www.jocomputer.com/" />

    <title>Dashboard | JO Computer</title>

    @vite(['resources/scss/app.scss', 'resources/js/app.js'])
    @stack('styles')

    <!-- Fonts and icons -->
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css"
        media="print" onload="this.media='all'">
    <script defer src="https://kit.fontawesome.com/939a218158.js" crossorigin="anonymous"></script>
</head>

<body class="g-sidenav-show bg-gray-100">
    <div class="bg-dark position-absolute w-100" style="min-height: 70px;"></div>
    <x-aside></x-aside>
    {{-- Wrapper for main content and footer to enable sticky footer --}}
    <div class="main-content position-relative border-radius-lg d-flex flex-column min-vh-100">
        <main>
            <nav class="navbar navbar-main navbar-expand-lg px-2 shadow-none border-radius-xl " id="navbarBlur"
                data-scroll="false">
                <div class="container-fluid py-0 px-3">
                    @yield('breadcrumb')
                    <div class="collapse navbar-collapse mt-md-0 mt-2" id="navbar">
                        <div class="ms-md-auto pe-md-3 d-flex align-items-center"></div>
                        <ul class="navbar-nav justify-content-end">
                            <li class="nav-item d-xl-none mb-2 me-3 d-flex align-items-center">
                                <a href="#" class="nav-link text-white p-0" id="iconNavbarSidenav">
                                    <div class="sidenav-toggler-inner">
                                        <i class="sidenav-toggler-line bg-white"></i>
                                        <i class="sidenav-toggler-line bg-white"></i>
                                        <i class="sidenav-toggler-line bg-white"></i>
                                    </div>
                                </a>
                            </li>
                            <li class="nav-item d-none d-md-block me-2">
                                <a href="{{ route('penjualan.create') }}" class="nav-link">
                                    <button class="btn btn-white btn-sm px-3 mb-0">
                                        <i class="bx bx-tv me-2"></i>POS</button>
                                </a>
                            </li>
                            <li class="nav-item dropdown d-flex align-items-center mt-md-2 me-3">
                                <button type="button" class="btn btn-white btn-sm mb-2 position-relative"
                                    style="padding-left: 13px; padding-right: 13px;" id="notificationDropdown"
                                    data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="bx bx-bell-fill cursor-pointer"></i>
                                    <span id="notification-badge"
                                        class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger small py-1 px-2"
                                        style="font-size: 0.6rem; display: none;">
                                        <span class="visually-hidden">unread messages</span>
                                    </span>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end px-2 py-3" id="notification-list"
                                    aria-labelledby="notificationDropdown" style="min-width: 300px;">
                                    <li class="text-center" id="notification-loader">
                                        <div class="spinner-border spinner-border-sm" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                    </li>
                                    {{-- notif di muat lewat js --}}
                                </ul>
                            </li>
                            @auth
                                <li class="nav-item dropdown d-flex align-items-center">
                                    <a href="#" class="nav-link text-dark p-0 mb-2 mb-md-0" id="userDropdown"
                                        data-bs-toggle="dropdown" aria-expanded="false" data-bs-auto-close="outside">
                                        @if (auth()->user()->img_user)
                                            <img src="{{ asset('storage/' . auth()->user()->img_user) }}" alt="Profile"
                                                class="avatar avatar-sm rounded cursor-pointer">
                                        @else
                                            <img src="{{ asset('assets/img/user.webp') }}" class="avatar avatar-sm me-3"
                                                alt="Gambar user default">
                                        @endif
                                    </a>
                                    <ul class="dropdown-menu dropdown-menu-end p-2" aria-labelledby="userDropdown">
                                        <li class="text-start d-flex m-2">
                                            @if (auth()->user()->img_user)
                                                <img src="{{ asset('storage/' . auth()->user()->img_user) }}" alt="Profile"
                                                    class="avatar avatar-sm rounded-circle cursor-pointer">
                                            @else
                                                <img src="{{ asset('assets/img/user.webp') }}" class="avatar avatar-sm me-3"
                                                    alt="Gambar user default">
                                            @endif
                                            <div class="ms-2">
                                                <p class="mb-0 text-xs fw-bolder">{{ auth()->user()->username }}</p>
                                                <p class="text-xs text-secondary">{{ auth()->user()->role->nama }}</p>
                                            </div>
                                        </li>
                                        <li class="d-md-none d-block">
                                            <hr class="horizontal dark mt-n2 mb-2">
                                            <a class="dropdown-item border-radius-md"
                                                href="{{ route('penjualan.create') }}"><i class="bx bx-tv me-2"></i>
                                                Point Of Sales
                                            </a>
                                        </li>
                                        <li>
                                            <hr class="horizontal dark mt-n2 mb-2 d-none d-md-block">
                                            <a class="dropdown-item border-radius-md" href="/">
                                                <i class="bx bx-shop me-2"></i> Web Market
                                            </a>
                                        </li>

                                        <li>
                                            <form action="{{ route('logout') }}" method="post">
                                                @csrf
                                                <button type="submit"
                                                    class="dropdown-item border-radius-md w-100 text-danger"
                                                    style="text-align: left;">
                                                    <i class="bx bx-box-arrow-right me-2"></i>Log Out
                                                </button>
                                            </form>
                                        </li>
                                    </ul>
                                </li>
                            @endauth
                        </ul>
                    </div>
                </div>
            </nav>
            {{ $slot }}
        </main>
        <footer class="footer ps-lg-4 mt-auto mb-3">
            <div class="container-fluid ">
                <div class="row align-items-start">
                    <div class="col-lg-6">
                        <div class="copyright text-start text-sm text-muted text-lg-start">
                            © {{ date('Y') }},
                            made with <i class="bx bx-heart-fill text-dark"></i> by
                            <a href="/" class="font-weight-bold">Ryan Junior</a>
                        </div>
                    </div>
                </div>
            </div>
        </footer>
    </div>
    @stack('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // --- NOTIFICATION LOGIC ---
            const notificationBadge = document.getElementById('notification-badge');
            const notificationList = document.getElementById('notification-list');
            const notificationLoader = document.getElementById('notification-loader');

            async function getLowStockData() {
                try {
                    const response = await fetch("{{ route('get-data.notifications.low-stock') }}"); //
                    if (!response.ok) throw new Error('Gagal memuat notifikasi');
                    return await response.json();
                } catch (error) {
                    console.error('Error fetching notifications:', error);
                    return {
                        count: 0,
                        products: []
                    };
                }
            }

            async function getUnregisteredSerialData() {
                try {
                    const response = await fetch(
                        "{{ route('get-data.notifications.unregistered-serials') }}"); //
                    if (!response.ok) throw new Error('Gagal memuat notifikasi SN.');
                    return await response.json();
                } catch (error) {
                    console.error('Error fetching unregistered serial notifications:', error);
                    return {
                        count: 0,
                        products: []
                    };
                }
            }

            async function fetchAllNotifications() {
                notificationLoader.style.display = 'block';
                notificationList.innerHTML = '';

                const [lowStockData, serialData] = await Promise.all([
                    getLowStockData(),
                    getUnregisteredSerialData()
                ]);

                const totalCount = lowStockData.count + serialData.count;
                let hasNotifications = false;

                if (totalCount > 0) {
                    const badgeContent = totalCount > 9 ? '9+' : totalCount;
                    notificationBadge.textContent = badgeContent;
                    notificationBadge.style.display = 'block';
                } else {
                    notificationBadge.style.display = 'none';
                }

                if (serialData.count > 0) {
                    hasNotifications = true;
                    notificationList.insertAdjacentHTML('beforeend',
                        `<li class="dropdown-header text-xs text-uppercase fw-bolder">Butuh Nomor Seri (${serialData.count})</li>`
                    );
                    serialData.products.forEach(product => {
                        const listItem = `
                                <li>
                                    <a class="dropdown-item border-radius-md" href="${product.url}">
                                        <div class="d-flex py-1">
                                            <div class="my-auto"><img src="${product.img_url}" class="avatar avatar-sm me-3" alt="Product image"></div>
                                            <div class="d-flex flex-column justify-content-center">
                                                <h6 class="text-sm font-weight-normal mb-1">${product.nama_produk}</h6>
                                                <p class="text-xs text-secondary mb-0">
                                                    <i class="bx bx-upc-scan me-1"></i> Butuh <span class="text-danger fw-bold">${product.needed}</span> SN
                                                </p>
                                            </div>
                                        </div>
                                    </a>
                                </li>`;
                        notificationList.insertAdjacentHTML('beforeend', listItem);
                    });
                }

                if (lowStockData.count > 0) {
                    if (hasNotifications) {
                        notificationList.insertAdjacentHTML('beforeend',
                            `<hr class="horizontal dark mt-2 mb-2">`);
                    }
                    hasNotifications = true;
                    notificationList.insertAdjacentHTML('beforeend',
                        `<li class="dropdown-header text-xs text-uppercase fw-bolder">Stok Rendah (${lowStockData.count})</li>`
                    );
                    lowStockData.products.forEach(product => {
                        const listItem = `
                                <li>
                                    <a class="dropdown-item border-radius-md" href="${product.url}">
                                        <div class="d-flex py-1">
                                            <div class="my-auto"><img src="${product.img_url}" class="avatar avatar-sm me-3" alt="Product image"></div>
                                            <div class="d-flex flex-column justify-content-center">
                                                <h6 class="text-sm font-weight-normal mb-1">${product.nama_produk}</h6>
                                                <p class="text-xs text-secondary mb-0">
                                                    <i class="bx bx-box-seam-fill me-1"></i> Sisa <span class="text-danger fw-bold">${product.qty}</span> (Min: ${product.stok_minimum})
                                                </p>
                                            </div>
                                        </div>
                                    </a>
                                </li>`;
                        notificationList.insertAdjacentHTML('beforeend', listItem);
                    });
                    // --- PERUBAHAN: Link "Lihat Semua Stok Rendah" dihilangkan dari sini ---
                }

                if (hasNotifications) {
                    notificationList.insertAdjacentHTML('beforeend', `<hr class="horizontal dark mt-2 mb-2">`);
                    // --- PERUBAHAN: Hanya tombol ini yang akan muncul di paling bawah ---
                    notificationList.insertAdjacentHTML('beforeend',
                        `<li><a class="dropdown-item border-radius-md text-center" href="{{ route('notifications.all') }}"><h6 class="text-sm font-weight-normal mb-0">Lihat Semua Notifikasi</h6></a></li>`
                    ); //
                } else {
                    notificationList.innerHTML =
                        `<li class="text-center text-muted px-3 py-2">Tidak ada notifikasi baru.</li>`;
                }

                notificationLoader.style.display = 'none';
            }

            const notificationDropdown = document.getElementById('notificationDropdown');
            if (notificationDropdown) {
                notificationDropdown.addEventListener('show.bs.dropdown', fetchAllNotifications);
            }
            fetchAllNotifications();
        });
    </script>

</body>

</html>
