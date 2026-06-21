<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name', 'School Enrollment System'))</title>
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
</head>
<body>

    <nav class="navbar navbar-expand-md navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-2" href="{{ route('dashboard') }}">
                <i class="bi bi-mortarboard-fill" style="font-size:1.1rem;"></i>
                School Enrollment System
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav"
                    aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="mainNav">
                @auth
                    <ul class="navbar-nav ms-auto align-items-md-center gap-1">
                        <li class="nav-item">
                            <span class="nav-link text-white-50 d-flex align-items-center gap-1">
                                <i class="bi bi-person-circle"></i>
                                {{ Auth::user()->name }}
                            </span>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('profile.edit') }}">
                                <i class="bi bi-gear me-1"></i> Profile
                            </a>
                        </li>
                        <li class="nav-item ms-1">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="btn btn-outline-light btn-sm">
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
