@extends('layouts.guest')
@section('title', 'Student Registration')
@section('content')

    <p class="text-muted small mb-4 text-center">Create your account to start your Grade 11 application. You'll complete the full application form after verifying your email.</p>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        {{-- Personal Information --}}
        <h6 class="text-uppercase fw-bold text-success mb-3 d-flex align-items-center gap-1.5" style="letter-spacing:.05em; font-size:.8rem;">
            <i class="bi bi-person-fill fs-5"></i> Account Details
        </h6>

        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <label for="first_name" class="form-label fw-semibold text-secondary small">First Name <span class="text-danger">*</span></label>
                <input id="first_name" type="text" name="first_name" value="{{ old('first_name') }}"
                       class="form-control @error('first_name') is-invalid @enderror"
                       placeholder="e.g. Maria" required autofocus>
                @error('first_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-6">
                <label for="last_name" class="form-label fw-semibold text-secondary small">Last Name <span class="text-danger">*</span></label>
                <input id="last_name" type="text" name="last_name" value="{{ old('last_name') }}"
                       class="form-control @error('last_name') is-invalid @enderror"
                       placeholder="e.g. Santos" required>
                @error('last_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-12">
                <label for="email" class="form-label fw-semibold text-secondary small">Email Address <span class="text-danger">*</span></label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0">
                        <i class="bi bi-envelope text-muted"></i>
                    </span>
                    <input id="email" type="email" name="email" value="{{ old('email') }}"
                           class="form-control border-start-0 ps-1 @error('email') is-invalid @enderror"
                           placeholder="you@example.com" required autocomplete="username">
                    @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="col-12">
                <label for="birthdate" class="form-label fw-semibold text-secondary small">Birthdate <span class="text-danger">*</span></label>
                <input id="birthdate" type="date" name="birthdate" value="{{ old('birthdate') }}"
                       class="form-control @error('birthdate') is-invalid @enderror" required>
                @error('birthdate') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
        </div>

        {{-- Password --}}
        <h6 class="text-uppercase fw-bold text-success mb-3 d-flex align-items-center gap-1.5 pt-2 border-top" style="letter-spacing:.05em; font-size:.8rem;">
            <i class="bi bi-lock-fill fs-5"></i> Set Your Password
        </h6>

        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <label for="password" class="form-label fw-semibold text-secondary small">Password <span class="text-danger">*</span></label>
                <div class="input-password-group">
                    <input id="password" type="password" name="password"
                           class="form-control @error('password') is-invalid @enderror"
                           placeholder="At least 8 characters"
                           required autocomplete="new-password">
                    <button type="button" class="password-toggle text-muted" id="toggleRegPassword"
                            aria-label="Toggle password visibility">
                        <i class="bi bi-eye" id="toggleRegPasswordIcon"></i>
                    </button>
                    @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <p class="password-hint small text-muted mt-2 d-flex align-items-start gap-1">
                    <i class="bi bi-info-circle-fill text-success opacity-75"></i>
                    <span>Minimum <strong>8 characters</strong> required.</span>
                </p>
            </div>

            <div class="col-md-6">
                <label for="password_confirmation" class="form-label fw-semibold text-secondary small">Confirm Password <span class="text-danger">*</span></label>
                <div class="input-password-group">
                    <input id="password_confirmation" type="password" name="password_confirmation"
                           class="form-control" placeholder="Re-enter password"
                           required autocomplete="new-password">
                    <button type="button" class="password-toggle text-muted" id="toggleRegConfirm"
                            aria-label="Toggle confirm password visibility">
                        <i class="bi bi-eye" id="toggleRegConfirmIcon"></i>
                    </button>
                </div>
                <p class="password-hint small text-muted mt-2 d-flex align-items-start gap-1">
                    <i class="bi bi-shield-check text-success opacity-75"></i>
                    <span>Must match the password entered on the left.</span>
                </p>
            </div>
        </div>

        {{-- Footer --}}
        <div class="d-flex align-items-center justify-content-between mt-4 pt-3 border-top flex-wrap gap-2">
            <a class="small text-decoration-none text-success fw-bold" href="{{ route('login') }}">
                <i class="bi bi-arrow-left"></i> Already have an account? Log In
            </a>
            <button type="submit" class="btn btn-auth-student px-4" data-loading-text="Creating account…">
                <i class="bi bi-person-plus-fill"></i> Create Account
            </button>
        </div>
    </form>

    <p class="text-center small text-muted mt-4 mb-0">
        <a href="{{ route('landing') }}" class="text-success fw-semibold text-decoration-none">
            <i class="bi bi-arrow-left"></i> Back to portal selection
        </a>
    </p>

    <script>
        function setupToggle(btnId, inputId, iconId) {
            document.getElementById(btnId)?.addEventListener('click', function () {
                const input = document.getElementById(inputId);
                const icon  = document.getElementById(iconId);
                if (input.type === 'password') {
                    input.type = 'text';
                    icon.classList.replace('bi-eye', 'bi-eye-slash');
                } else {
                    input.type = 'password';
                    icon.classList.replace('bi-eye-slash', 'bi-eye');
                }
            });
        }
        setupToggle('toggleRegPassword', 'password', 'toggleRegPasswordIcon');
        setupToggle('toggleRegConfirm', 'password_confirmation', 'toggleRegConfirmIcon');
    </script>

@endsection
