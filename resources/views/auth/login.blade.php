@extends('layouts.guest')
@section('title', request('portal') === 'registrar' ? 'Registrar Log In' : 'Log In')
@section('auth-theme', request('portal') === 'registrar' ? 'auth-body--registrar' : 'auth-body--student')
@section('auth-subtitle', request('portal') === 'registrar' ? 'Registrar Portal — Staff Access Only' : 'SHS Online Enrollment Portal')
@section('brand-icon-class', request('portal') === 'registrar' ? 'auth-brand-icon--registrar' : '')
@section('brand-icon', request('portal') === 'registrar' ? 'bi-shield-lock-fill' : 'bi-mortarboard-fill')
@section('content')

    @if (session('status'))
        <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 mb-3 border-0 shadow-sm" role="alert">
            <i class="bi bi-check-circle-fill text-success fs-5"></i>
            <div>{{ session('status') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <form method="POST" action="{{ route('login', request('portal') === 'registrar' ? ['portal' => 'registrar'] : []) }}">
        @csrf

        {{-- School / Staff ID or Email --}}
        @php
            $isRegistrar = request('portal') === 'registrar';
            $idLabel = $isRegistrar ? 'Staff ID or Email' : 'School ID';
            $idPlaceholder = $isRegistrar ? 'e.g. REG-0001' : 'e.g. 2026-00001';
        @endphp
        <div class="mb-3">
            <label for="login" class="form-label fw-semibold text-secondary small">{{ $idLabel }}</label>
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0">
                    <i class="bi bi-person-vcard text-muted"></i>
                </span>
                <input id="login" type="text" name="login" value="{{ old('login') }}"
                       class="form-control border-start-0 ps-1 @error('login') is-invalid @enderror"
                       placeholder="{{ $idPlaceholder }}"
                       required autofocus autocomplete="username">
                @error('login')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            @unless ($isRegistrar)
                <p class="text-muted mt-1 mb-0" style="font-size:.75rem;">
                    Applicants: use your email to log in until your School ID is issued after admission.
                </p>
            @endunless
        </div>

        {{-- Password --}}
        <div class="mb-3">
            <div class="d-flex justify-content-between align-items-center mb-1">
                <label for="password" class="form-label fw-semibold text-secondary small mb-0">Password</label>
                @if (Route::has('password.request'))
                    <a class="small fw-semibold @if(request('portal') === 'registrar') auth-registrar-link @else text-success text-decoration-none @endif"
                       href="{{ route('password.request', request('portal') === 'registrar' ? ['portal' => 'registrar'] : []) }}">
                        Forgot password?
                    </a>
                @endif
            </div>
            <div class="input-password-group">
                <input id="password" type="password" name="password"
                       class="form-control @error('password') is-invalid @enderror"
                       placeholder="Enter your password"
                       required autocomplete="current-password">
                <button type="button" class="password-toggle text-muted" id="toggleLoginPassword"
                        aria-label="Toggle password visibility">
                    <i class="bi bi-eye" id="toggleLoginPasswordIcon"></i>
                </button>
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <p class="password-hint small text-muted mt-2 d-flex align-items-start gap-1">
                <i class="bi bi-info-circle-fill @if(request('portal') === 'registrar') auth-registrar-accent @else text-success @endif opacity-75"></i>
                Your password must be at least 8 characters long.
            </p>
        </div>

        {{-- Remember me --}}
        <div class="mb-4 form-check">
            <input id="remember_me" type="checkbox" name="remember" class="form-check-input">
            <label for="remember_me" class="form-check-label text-muted small">Remember me</label>
        </div>

        {{-- Submit --}}
        <div class="d-grid mb-3">
            @if(request('portal') === 'registrar')
                <button type="submit" class="btn btn-lg btn-auth-registrar">
                    <i class="bi bi-box-arrow-in-right"></i> Log In
                </button>
            @else
                <button type="submit" class="btn btn-lg btn-auth-student">
                    <i class="bi bi-box-arrow-in-right"></i> Log In
                </button>
            @endif
        </div>

        @if(request('portal') !== 'registrar')
            <p class="text-center small text-muted mt-4 mb-0">
                Don't have an account?
                <a href="{{ route('register') }}" class="text-success fw-semibold text-decoration-none">Register here</a>
            </p>
            <p class="text-center small text-muted mt-2 mb-0">
                <a href="{{ route('landing') }}" class="text-success fw-semibold text-decoration-none">
                    <i class="bi bi-arrow-left"></i> Back to portal selection
                </a>
            </p>
        @else
            <p class="text-center small text-muted mt-4 mb-0">
                <a href="{{ route('landing') }}" class="auth-registrar-link fw-semibold">
                    <i class="bi bi-arrow-left"></i> Back to portal selection
                </a>
            </p>
        @endif
    </form>

    <script>
        document.getElementById('toggleLoginPassword')?.addEventListener('click', function () {
            const input = document.getElementById('password');
            const icon  = document.getElementById('toggleLoginPasswordIcon');
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.replace('bi-eye', 'bi-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.replace('bi-eye-slash', 'bi-eye');
            }
        });
    </script>

@endsection
