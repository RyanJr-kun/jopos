@php
    use Illuminate\Support\Facades\Route;
@endphp

<ul class="menu-sub">
    @if ($canView)
        @if (isset($menu))
            @foreach ($menu as $submenu)
                {{-- 1. CEK PERMISSION KHUSUS UNTUK ITEM SUBMENU INI --}}
                @php
                    $canViewSubmenuItem = true; // Default: tampilkan

                    if (isset($submenu->permission)) {
                        $canViewSubmenuItem = auth()->user()->can($submenu->permission);
                    }
                @endphp

                {{-- 2. BUNGKUS LOGIKA ACTIVE DAN RENDER LI DENGAN IF BARU INI --}}
                @if ($canViewSubmenuItem)
                    {{-- active menu method --}}
                    @php
                        $activeClass = null;
                        $active = 'active open';
                        $currentRouteName = Route::currentRouteName();

                        if ($currentRouteName === $submenu->slug) {
                            $activeClass = 'active';
                        } elseif (isset($submenu->submenu)) {
                            if (gettype($submenu->slug) === 'array') {
                                foreach ($submenu->slug as $slug) {
                                    if (
                                        str_contains($currentRouteName, $slug) and
                                        strpos($currentRouteName, $slug) === 0
                                    ) {
                                        $activeClass = $active;
                                    }
                                }
                            } else {
                                if (
                                    str_contains($currentRouteName, $submenu->slug) and
                                    strpos($currentRouteName, $submenu->slug) === 0
                                ) {
                                    $activeClass = $active;
                                }
                            }
                        }
                    @endphp

                    <li class="menu-item {{ $activeClass }}">
                        <a href="{{ isset($submenu->url) ? url($submenu->url) : 'javascript:void(0)' }}"
                            class="{{ isset($submenu->submenu) ? 'menu-link menu-toggle' : 'menu-link' }}"
                            @if (isset($submenu->target) and !empty($submenu->target)) target="_blank" @endif>
                            @if (isset($submenu->icon))
                                <i class="{{ $submenu->icon }}"></i>
                            @endif
                            <div>{{ isset($submenu->name) ? __($submenu->name) : '' }}</div>
                            @isset($submenu->badge)
                                <div class="badge rounded-pill bg-{{ $submenu->badge[0] }} text-uppercase ms-auto">
                                    {{ $submenu->badge[1] }}</div>
                            @endisset
                        </a>

                        {{-- submenu --}}
                        @if (isset($submenu->submenu))
                            @include('layouts.sections.menu.submenu', ['menu' => $submenu->submenu])
                        @endif
                    </li>
                @endif
                {{-- AKHIR DARI PENGECEKAN SUBMENU ITEM --}}
            @endforeach
        @endif
    @endif
</ul>
