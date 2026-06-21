<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('school.short', 'CISHS'))</title>
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
</head>
<body class="{{ Auth::check() && Auth::user()->isRegistrar() ? 'portal-registrar' : 'portal-student' }}">

    {{-- Top Navbar --}}
    <nav class="navbar navbar-expand-md navbar-dark">
        <div class="container-fluid px-3">
            @php
                $dashboardRoute = Auth::check() && Auth::user()->isRegistrar() ? route('registrar.showDashboard') : route('student.showDashboard');
                $roleLabel = Auth::check() && Auth::user()->isRegistrar() ? 'Registrar' : 'Student';
            @endphp
            <a class="navbar-brand d-flex align-items-center gap-2" href="{{ $dashboardRoute }}"
               title="{{ config('school.name') }}">
                <i class="bi bi-mortarboard-fill" style="font-size:1.3rem;"></i>
                <span class="fw-bold">{{ config('school.short', 'CISHS') }}</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav"
                    aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="mainNav">
                @auth
                    <ul class="navbar-nav ms-auto align-items-md-center gap-2">
                        <li class="nav-item">
                            <span class="nav-link text-white d-flex align-items-center gap-1">
                                <i class="bi @if(Auth::user()->isRegistrar()) bi-person-badge-fill @else bi-person-fill @endif text-white-50"></i>
                                <span class="fw-semibold text-white">{{ Auth::user()->name }}</span>
                                <span class="badge bg-light text-dark text-uppercase ms-1" style="font-size:0.7rem;">{{ $roleLabel }}</span>
                            </span>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white-50" href="{{ route('profile.edit') }}" title="Profile Settings">
                                <i class="bi bi-gear-fill"></i>
                            </a>
                        </li>
                        <li class="nav-item ms-md-2">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="btn btn-outline-light btn-sm w-100">
                                    <i class="bi bi-box-arrow-right me-1"></i> Log Out
                                </button>
                            </form>
                        </li>
                    </ul>
                @endauth
            </div>
        </div>
    </nav>

    <main>
        @yield('content')
    </main>

    @include('partials.submit-loading')
</body>
</html>
