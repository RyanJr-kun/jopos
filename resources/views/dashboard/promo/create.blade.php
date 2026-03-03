@extends('layouts/contentNavbarLayout')

@section('title', 'Cards basic - UI elements')
@section('content')
@section('breadcrumb')
    @php
        $breadcrumbItems = [
            ['name' => 'Page', 'url' => '#'],
            ['name' => 'Manajemen Promo & Diskon', 'url' => route('promo.index')],
            ['name' => 'Buat Promo Baru', 'url' => '#'],
        ];
    @endphp
    <x-breadcrumb :items="$breadcrumbItems" />
@endsection

<div class="container-fluid p-3">
    <div class="card rounded-2">
        <div class="card-header pb-0 px-3 pt-2 mb-3">
            <h6 class="mb-0">Buat Promo Baru</h6>
        </div>
        <div class="card-body pt-0">
            <form action="{{ route('promo.store') }}" method="POST">
                @csrf
                @if ($errors->any())
                    <div class="alert alert-danger text-white mt-3" role="alert">
                        <strong class="font-weight-bold">Oops! Terjadi kesalahan:</strong>
                        <ul class="mb-0 ps-3">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @include('dashboard.promo._form')

                <div class="d-flex justify-content-end mt-4">
                    <button type="submit" class="btn btn-outline-info me-2 ">Simpan Promo</button>
                    <a href="{{ route('promo.index') }}" class="btn btn-secondary ">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
