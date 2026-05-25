<div class="col-12 mb-4">
    <div class="card rounded-2 border-top border-{{ $section['badge_color'] }} border-3">
        <div class="card-header pb-2">
            <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-2">
                    <i class="{{ $section['icon'] }} fs-4 text-{{ $section['badge_color'] }}"></i>
                    <div>
                        <h6 class="mb-0">{{ $section['title'] }}</h6>
                        <p class="text-sm text-muted mb-0">{{ $section['description'] }}</p>
                    </div>
                </div>
                <span class="badge bg-label-{{ $section['badge_color'] }}">{{ count($section['items']) }} Item</span>
            </div>
        </div>

        <div class="card-body px-0 pt-0 pb-2">
            <div class="list-group list-group-flush">
                @forelse ($section['items'] as $item)
                    <div class="list-group-item px-4 py-3">

                        {{-- Logika Rendering untuk Data Inventory (Stok / SN) --}}
                        @if ($section['type'] == 'low_stock' || $section['type'] == 'unregistered_serial')
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center">
                                    <img src="{{ $item['image'] }}" class="rounded me-3" width="48" height="48"
                                        style="object-fit:cover;" alt="product image">
                                    <div class="d-flex flex-column">
                                        <h6 class="mb-1 text-sm">{{ $item['name'] }}</h6>
                                        <div class="d-flex gap-3 text-xs">
                                            @foreach ($item['meta'] as $meta)
                                                <div>{{ $meta['label'] }}: <span
                                                        class="{{ $meta['class'] }}">{{ $meta['value'] }}</span></div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                                {{-- PERBAIKAN: Menggunakan isset($item['action_url']) --}}
                                @if (isset($item['action_url']))
                                    <a href="{{ $item['action_url'] }}"
                                        class="btn {{ $section['action_btn'] }} btn-sm px-3 d-inline-flex align-items-center">
                                        <i class="{{ $section['action_icon'] }} me-md-1"></i><span
                                            class="d-none d-md-inline">{{ $section['action_text'] }}</span>
                                    </a>
                                @endif
                            </div>

                            {{-- Logika Rendering untuk Data Sistem (Database Laravel Notification) --}}
                        @elseif($section['type'] == 'system')
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6
                                        class="mb-1 text-sm fw-semibold {{ is_null($item->read_at) ? 'text-dark' : 'text-muted' }}">
                                        {{ $item->data['title'] ?? 'Pemberitahuan Sistem' }}
                                    </h6>
                                    <p class="mb-1 text-xs text-muted">
                                        {{ $item->data['message'] ?? 'Ada pembaruan pada sistem Anda.' }}</p>
                                    <small class="text-muted" style="font-size: 0.7rem;"><i class="bx bx-time-five"></i>
                                        {{ $item->created_at->diffForHumans() }}</small>
                                </div>
                                @if (is_null($item->read_at))
                                    <span class="badge bg-primary rounded-pill p-1"><span class="visually-hidden">Belum
                                            dibaca</span></span>
                                @endif
                            </div>
                        @endif

                    </div>
                @empty
                    <div class="list-group-item text-center py-4">
                        <p class="text-muted mb-0 fw-bolder text-sm">{{ $section['empty_text'] }}</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
