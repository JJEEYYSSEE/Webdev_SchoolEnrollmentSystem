<section>
    <div class="d-flex align-items-center gap-2 mb-1">
        <div class="rounded-circle bg-primary bg-opacity-10 p-2 d-flex align-items-center justify-content-center text-primary" style="width: 36px; height: 36px;">
            <i class="bi bi-key-fill fs-5"></i>
        </div>
        <h5 class="fw-bold mb-0 text-dark">Update Password</h5>
    </div>
    <p class="text-muted small mb-2 ms-5">Ensure your account is using a long, random password to stay secure.</p>
    <div class="alert alert-info border-0 d-flex gap-2 ms-md-5 mb-4 p-3 shadow-sm rounded-3">
        <i class="bi bi-shield-lock-fill text-info fs-4"></i>
        <div class="small">
            <strong>Security Notice:</strong> For verification, we will email a 6-digit confirmation code to your address before the changes take effect.
        </div>
    </div>

    <form method="post" action="{{ route('password.update') }}" class="ms-md-5">
        @csrf
        @method('put')

        <div class="mb-3">
            <label for="update_password_current_password" class="form-label fw-semibold small text-muted">Current Password</label>
            <div class="input-password-group">
                <input id="update_password_current_password" name="current_password" type="password"
                       class="form-control @if($errors->updatePassword->has('current_password')) is-invalid @endif"
                       placeholder="Enter your current password"
                       autocomplete="current-password">
                <button type="button" class="password-toggle text-muted" id="toggleCurrentPw"
                        aria-label="Toggle password visibility">
                    <i class="bi bi-eye" id="toggleCurrentPwIcon"></i>
                </button>
                @if ($errors->updatePassword->has('current_password'))
                    <div class="invalid-feedback">{{ $errors->updatePassword->first('current_password') }}</div>
                @endif
            </div>
        </div>

        <div class="mb-3">
            <label for="update_password_password" class="form-label fw-semibold small text-muted">New Password</label>
            <div class="input-password-group">
                <input id="update_password_password" name="password" type="password"
                       class="form-control @if($errors->updatePassword->has('password')) is-invalid @endif"
                       placeholder="At least 8 characters"
                       autocomplete="new-password">
                <button type="button" class="password-toggle text-muted" id="toggleNewPw"
                        aria-label="Toggle password visibility">
                    <i class="bi bi-eye" id="toggleNewPwIcon"></i>
                </button>
                @if ($errors->updatePassword->has('password'))
                    <div class="invalid-feedback">{{ $errors->updatePassword->first('password') }}</div>
                @endif
            </div>
            <p class="password-hint small text-muted mt-2 d-flex align-items-start gap-1">
                <i class="bi bi-info-circle-fill text-success opacity-75"></i>
                <span>Minimum <strong>8 characters</strong> required.</span>
            </p>
        </div>

        <div class="mb-4">
            <label for="update_password_password_confirmation" class="form-label fw-semibold small text-muted">Confirm New Password</label>
            <div class="input-password-group">
                <input id="update_password_password_confirmation" name="password_confirmation" type="password"
                       class="form-control @if($errors->updatePassword->has('password_confirmation')) is-invalid @endif"
                       placeholder="Re-enter your new password"
                       autocomplete="new-password">
                <button type="button" class="password-toggle text-muted" id="toggleConfirmNewPw"
                        aria-label="Toggle password visibility">
                    <i class="bi bi-eye" id="toggleConfirmNewPwIcon"></i>
                </button>
                @if ($errors->updatePassword->has('password_confirmation'))
                    <div class="invalid-feedback">{{ $errors->updatePassword->first('password_confirmation') }}</div>
                @endif
            </div>
        </div>

        <div class="d-flex align-items-center gap-2 mt-4 pt-2">
            <button type="submit" class="btn btn-primary px-4 d-inline-flex align-items-center gap-1">
                <i class="bi bi-envelope-check-fill"></i> Send Code &amp; Save
            </button>
            @if (session('status') === 'password-updated')
                <span class="text-success small d-inline-flex align-items-center gap-1 font-monospace">
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
