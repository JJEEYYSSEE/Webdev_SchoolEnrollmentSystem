<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('school.short', 'CISHS')) — Student Portal</title>
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
</head>
<body class="portal-student">

    {{-- Top Navbar --}}
    <nav class="navbar navbar-expand-md navbar-dark">
        <div class="container-fluid px-3">
            <a class="navbar-brand d-flex align-items-center gap-2" href="{{ route('student.showDashboard') }}"
               title="{{ config('school.name') }}">
                <i class="bi bi-mortarboard-fill" style="font-size:1.3rem;"></i>
                <span class="fw-bold">{{ config('school.short', 'CISHS') }}</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#studentNav"
                    aria-controls="studentNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="studentNav">
                <ul class="navbar-nav ms-auto align-items-md-center gap-2">
                    <li class="nav-item">
                        <span class="nav-link text-white d-flex align-items-center gap-1">
                            <i class="bi bi-person-fill text-white-50"></i>
                            <span class="fw-semibold text-white">{{ Auth::user()?->name ?? 'Guest' }}</span>
                            <span class="badge bg-light text-dark text-uppercase ms-1" style="font-size:0.7rem;">Student</span>
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
            </div>
        </div>
    </nav>

    <div class="d-flex">

        {{-- Sidebar --}}
        <aside class="sidebar d-none d-md-block">
            <p class="sidebar-label">My Menu</p>
            <nav class="nav flex-column gap-1">
                <a href="{{ route('student.showDashboard') }}"
                   class="sidebar-link {{ request()->routeIs('student.showDashboard') ? 'active' : '' }}">
                    <i class="bi bi-speedometer2"></i> Dashboard
                </a>
                <a href="{{ route('student.showEnrollForm') }}"
                   class="sidebar-link {{ request()->routeIs('student.showEnrollForm') ? 'active' : '' }}">
                    <i class="bi bi-file-earmark-text"></i> Enrollment Form
                </a>
                <a href="{{ route('student.showEnrollStatus') }}"
                   class="sidebar-link {{ request()->routeIs('student.showEnrollStatus') ? 'active' : '' }}">
                    <i class="bi bi-hourglass-split"></i> Enrollment Status
                </a>
                <a href="{{ route('student.showSection') }}"
                   class="sidebar-link {{ request()->routeIs('student.showSection') ? 'active' : '' }}">
                    <i class="bi bi-collection"></i> My Section
                </a>
                <a href="{{ route('student.showSchedule') }}"
                   class="sidebar-link {{ request()->routeIs('student.showSchedule') ? 'active' : '' }}">
                    <i class="bi bi-calendar-week"></i> My Schedule
                </a>
                <a href="{{ route('student.showSubjects') }}"
                   class="sidebar-link {{ request()->routeIs('student.showSubjects') ? 'active' : '' }}">
                    <i class="bi bi-book-fill"></i> My Subjects
                </a>
                <a href="{{ route('student.showRecords') }}"
                   class="sidebar-link {{ request()->routeIs('student.showRecords') ? 'active' : '' }}">
                    <i class="bi bi-bar-chart-line-fill"></i> My Records
                </a>
            </nav>

        </aside>

        {{-- Main content --}}
        <div class="flex-grow-1 p-3 p-md-4" style="min-height: calc(100vh - 56px); overflow-x: auto;">
            @yield('content')
        </div>

    </div>

    {{-- Site Footer --}}
    <footer class="portal-footer">
        <span>Powered by <strong>{{ config('school.platform', 'E-Tala Enrollment System') }}</strong></span>
    </footer>

</body>
</html>
