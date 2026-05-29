{{-- ============================================================
     NAVBAR POS
     ============================================================ --}}
<nav class="navbar navbar-main pos-navbar sticky-top border-bottom" aria-label="Navigasi POS" id="pos-navbar">
    <div class="container-fluid align-items-center px-3 gap-2">

        {{-- Logo --}}
        <a href="/dashboard" target="_blank" aria-label="Ke Dashboard" class="flex-shrink-0">
            <img src="{{ asset('assets/img/LM-Default.png') }}" class="navbar-brand-logo" alt="Logo JO Computer">
        </a>

        {{-- Clock Badge --}}
        <div class="badge bg-label-success pos-clock-badge" aria-live="polite" aria-atomic="true">
            <i class="bx bx-clock-fill me-1" aria-hidden="true"></i>
            <span id="realtime-clock" class="fw-bold">Memuat...</span>
        </div>

        {{-- Action Buttons --}}
        <div class="d-flex align-items-center gap-2 ms-auto">

            {{-- Dashboard (desktop only) --}}
            <a href="/dashboard" target="_blank" class="d-none d-lg-block text-decoration-none">
                <button class="btn btn-primary btn-sm px-3 mb-0" type="button">
                    <i class="bx bx-globe me-1" aria-hidden="true"></i>Dashboard
                </button>
            </a>

            <div class="vr d-none d-lg-block opacity-25"></div>

            {{-- Fullscreen --}}
            <button class="btn btn-light d-none d-md-flex align-items-center justify-content-center mb-0" type="button"
                style="width: 32px; height: 32px; padding: 0; border-radius: 8px;" onclick="toggleFullScreen(event)"
                aria-label="Toggle Fullscreen">
                <i class="bx bx-fullscreen" aria-hidden="true"></i>
            </button>

            {{-- Riwayat --}}
            <button class="btn btn-light d-none d-md-flex align-items-center justify-content-center mb-0" type="button"
                style="width: 32px; height: 32px; padding: 0; border-radius: 8px;" data-bs-toggle="modal"
                data-bs-target="#salesHistoryModal" aria-label="Riwayat Penjualan">
                <i class="bx bx-history" aria-hidden="true"></i>
            </button>

            {{-- User Dropdown --}}
            @auth
                <div class="nav-item navbar-dropdown dropdown-user dropdown">
                    <a href="javascript:void(0);" class="nav-link dropdown-toggle hide-arrow p-0" id="userDropdown"
                        data-bs-toggle="dropdown">
                        <div class="avatar avatar-online">
                            @if (auth()->user()->employee?->avatar)
                                <img src="{{ Storage::url(auth()->user()->employee->avatar) }}" alt="Profile"
                                    class="w-px-40 h-auto rounded-circle" style="object-fit: cover; aspect-ratio: 1/1;">
                            @else
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
                                                <img src="{{ Storage::url(auth()->user()->employee->avatar) }}"
                                                    alt="Profile" class="w-px-40 h-auto rounded-circle"
                                                    style="object-fit: cover; aspect-ratio: 1/1;">
                                            @else
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
                                            <small
                                                class="badge py-1 bg-label-{{ $colorClass }} me-1">{{ $role }}</small>
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
                        <li>
                            <a class="dropdown-item" target="_blank" href="{{ config('app.main_web_url') }}">
                                <i class="bx bx-store me-2"></i> Web Market
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item d-md-none" href="{{ route('penjualan.create') }}">
                                <i class="bx bx-tv me-2"></i> Point Of Sales
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
                </div>
            @endauth
        </div>
    </div>
</nav>
