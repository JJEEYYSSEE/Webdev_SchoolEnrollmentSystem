<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="SHS Online Enrollment Portal — Register, enroll, and track your academic records online.">
    <title>{{ config('school.short', 'CISHS') }} — SHS Online Portal</title>
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
</head>
<body class="auth-body auth-body--landing">

    <div class="d-flex flex-column min-vh-100">

        {{-- Hero header --}}
        <header class="text-center pt-5 pb-4 text-white">
            <div class="auth-brand-icon mx-auto bg-white text-dark d-flex align-items-center justify-content-center rounded-circle shadow" style="width: 72px; height: 72px;">
                <i class="bi bi-mortarboard-fill text-success fs-2"></i>
            </div>
            <h1 class="fw-bold mt-3 mb-1 text-white display-6">{{ config('school.name', 'Cabrivex International Senior High School') }}</h1>
            <p class="text-white opacity-75 mb-0">Senior High School — Online Enrollment Portal</p>
            <p class="text-white-50 small mb-0">powered by {{ config('school.platform', 'E-Tala Enrollment System') }}</p>
        </header>

        {{-- Role selection --}}
        <main class="flex-grow-1 d-flex align-items-start align-items-md-center pb-5">
            <div class="container">

                <p class="text-center text-uppercase fw-bold small text-white opacity-75 mb-4"
                   style="letter-spacing: .08em; font-size: .75rem;">
                    Choose how you want to continue
                </p>

                <div class="row justify-content-center g-4">

                    {{-- Student card --}}
                    <div class="col-md-5 col-lg-4">
                        <div class="card role-card h-100 border-0 rounded-4 shadow-lg text-center bg-white text-dark">
                            <div class="card-body p-4 p-md-5">
                                <div class="rounded-circle bg-success bg-opacity-10 d-inline-flex p-4 mb-3"
                                     style="width:80px;height:80px;align-items:center;justify-content:center;">
                                    <i class="bi bi-person-badge-fill text-success" style="font-size: 2rem;"></i>
                                </div>
                                <h4 class="fw-bold mb-2">I'm a Student</h4>
                                <p class="text-muted small mb-4">
                                    Register, enroll for the active semester, and track your grades and records online.
                                </p>
                                <div class="d-grid gap-2">
                                    <a href="{{ route('register') }}" class="btn btn-success btn-lg">
                                        <i class="bi bi-person-plus-fill me-1"></i> Register / Enroll
                                    </a>
                                    <a href="{{ route('login') }}" class="btn btn-outline-secondary btn-sm">
                                        Already have an account? Log in
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Registrar card --}}
                    <div class="col-md-5 col-lg-4">
                        <div class="card role-card h-100 border-0 rounded-4 shadow-lg text-center bg-white text-dark">
                            <div class="card-body p-4 p-md-5">
                                <div class="rounded-circle bg-primary bg-opacity-10 d-inline-flex p-4 mb-3"
                                     style="width:80px;height:80px;align-items:center;justify-content:center;">
                                    <i class="bi bi-shield-lock-fill text-primary" style="font-size: 2rem;"></i>
                                </div>
                                <h4 class="fw-bold mb-2">I'm a Registrar</h4>
                                <p class="text-muted small mb-4">
                                    Manage semesters, sections, subjects, students, and review enrollment applications.
                                </p>
                                <div class="d-grid gap-2">
                                    <a href="{{ route('login', ['portal' => 'registrar']) }}" class="btn btn-primary btn-lg">
                                        <i class="bi bi-box-arrow-in-right me-1"></i> Registrar Log In
                                    </a>
                                    <span class="text-muted small py-1">
                                        <i class="bi bi-info-circle me-1"></i>
                                        Accounts are created by the school admin.
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </main>

        <footer class="text-center text-white-50 small py-4 mt-auto border-top border-white border-opacity-10">
            &copy; {{ date('Y') }} {{ config('school.name', 'Cabrivex International Senior High School') }} &middot; powered by {{ config('school.platform', 'E-Tala Enrollment System') }}
        </footer>

    </div>

</body>
</html>
