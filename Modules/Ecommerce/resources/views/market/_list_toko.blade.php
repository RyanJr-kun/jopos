{{-- FILE: resources/views/content/market/_list_toko.blade.php --}}
@forelse ($stores as $toko)
    <div class="list-group-item list-group-item-action p-4 border-bottom toko-item" style="cursor: pointer;"
        data-map-url="{{ $toko->map_url ?? 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3955.1238542545925!2d110.75378237591431!3d-7.561472674674966!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e7a14f83e78f24b%3A0x76f6f20de70e8d57!2sJO%20Computer!5e0!3m2!1sen!2sid!4v1760027885984!5m2!1sen!2sid' }}"
        data-lat="{{ $toko->latitude }}" data-lng="{{ $toko->longitude }}" data-name="{{ $toko->name_toko }}"
        onclick="updateMapByCoords(this)">

        <h5 class="fw-bolder mb-2 text-dark">{{ $toko->name_toko }} - {{ $toko->kecamatan }}</h5>

        <div class="d-flex align-items-start mb-2 small text-muted">
            <i class="bx bx-location-fill fs-4 me-3"></i>
            <div>{{ $toko->alamat ?? 'Alamat belum diatur.' }}</div>
        </div>

        <div class="d-flex flex-column flex-md-row align-items-start gap-3 mt-2">
            <div class="d-flex align-items-start small text-muted">
                <i class="bx bxl-whatsapp fs-4 me-2"></i>
                <div>
                    <strong class="d-block text-dark">Whatsapp</strong>
                    <a href="https://wa.me/{{ $toko->telepon ?? '' }}" class="text-light fs-7" target="_blank"
                        onclick="event.stopPropagation();">
                        {{ $toko->telepon ?? 'Nomor belum diatur.' }}
                    </a>
                </div>
            </div>

            <div class="d-flex align-items-start small text-muted">
                <i class="bx bx-envelope fs-4 me-2"></i>
                <div>
                    <strong class="d-block text-dark">Email</strong>
                    <a href="mailto:{{ $toko->email ?? '' }}" class="text-light fs-7"
                        onclick="event.stopPropagation();">
                        {{ $toko->email ?? 'Email belum diatur.' }}
                    </a>
                </div>
            </div>
        </div>
    </div>
@empty
    <div class="p-5 text-center text-muted">
        <i class="bx bx-store-alt fs-1 mb-2"></i>
        <p class="mb-0">Tidak ada toko yang cocok dengan pencarian Anda.</p>
    </div>
@endforelse
