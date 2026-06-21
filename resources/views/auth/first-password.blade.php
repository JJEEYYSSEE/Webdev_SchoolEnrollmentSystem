@extends('layouts.app')
@section('title', 'Set Your Password')
@section('content')

    <div class="container py-5" style="max-width: 480px;">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4 p-md-5">
                <div class="text-center mb-3">
                    <div class="rounded-circle bg-primary bg-opacity-10 d-inline-flex align-items-center justify-content-center text-primary" style="width:64px;height:64px;">
                        <i class="bi bi-shield-lock-fill fs-2"></i>
                    </div>
                </div>

                <h5 class="fw-bold text-center mb-1">Set a New Password</h5>
                <p class="text-muted small text-center mb-4">
                    Welcome! For your security, please replace the system default password before continuing.
                </p>

                <form method="post" action="{{ route('password.first.update') }}">
                    @csrf

                    <div class="mb-3">
                        <label for="password" class="form-label fw-semibold small">New Password</label>
                        <div class="input-password-group">
                            <input id="password" type="password" name="password"
                                   class="form-control @error('password') is-invalid @enderror"
                                   placeholder="At least 8 characters" required autofocus autocomplete="new-password">
                            <button type="button" class="password-toggle text-muted" id="togglePw" aria-label="Toggle password visibility">
                                <i class="bi bi-eye" id="togglePwIcon"></i>
                            </button>
                            @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="password_confirmation" class="form-label fw-semibold small">Confirm New Password</label>
                        <input id="password_confirmation" type="password" name="password_confirmation"
                               class="form-control" placeholder="Re-enter your new password" required autocomplete="new-password">
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary btn-lg" data-loading-text="Saving…">
                            <i class="bi bi-check2-circle me-1"></i> Save &amp; Continue
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('togglePw')?.addEventListener('click', function () {
            const input = document.getElementById('password');
            const icon  = document.getElementById('togglePwIcon');
            input.type = input.type === 'password' ? 'text' : 'password';
            icon.classList.toggle('bi-eye');
            icon.classList.toggle('bi-eye-slash');
        });
    </script>

@endsection
