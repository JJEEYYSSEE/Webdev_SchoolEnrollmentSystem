<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="SHS Online Enrollment Portal — Register, enroll, and track your academic records online.">
    <title>{{ config('school.short', 'CISHS') }} — SHS Online Portal</title>
    @include('partials.icon-head')
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
</head>
<body class="auth-body auth-body--landing">

    {{-- Decorative background orbs --}}
    <div class="landing-orbs" aria-hidden="true">
        <span class="landing-orb landing-orb--green"></span>
        <span class="landing-orb landing-orb--blue"></span>
        <span class="landing-orb landing-orb--teal"></span>
    </div>

    <div class="d-flex flex-column min-vh-100 position-relative">

        {{-- ── Hero ─────────────────────────────────────────────────────────── --}}
        <header class="landing-hero text-center pt-5 pb-3">
            <div class="container">

                {{-- Logo badge --}}
                <div class="landing-logo mx-auto mb-4">
                    <i class="bi bi-mortarboard-fill"></i>
                </div>

                {{-- Pill badge --}}
                <span class="landing-pill mb-3 d-inline-block">
                    <i class="bi bi-stars me-1"></i> SHS Online Enrollment Portal
                </span>

                {{-- School name --}}
                <h1 class="landing-school-name fw-black mt-3 mb-2">
                    {{ config('school.name', 'Cabrivex International Senior High School') }}
                </h1>
                <p class="landing-school-sub mb-0">
                    Senior High School &mdash; Academic Records &amp; Enrollment System
                </p>
            </div>
        </header>

        {{-- ── Role Selection ───────────────────────────────────────────────── --}}
        <main class="flex-grow-1 d-flex align-items-center py-4 py-md-5">
            <div class="container">

                <p class="landing-choose-label text-center mb-4">
                    <i class="bi bi-arrow-down-circle me-1 opacity-75"></i>
                    Choose how you want to continue
                </p>

                <div class="row justify-content-center g-4">

                    {{-- Student card --}}
                    <div class="col-sm-10 col-md-5 col-lg-4">
                        <div class="landing-role-card landing-role-card--student h-100">
                            <div class="landing-card-icon landing-card-icon--student">
                                <i class="bi bi-person-badge-fill"></i>
                            </div>
                            <h2 class="landing-card-title mt-3 mb-2">I'm a Student</h2>
                            <p class="landing-card-desc">
                                Register, enroll for the active semester, and track your grades and records online.
                            </p>
                            <div class="landing-card-actions mt-auto pt-3">
                                <a href="{{ route('register') }}"
                                   class="btn landing-btn-student w-100 mb-2">
                                    <i class="bi bi-person-plus-fill"></i> Register / Enroll
                                </a>
                                <a href="{{ route('login') }}"
                                   class="btn landing-btn-ghost w-100">
                                    Already have an account? Log in
                                </a>
                            </div>
                        </div>
                    </div>

                    {{-- Registrar card --}}
                    <div class="col-sm-10 col-md-5 col-lg-4">
                        <div class="landing-role-card landing-role-card--registrar h-100">
                            <div class="landing-card-icon landing-card-icon--registrar">
                                <i class="bi bi-shield-lock-fill"></i>
                            </div>
                            <h2 class="landing-card-title mt-3 mb-2">I'm a Registrar</h2>
                            <p class="landing-card-desc">
                                Manage semesters, sections, subjects, students, and review enrollment applications.
                            </p>
                            <div class="landing-card-actions mt-auto pt-3">
                                <a href="{{ route('login', ['portal' => 'registrar']) }}"
                                   class="btn landing-btn-registrar w-100 mb-2">
                                    <i class="bi bi-box-arrow-in-right"></i> Registrar Log In
                                </a>
                                <p class="landing-admin-note mb-0">
                                    <i class="bi bi-info-circle"></i>
                                    Accounts are created by the school admin.
                                </p>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </main>

        {{-- ── Footer ───────────────────────────────────────────────────────── --}}
        <footer class="landing-footer text-center py-4">
            <p class="mb-1 small text-white-50">
                &copy; {{ date('Y') }} {{ config('school.name', 'Cabrivex International Senior High School') }}
            </p>
            <p class="mb-0 small" style="color: rgba(255,255,255,.3); font-size: .75rem;">
                Powered by <strong style="color: rgba(255,255,255,.45);">{{ config('school.platform', 'E-Tala Enrollment System') }}</strong>
            </p>
        </footer>

    </div>

</body>
</html>
