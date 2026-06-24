<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('school.short', 'CISHS')) — Application</title>
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
</head>
<body class="portal-student">

    {{-- Minimal applicant navbar — no settings/profile until admitted --}}
    <nav class="navbar navbar-expand-md navbar-dark">
        <div class="container">
            <span class="navbar-brand d-flex align-items-center gap-2" title="{{ config('school.name') }}">
                <i class="bi bi-mortarboard-fill" style="font-size:1.3rem;"></i>
                <span class="fw-bold">{{ config('school.short', 'CISHS') }}</span>
            </span>
            <ul class="navbar-nav ms-auto align-items-md-center gap-2">
                <li class="nav-item">
                    <span class="nav-link text-white d-flex align-items-center gap-1">
                        <i class="bi bi-person-fill text-white-50"></i>
                        <span class="fw-semibold text-white">{{ Auth::user()?->name }}</span>
                        <span class="badge bg-light text-dark text-uppercase ms-1" style="font-size:0.7rem;">Applicant</span>
                    </span>
                </li>
                <li class="nav-item ms-md-2">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="btn btn-outline-light btn-sm">
                            <i class="bi bi-box-arrow-right me-1"></i> Log Out
                        </button>
                    </form>
                </li>
            </ul>
        </div>
    </nav>

    <main>
        @yield('content')
    </main>

    @include('partials.confirm-modal')
    @include('partials.submit-loading')
</body>
</html>
