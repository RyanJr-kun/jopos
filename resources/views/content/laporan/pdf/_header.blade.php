{{--
    Komponen Header PDF Tiga Kolom
    Menerima:
    - $profilToko: Model Stores
    - $title: Judul Laporan (string)
    - $startDate: Tanggal Mulai (string)
    - $endDate: Tanggal Selesai (string)
--}}

<table class="header-table">
    <tr>
        <td class="col-1" style="vertical-align: middle;">
            {{-- Cek jika logo ada, jika tidak, tampilkan placeholder --}}
            @if ($profilToko && $profilToko->logo)
                {{-- Gunakan public_path() untuk mendapatkan path absolut ke gambar di storage --}}
                <img src="{{ public_path('storage/' . $profilToko->logo) }}" alt="Logo Toko"
                    style="width: 80px; height: auto;">
            @else
                <div
                    style="width: 80px; height: 80px; background-color: #f0f0f0; text-align:center; line-height:80px; font-size:10px; display: inline-block;">
                    Logo</div>
            @endif
        </td>
        <td class="col-11 header-info" colspan="2" style="vertical-align: middle;">
            <h1 style="margin-bottom: 2px;">{{ $profilToko->name_toko ?? 'Nama Toko Anda' }}</h1>
            <p style="margin: 2px 0;">{!! nl2br(e($profilToko->alamat ?? 'Alamat Lengkap Toko')) !!}</p>
            <p style="margin: 2px 0;">Telp: {{ $profilToko->telepon ?? '-' }} | Email: {{ $profilToko->email ?? '-' }}
            </p>
        </td>
    </tr>
    <tr>
        <td class="report-title" colspan="3" style="text-align: center; padding-top: 15px;">
            <h2>{{ $title }}</h2>
            <p>Periode: {{ \Carbon\Carbon::parse($startDate)->isoFormat('D MMM Y') }} -
                {{ \Carbon\Carbon::parse($endDate)->isoFormat('D MMM Y') }}</p>
        </td>
    </tr>
</table>
