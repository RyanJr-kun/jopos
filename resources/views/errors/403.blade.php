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
            <h4 class="text-poppins mb-2 mx-2">Access Denied</h4>
            <p class="mb-6 mx-2">You don't have permission to access this page.</p>
            <a href="javascript:history.back()" class="btn btn-primary btn-sm">Kembali</a>
            <div class="mt-6">
                <img src="{{ asset('assets/img/illustrations/kanna-403.jpeg') }}" alt="page-misc-error-light" width="960"
                    class="img-fluid" />
            </div>
        </div>
    </div>
    <!-- /Error -->
@endsection
