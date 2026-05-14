@extends('layouts/blankLayout')

@section('title', 'Error - Pages')

@section('page-style')
    <!-- Page -->
    @vite(['resources/assets/vendor/scss/pages/page-misc.scss'])
@endsection


@section('content')
    <!-- Error -->
    <div class="container-fluid bg-white  container-p-y">
        <div class="misc-wrapper">
            <h1 class="mb-2 mx-2" style="line-height: 6rem;font-size: 6rem;">403</h1>
            <h4 class="mb-2 mx-2">Access Denied</h4>
            <p class="mb-6 mx-2">You don't have permission to access this page.</p>
            <a href="javascript:history.back()" class="btn btn-primary">Kembali</a>
            <div class="mt-6">
                <img src="{{ asset('assets/img/illustrations/kanna-403.jpeg') }}" alt="page-misc-error-light" width="960"
                    class="img-fluid" />
            </div>
        </div>
    </div>
    <!-- /Error -->
@endsection
