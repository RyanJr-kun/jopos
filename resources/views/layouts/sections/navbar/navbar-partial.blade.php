@php
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Facades\Route;
@endphp

<!--  Brand demo (display only for navbar-full and hide on below xl) -->
@if (isset($navbarFull))
    <div class="navbar-brand app-brand demo d-none d-xl-flex py-0 me-4">
        <a href="{{ url('/') }}" class="app-brand-link gap-2">
            <span class="app-brand-logo demo">@include('_partials.macros')</span>
            <span
                class="app-brand-text demo menu-text fw-bold text-heading">{{ config('variables.templateName') }}</span>
        </a>
    </div>
@endif

<!-- ! Not required for layout-without-menu -->
@if (!isset($navbarHideToggle))
    <div
        class="layout-menu-toggle navbar-nav align-items-xl-center me-4 me-xl-0 {{ isset($contentNavbar) ? ' d-xl-none ' : '' }}">
        <a class="nav-item nav-link px-0 me-xl-6" href="javascript:void(0)">
            <i class="icon-base bx bx-menu icon-md"></i>
        </a>
    </div>
@endif

<div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
    <!-- Search -->
    <div class="navbar-nav align-items-center">
        <div class="nav-item d-flex align-items-center">
            <i class="icon-base bx bx-search icon-md"></i>
            <input type="text" class="form-control border-0 shadow-none ps-1 ps-sm-2" placeholder="Search..."
                aria-label="Search...">
        </div>
    </div>
    <!-- /Search -->
    <ul class="navbar-nav flex-row align-items-center ms-auto">
        {{-- link ke kasir --}}
        <li class="nav-item d-none d-md-block me-2">
            <a href="{{ route('penjualan.create') }}" class="nav-link badge bg-label-primary py-2 fw-bold">
                <i class="bx bx-tv me-2 "></i>POS
            </a>
        </li>

        {{-- notifikasi --}}
        <li class="nav-item dropdown-notifications navbar-dropdown dropdown me-3 me-xl-2">
            <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown"
                data-bs-auto-close="outside" aria-expanded="false">
                <span class="position-relative">
                    <i class="icon-base bx bx-bell icon-md"></i>
                    @if ($unreadCount > 0)
                        <span class="badge rounded-pill bg-danger badge-dot badge-notifications border"></span>
                    @endif
                </span>
            </a>
            <ul class="dropdown-menu dropdown-menu-end p-0">
                <li class="dropdown-menu-header border-bottom">
                    <div class="dropdown-header d-flex align-items-center py-3">
                        <h6 class="mb-0 me-auto">Notification</h6>
                        <div class="d-flex align-items-center h6 mb-0">
                            <span class="badge bg-label-primary me-2">{{ $unreadCount }} New</span>
                            {{-- Tombol Mark All Read {{ route('notifikasi.readAll') }} --}}
                            <a href="#" class="dropdown-notifications-all p-2" data-bs-toggle="tooltip"
                                data-bs-placement="top" title="Mark all as read">
                                <i class="icon-base bx bx-envelope-open text-heading"></i>
                            </a>
                        </div>
                    </div>
                </li>
                <li class="dropdown-notifications-list scrollable-container ps">
                    <ul class="list-group list-group-flush">
                        @forelse($notifications as $notif)
                            <li
                                class="list-group-item list-group-item-action dropdown-notifications-item {{ $notif->read_at ? '' : 'marked-as-read' }}">
                                <a href="{{ $notif->data['url'] ?? '#' }}" class="d-flex">
                                    <div class="flex-shrink-0 me-3">
                                        <div class="avatar">
                                            <span
                                                class="avatar-initial rounded-circle bg-label-{{ $notif->data['color'] ?? 'primary' }}">
                                                <i class="bx {{ $notif->data['icon'] ?? 'bx-bell' }}"></i>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="small mb-0">{{ $notif->data['title'] ?? 'Notifikasi' }}</h6>
                                        <small
                                            class="mb-1 d-block text-body">{{ $notif->data['message'] ?? '' }}</small>
                                        <small
                                            class="text-body-secondary">{{ $notif->created_at->diffForHumans() }}</small>
                                    </div>
                                    <div class="flex-shrink-0 dropdown-notifications-actions">
                                        @if (!$notif->read_at)
                                            <span class="badge badge-dot bg-primary"></span>
                                        @endif
                                    </div>
                                </a>
                            </li>
                        @empty
                            <li class="list-group-item text-center small text-muted py-4">
                                Tidak ada notifikasi baru.
                            </li>
                        @endforelse
                    </ul>
                </li>
                <li class="border-top">
                    <div class="d-grid p-4">
                        {{-- {{ route('notifikasi') }} --}}
                        <a class="btn btn-primary btn-sm d-flex" href="{{ route('notifications.all') }}">
                            <small class="align-middle">Lihat Semua Notifikasi</small>
                        </a>
                    </div>
                </li>
            </ul>
        </li>

        <!-- User -->
        {{-- <li class="nav-item navbar-dropdown dropdown-user dropdown">
            <a class="nav-link dropdown-toggle hide-arrow p-0" href="javascript:void(0);" data-bs-toggle="dropdown">
                <div class="avatar avatar-online">
                    <img src="{{ asset('assets/img/avatars/1.png') }}" alt class="w-px-40 h-auto rounded-circle">
                </div>
            </a>
            <ul class="dropdown-menu dropdown-menu-end">
                <li>
                    <a class="dropdown-item" href="javascript:void(0);">
                        <div class="d-flex">
                            <div class="flex-shrink-0 me-3">
                                <div class="avatar avatar-online">
                                    <img src="{{ asset('assets/img/avatars/1.png') }}" alt
                                        class="w-px-40 h-auto rounded-circle">
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-0">John Doe</h6>
                                <small class="text-muted">Admin</small>
                            </div>
                        </div>
                    </a>
                </li>
                <li>
                    <div class="dropdown-divider my-1"></div>
                </li>
                <li>
                    <a class="dropdown-item" href="javascript:void(0);">
                        <i class="icon-base bx bx-user icon-md me-3"></i><span>My Profile</span>
                    </a>
                </li>
                <li>
                    <a class="dropdown-item" href="javascript:void(0);">
                        <i class="icon-base bx bx-cog icon-md me-3"></i><span>Settings</span>
                    </a>
                </li>
                <li>
                    <a class="dropdown-item" href="javascript:void(0);">
                        <span class="d-flex align-items-center align-middle">
                            <i class="flex-shrink-0 icon-base bx bx-credit-card icon-md me-3"></i><span
                                class="flex-grow-1 align-middle">Billing Plan</span>
                            <span class="flex-shrink-0 badge rounded-pill bg-danger">4</span>
                        </span>
                    </a>
                </li>
                <li>
                    <div class="dropdown-divider my-1"></div>
                </li>
                <li>
                    <a class="dropdown-item" href="javascript:void(0);">
                        <i class="icon-base bx bx-power-off icon-md me-3"></i><span>Log Out</span>
                    </a>
                </li>
            </ul>
        </li> --}}
        <!--/ User -->
        @auth
            <li class="nav-item navbar-dropdown dropdown-user dropdown">
                <a href="javascript:void(0);" class="nav-link dropdown-toggle hide-arrow p-0" id="userDropdown"
                    data-bs-toggle="dropdown">
                    <div class="avatar avatar-online">
                        @if (auth()->user()->employee?->avatar)
                            {{-- Jika user punya relasi employee DAN punya avatar --}}
                            <img src="{{ Storage::url(auth()->user()->employee->avatar) }}" alt="Profile"
                                class="w-px-40 h-auto rounded-circle" style="object-fit: cover; aspect-ratio: 1/1;">
                        @else
                            {{-- Jika tidak punya avatar, tampilkan inisial nama --}}
                            <span class="avatar-initial rounded-circle bg-label-primary flex-shrink-0">
                                {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                            </span>
                        @endif
                    </div>
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                        <a class="dropdown-item" href="javascript:void(0);">
                            <div class="d-flex">
                                <div class="flex-shrink-0 me-3">
                                    <div class="avatar avatar-online">
                                        @if (auth()->user()->employee?->avatar)
                                            {{-- Jika user punya relasi employee DAN punya avatar --}}
                                            <img src="{{ Storage::url(auth()->user()->employee->avatar) }}" alt="Profile"
                                                class="w-px-40 h-auto rounded-circle"
                                                style="object-fit: cover; aspect-ratio: 1/1;">
                                        @else
                                            {{-- Jika tidak punya avatar, tampilkan inisial nama --}}
                                            <span class="avatar-initial rounded-circle bg-label-primary flex-shrink-0">
                                                {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="grow">
                                    <h6 class="mb-0">{{ Auth::user()->name ?? 'user' }}</h6>
                                    @php
                                        $roleColors = [
                                            'admin' => 'danger',
                                            'kasir' => 'primary',
                                            'teknisi' => 'success',
                                            'pelayan' => 'info',
                                            'Magang' => 'warning',
                                            'Manajer' => 'dark',
                                        ];
                                    @endphp
                                    @forelse(Auth::user()->getRoleNames() as $role)
                                        @php $colorClass = $roleColors[strtolower($role)] ?? 'primary'; @endphp
                                        <small class="badge py-1 bg-label-{{ $colorClass }} me-1">{{ $role }}
                                        </small>
                                    @empty
                                        <span class="text-muted small">Tanpa Role</span>
                                    @endforelse
                                </div>
                            </div>
                        </a>
                    </li>
                    <li>
                        <div class="dropdown-divider my-1"></div>
                    </li>
                    <li class="d-md-none d-block">
                        <a class="dropdown-item" href="{{ route('penjualan.create') }}">
                            <i class="icon-base bx bx-user icon-md me-3"></i><span> Point Of Sales</span>
                        </a>
                    </li>
                    <li class="d-md-none d-block">
                        <hr class="horizontal dark mt-n2 mb-2">
                        <a class="dropdown-item border-radius-md" href="{{ route('penjualan.create') }}"><i
                                class="bx bx-tv me-2"></i>
                            Point Of Sales
                        </a>
                    </li>
                    <li>

                        <a class="dropdown-item border-radius-md" href="/">
                            <i class="bx bx-bxs-shopping-bag me-2"></i> Web Market
                        </a>
                    </li>

                    <li>
                        <form action="{{ route('employee.logout') }}" method="post">
                            @csrf

                            <button type="submit" class="dropdown-item text-danger">
                                <i class="icon-base bx bx-power-off icon-md me-3"></i>Log Out
                            </button>
                        </form>
                    </li>
                </ul>
            </li>
        @endauth
    </ul>
</div>
