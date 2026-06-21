<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name', 'School Enrollment System')) — SHS Enrollment</title>
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
</head>
<body>
    <div class="guest-wrapper">
        <div class="auth-card card @yield('card-class')">
            {{-- Card header with gradient --}}
            <div class="card-header text-center">
                <div class="auth-brand-icon mx-auto">
                    <i class="bi bi-mortarboard-fill"></i>
                </div>
                <h5 class="fw-bold mb-0 mt-1">@yield('title', 'School Enrollment System')</h5>
                <p class="small mb-0 mt-1 opacity-75">SHS Online Enrollment Portal</p>
            </div>

            {{-- Card body --}}
            <div class="card-body">
                @yield('content')
            </div>
        </div>
    </div>

    @include('partials.submit-loading')
</body>
</html>
