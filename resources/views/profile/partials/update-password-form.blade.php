<section>
    <h5 class="mb-1 fw-bold">Update Password</h5>
    <p class="text-muted small mb-2">Ensure your account is using a long, random password to stay secure.</p>
    <p class="text-muted small mb-4 d-flex align-items-start gap-1">
        <i class="bi bi-shield-lock text-primary opacity-75"></i>
        For security, we'll email a 6-digit verification code to confirm this change before it takes effect.
    </p>

    <form method="post" action="{{ route('password.update') }}">
        @csrf
        @method('put')

        <div class="mb-3">
            <label for="update_password_current_password" class="form-label fw-semibold">Current Password</label>
            <div class="input-password-group">
                <input id="update_password_current_password" name="current_password" type="password"
                       class="form-control @if($errors->updatePassword->has('current_password')) is-invalid @endif"
                       placeholder="Enter your current password"
                       autocomplete="current-password">
                <button type="button" class="password-toggle" id="toggleCurrentPw"
                        aria-label="Toggle password visibility">
                    <i class="bi bi-eye" id="toggleCurrentPwIcon"></i>
                </button>
                @if ($errors->updatePassword->has('current_password'))
                    <div class="invalid-feedback">{{ $errors->updatePassword->first('current_password') }}</div>
                @endif
            </div>
        </div>

        <div class="mb-3">
            <label for="update_password_password" class="form-label fw-semibold">New Password</label>
            <div class="input-password-group">
                <input id="update_password_password" name="password" type="password"
                       class="form-control @if($errors->updatePassword->has('password')) is-invalid @endif"
                       placeholder="At least 8 characters"
                       autocomplete="new-password">
                <button type="button" class="password-toggle" id="toggleNewPw"
                        aria-label="Toggle password visibility">
                    <i class="bi bi-eye" id="toggleNewPwIcon"></i>
                </button>
                @if ($errors->updatePassword->has('password'))
                    <div class="invalid-feedback">{{ $errors->updatePassword->first('password') }}</div>
                @endif
            </div>
            {{-- Password hint based on actual backend validation: Rules\Password::defaults() = min 8 characters --}}
            <p class="password-hint">
                <i class="bi bi-info-circle-fill text-primary opacity-75"></i>
                Must be at least <strong>8 characters</strong>. Any combination of letters, numbers, or symbols is accepted.
            </p>
        </div>

        <div class="mb-4">
            <label for="update_password_password_confirmation" class="form-label fw-semibold">Confirm New Password</label>
            <div class="input-password-group">
                <input id="update_password_password_confirmation" name="password_confirmation" type="password"
                       class="form-control @if($errors->updatePassword->has('password_confirmation')) is-invalid @endif"
                       placeholder="Re-enter your new password"
                       autocomplete="new-password">
                <button type="button" class="password-toggle" id="toggleConfirmNewPw"
                        aria-label="Toggle password visibility">
                    <i class="bi bi-eye" id="toggleConfirmNewPwIcon"></i>
                </button>
                @if ($errors->updatePassword->has('password_confirmation'))
                    <div class="invalid-feedback">{{ $errors->updatePassword->first('password_confirmation') }}</div>
                @endif
            </div>
        </div>

        <div class="d-flex align-items-center gap-3">
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-floppy me-1"></i> Save Password
            </button>
            @if (session('status') === 'password-updated')
                <span class="text-success small d-flex align-items-center gap-1">
                    <i class="bi bi-check-circle-fill"></i> Saved successfully.
                </span>
            @endif
        </div>
    </form>

    <script>
        function setupPasswordToggle(btnId, inputId, iconId) {
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
        setupPasswordToggle('toggleCurrentPw', 'update_password_current_password', 'toggleCurrentPwIcon');
        setupPasswordToggle('toggleNewPw', 'update_password_password', 'toggleNewPwIcon');
        setupPasswordToggle('toggleConfirmNewPw', 'update_password_password_confirmation', 'toggleConfirmNewPwIcon');
    </script>
</section>
