<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name', 'School Enrollment System')) — SHS Enrollment</title>
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
</head>
<body class="auth-body @yield('auth-theme', 'auth-body--student')">
    <div class="guest-wrapper">
        <div class="auth-card card border-0 rounded-4 shadow-lg @yield('card-class')">
            {{-- Card header --}}
            <div class="card-header border-0 text-center py-4 bg-transparent text-dark">
                <div class="auth-brand-icon mx-auto @yield('brand-icon-class')">
                    <i class="bi @yield('brand-icon', 'bi-mortarboard-fill')"></i>
                </div>
                <h4 class="fw-bold mb-0 mt-2">@yield('title', 'School Enrollment System')</h4>
                <p class="small text-muted mb-0 mt-1">@yield('auth-subtitle', 'SHS Online Enrollment Portal')</p>
            </div>

            {{-- Card body --}}
            <div class="card-body px-4 pb-4 pt-0">
                @yield('content')
            </div>
        </div>
    </div>

    @include('partials.submit-loading')
</body>
</html>
