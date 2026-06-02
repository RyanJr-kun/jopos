<!DOCTYPE html>
<html lang="en" class="layout-menu-fixed layout-compact" data-assets-path="{{ asset('/assets') . '/' }}" dir="ltr"
    data-skin="default" data-base-url="{{ url('/') }}" data-framework="laravel" data-bs-theme="light"
    data-template="vertical-menu-template">

<head>
    <meta charset="utf-8" />
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title>
        @yield('title') | {{ config('variables.appName') ? config('variables.appName') : 'AppName' }}
        - {{ config('variables.appSuffix') ? config('variables.appSuffix') : 'AppSuffix' }}
    </title>
    <meta name="description"
        content="{{ config('variables.appDescription') ? config('variables.appDescription') : '' }}" />
    <meta name="keywords" content="{{ config('variables.appKeyword') ? config('variables.appKeyword') : '' }}" />
    <meta property="og:title" content="{{ config('variables.ogTitle') ? config('variables.ogTitle') : '' }}" />
    <meta property="og:type" content="{{ config('variables.ogType') ? config('variables.ogType') : '' }}" />
    <meta property="og:url" content="{{ url()->current() }}" />
    <meta property="og:image" content="{{ config('variables.ogImage') ? config('variables.ogImage') : '' }}" />
    <meta property="og:description"
        content="{{ config('variables.appDescription') ? config('variables.appDescription') : '' }}" />
    <meta property="og:site_name" content="{{ config('variables.appName') ? config('variables.appName') : '' }}" />
    <!-- laravel CRUD token -->
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <!-- Canonical SEO -->
    <link rel="canonical" href="{{ url()->current() }}" />
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('assets/img/favicon/favicon.ico') }}" />

    <!-- Include Styles -->
    @include('layouts/sections/styles')

    <!-- Include Scripts for customizer, helper, analytics, config -->
    @include('layouts/sections/scriptsIncludes')
</head>

<body>
    <!-- Layout Content -->
    @include('components.toast')
    @yield('layoutContent')
    <!--/ Layout Content -->



    <!-- Include Scripts -->
    @include('layouts/sections/scripts')
</body>

</html>
