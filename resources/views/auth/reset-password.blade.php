@extends('layouts.guest')
@section('title', 'Reset Password')
@section('content')

    <p class="text-muted small mb-4">
        <i class="bi bi-lock-fill text-success me-1"></i>
        Choose a strong new password for your account.
    </p>

    <form method="POST" action="{{ route('password.store') }}">
        @csrf

        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        {{-- Email --}}
        <div class="mb-3">
            <label for="email" class="form-label fw-semibold text-secondary small">Email Address</label>
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0">
                    <i class="bi bi-envelope text-muted"></i>
                </span>
                <input id="email" type="email" name="email"
                       value="{{ old('email', $request->email) }}"
                       class="form-control border-start-0 ps-1 @error('email') is-invalid @enderror"
                       required autofocus autocomplete="username">
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        {{-- New Password --}}
        <div class="mb-3">
            <label for="password" class="form-label fw-semibold text-secondary small">New Password</label>
            <div class="input-password-group">
                <input id="password" type="password" name="password"
                       class="form-control @error('password') is-invalid @enderror"
                       placeholder="At least 8 characters"
                       required autocomplete="new-password">
                <button type="button" class="password-toggle text-muted" id="toggleResetPassword"
                        aria-label="Toggle password visibility">
                    <i class="bi bi-eye" id="toggleResetPasswordIcon"></i>
                </button>
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <p class="password-hint small text-muted mt-2 d-flex align-items-start gap-1">
                <i class="bi bi-info-circle-fill text-success opacity-75"></i>
                <span>Minimum <strong>8 characters</strong> required.</span>
            </p>
        </div>

        {{-- Confirm Password --}}
        <div class="mb-4">
            <label for="password_confirmation" class="form-label fw-semibold text-secondary small">Confirm Password</label>
            <div class="input-password-group">
                <input id="password_confirmation" type="password" name="password_confirmation"
                       class="form-control @error('password_confirmation') is-invalid @enderror"
                       placeholder="Re-enter new password"
                       required autocomplete="new-password">
                <button type="button" class="password-toggle text-muted" id="toggleResetConfirm"
                        aria-label="Toggle confirm password visibility">
                    <i class="bi bi-eye" id="toggleResetConfirmIcon"></i>
                </button>
                @error('password_confirmation')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="d-grid mb-3">
            <button type="submit" class="btn btn-lg btn-auth-student">
                <i class="bi bi-check2-circle"></i> Reset Password
            </button>
        </div>
    </form>

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
        setupToggle('toggleResetPassword', 'password', 'toggleResetPasswordIcon');
        setupToggle('toggleResetConfirm', 'password_confirmation', 'toggleResetConfirmIcon');
    </script>

@endsection
