@props([
    'pemasok' => null,
    'prefix' => '',
])

<div class="row g-3">
    <div class="col-md-6">
        <label for="{{ $prefix }}name" class="form-label">Nama</label>
        <input id="{{ $prefix }}name" name="name" type="text"
            class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $pemasok->name ?? '') }}"
            required>
        @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-6">
        <label for="{{ $prefix }}perusahaan" class="form-label">Perusahaan</label>
        <input id="{{ $prefix }}perusahaan" name="perusahaan" type="text"
            class="form-control @error('perusahaan') is-invalid @enderror"
            value="{{ old('perusahaan', $pemasok->perusahaan ?? '') }}" required>
        @error('perusahaan')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-6">
        <label for="{{ $prefix }}kontak" class="form-label">Kontak</label>
        <input id="{{ $prefix }}kontak" name="kontak" type="text"
            class="form-control @error('kontak') is-invalid @enderror"
            value="{{ old('kontak', $pemasok->kontak ?? '') }}" required>
        @error('kontak')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-6">
        <label for="{{ $prefix }}email" class="form-label">Email</label>
        <input type="email" class="form-control @error('email') is-invalid @enderror" id="{{ $prefix }}email"
            name="email" placeholder="example@gmail.com" value="{{ old('email', $pemasok->email ?? '') }}">
        @error('email')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-12">
        <label for="{{ $prefix }}alamat" class="form-label">Alamat</label>
        <textarea id="{{ $prefix }}alamat" name="alamat" class="form-control @error('alamat') is-invalid @enderror"
            rows="2">{{ old('alamat', $pemasok->alamat ?? '') }}</textarea>
        @error('alamat')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-12">
        <label for="{{ $prefix }}note" class="form-label">Catatan (Opsional)</label>
        <textarea id="{{ $prefix }}note" name="note" class="form-control @error('note') is-invalid @enderror"
            rows="2">{{ old('note', $pemasok->note ?? '') }}</textarea>
        @error('note')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
