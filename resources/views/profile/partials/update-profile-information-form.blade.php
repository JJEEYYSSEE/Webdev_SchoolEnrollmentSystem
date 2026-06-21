<section>
    <div class="d-flex align-items-center gap-2 mb-1">
        <div class="rounded-circle bg-primary bg-opacity-10 p-2 d-flex align-items-center justify-content-center text-primary" style="width: 36px; height: 36px;">
            <i class="bi bi-person-fill fs-5"></i>
        </div>
        <h5 class="fw-bold mb-0 text-dark">Profile Information</h5>
    </div>
    <p class="text-muted small mb-4 ms-5">Update your account's profile information and email address.</p>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="ms-md-5">
        @csrf
        @method('patch')

        <div class="row g-3 mb-3">
            <div class="col-md-6">
                <label for="name" class="form-label fw-semibold small text-muted">Display Name <span class="text-danger">*</span></label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-person text-muted"></i></span>
                    <input id="name" name="name" type="text"
                           class="form-control @error('name') is-invalid @enderror"
                           value="{{ old('name', $user->name) }}" required autofocus autocomplete="name">
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="col-md-6">
                <label for="email" class="form-label fw-semibold small text-muted">Email Address <span class="text-danger">*</span></label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-envelope text-muted"></i></span>
                    <input id="email" name="email" type="email"
                           class="form-control @error('email') is-invalid @enderror"
                           value="{{ old('email', $user->email) }}" required autocomplete="username">
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
            <div class="mt-3 p-3 bg-light rounded-3 border ms-1 mb-3">
                <p class="text-muted small mb-0 d-flex align-items-center gap-2">
                    <i class="bi bi-exclamation-triangle-fill text-warning"></i>
                    <span>Your email address is unverified.</span>
                    <button form="send-verification" class="btn btn-link p-0 small fw-bold text-success text-decoration-none">
                        Click here to re-send the verification email.
                    </button>
                </p>
                @if (session('status') === 'verification-link-sent')
                    <p class="text-success small mt-1.5 mb-0 fw-semibold">
                        <i class="bi bi-check-circle-fill"></i> A new verification link has been sent to your email address.
                    </p>
                @endif
            </div>
        @endif

        <div class="d-flex align-items-center gap-2 mt-4 pt-2">
            <button type="submit" class="btn btn-primary px-4 d-inline-flex align-items-center gap-1">
                <i class="bi bi-floppy"></i> Save Profile
            </button>
            @if (session('status') === 'profile-updated')
                <span class="text-success small d-inline-flex align-items-center gap-1 font-monospace">
                    <i class="bi bi-check-circle-fill"></i> Saved successfully.
                </span>
            @endif
        </div>
    </form>
</section>
