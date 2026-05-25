@extends('layouts/contentNavbarLayout')
@section('title', 'Pusat Notifikasi')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0">
            <span class="text-muted fw-light">Dashboard /</span> Notifikasi Saya
        </h4>
        @if ($totalUnread > 0)
            <span class="badge bg-primary">Ada {{ $totalUnread }} Notifikasi Baru</span>
        @endif
    </div>

    <ul class="nav nav-pills mb-4" id="notif-pills" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" id="pill-all-tab" data-bs-toggle="pill" href="#pill-all" role="tab"
                aria-selected="true">Semua</a>
        </li>
        @foreach ($sections as $section)
            <li class="nav-item">
                <a class="nav-link" id="pill-{{ $section['type'] }}-tab" data-bs-toggle="pill"
                    href="#pill-{{ $section['type'] }}" role="tab" aria-selected="false">
                    {{ $section['title'] }}
                </a>
            </li>
        @endforeach
    </ul>

    <div class="tab-content p-0 bg-transparent shadow-none" id="notif-pills-content">

        <div class="tab-pane fade show active" id="pill-all" role="tabpanel" aria-labelledby="pill-all-tab">
            <div class="row">
                @forelse($sections as $section)
                    @include('content.hrd.setting.section_card', ['section' => $section])
                @empty
                    <div class="col-12 text-center py-5">
                        <img src="{{ asset('assets/img/illustrations/man-with-laptop.png') }}" height="140"
                            alt="No Notifs">
                        <h5 class="mt-3">Semua Bersih!</h5>
                        <p class="text-muted">Tidak ada notifikasi aktif atau semua preferensi dimatikan.</p>
                    </div>
                @endforelse
            </div>
        </div>

        @foreach ($sections as $section)
            <div class="tab-pane fade" id="pill-{{ $section['type'] }}" role="tabpanel"
                aria-labelledby="pill-{{ $section['type'] }}-tab">
                <div class="row">
                    @include('content.hrd.setting.section_card', ['section' => $section])
                </div>
            </div>
        @endforeach

    </div>
@endsection
