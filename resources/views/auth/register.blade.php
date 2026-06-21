@extends('layouts.guest')
@section('title', 'Student Registration')
@section('card-class', 'auth-card--wide')
@section('content')

    <p class="text-muted small mb-4">Fill in your details below to create your student account and begin enrolling.</p>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        {{-- Personal Information --}}
        <p class="text-uppercase fw-bold small text-muted mb-2" style="letter-spacing:.05em; font-size:.72rem;">
            <i class="bi bi-person me-1"></i> Personal Information
        </p>

        <div class="row g-3 mb-3">
            <div class="col-md-6">
                <label for="first_name" class="form-label fw-semibold">First Name</label>
                <input id="first_name" type="text" name="first_name" value="{{ old('first_name') }}"
                       class="form-control @error('first_name') is-invalid @enderror"
                       placeholder="e.g. Maria" required autofocus>
                @error('first_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-6">
                <label for="last_name" class="form-label fw-semibold">Last Name</label>
                <input id="last_name" type="text" name="last_name" value="{{ old('last_name') }}"
                       class="form-control @error('last_name') is-invalid @enderror"
                       placeholder="e.g. Santos" required>
                @error('last_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-12">
                <label for="email" class="form-label fw-semibold">Email Address</label>
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

            <div class="col-md-6">
                <label for="phone" class="form-label fw-semibold">
                    Phone <span class="text-muted fw-normal">(optional)</span>
                </label>
                <input id="phone" type="text" name="phone" value="{{ old('phone') }}"
                       class="form-control @error('phone') is-invalid @enderror"
                       placeholder="e.g. 09XX-XXX-XXXX">
                @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-6">
                <label for="birthdate" class="form-label fw-semibold">
                    Birthdate <span class="text-muted fw-normal">(optional)</span>
                </label>
                <input id="birthdate" type="date" name="birthdate" value="{{ old('birthdate') }}"
                       class="form-control @error('birthdate') is-invalid @enderror">
                @error('birthdate') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-12">
                <label for="address" class="form-label fw-semibold">
                    Address <span class="text-muted fw-normal">(optional)</span>
                </label>
                <input id="address" type="text" name="address" value="{{ old('address') }}"
                       class="form-control @error('address') is-invalid @enderror"
                       placeholder="Street, Barangay, City">
                @error('address') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
        </div>

        <hr class="my-3">

        {{-- Academic Information --}}
        <p class="text-uppercase fw-bold small text-muted mb-2" style="letter-spacing:.05em; font-size:.72rem;">
            <i class="bi bi-mortarboard me-1"></i> Academic Information
        </p>

        <div class="row g-3 mb-3">
            <div class="col-md-7">
                <label for="strand_id" class="form-label fw-semibold">Strand</label>
                <select id="strand_id" name="strand_id"
                        class="form-select @error('strand_id') is-invalid @enderror" required>
                    <option value="" disabled {{ old('strand_id') ? '' : 'selected' }}>— Select your strand —</option>
                    @foreach ($strands as $strand)
                        <option value="{{ $strand->id }}" {{ old('strand_id') == $strand->id ? 'selected' : '' }}>
                            {{ $strand->strand_code }} — {{ $strand->strand_name }}
                        </option>
                    @endforeach
                </select>
                @error('strand_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-5">
                <label for="grade_level" class="form-label fw-semibold">Grade Level</label>
                <select id="grade_level" name="grade_level"
                        class="form-select @error('grade_level') is-invalid @enderror" required>
                    <option value="" disabled {{ old('grade_level') ? '' : 'selected' }}>— Select grade —</option>
                    <option value="11" {{ old('grade_level') == '11' ? 'selected' : '' }}>Grade 11</option>
                    <option value="12" {{ old('grade_level') == '12' ? 'selected' : '' }}>Grade 12</option>
                </select>
                @error('grade_level') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
        </div>

        <hr class="my-3">

        {{-- Password --}}
        <p class="text-uppercase fw-bold small text-muted mb-2" style="letter-spacing:.05em; font-size:.72rem;">
            <i class="bi bi-lock me-1"></i> Set Your Password
        </p>

        <div class="row g-3">
            <div class="col-md-6">
                <label for="password" class="form-label fw-semibold">Password</label>
                <div class="input-password-group">
                    <input id="password" type="password" name="password"
                           class="form-control @error('password') is-invalid @enderror"
                           placeholder="At least 8 characters"
                           required autocomplete="new-password">
                    <button type="button" class="password-toggle" id="toggleRegPassword"
                            aria-label="Toggle password visibility">
                        <i class="bi bi-eye" id="toggleRegPasswordIcon"></i>
                    </button>
                    @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                {{-- Password hint based on actual backend validation: Rules\Password::defaults() = min 8 chars --}}
                <p class="password-hint">
                    <i class="bi bi-info-circle-fill text-primary opacity-75"></i>
                    Must be at least <strong>8 characters</strong>. Any combination of letters, numbers, or symbols is allowed.
                </p>
            </div>

            <div class="col-md-6">
                <label for="password_confirmation" class="form-label fw-semibold">Confirm Password</label>
                <div class="input-password-group">
                    <input id="password_confirmation" type="password" name="password_confirmation"
                           class="form-control" placeholder="Re-enter your password"
                           required autocomplete="new-password">
                    <button type="button" class="password-toggle" id="toggleRegConfirm"
                            aria-label="Toggle confirm password visibility">
                        <i class="bi bi-eye" id="toggleRegConfirmIcon"></i>
                    </button>
                </div>
                <p class="password-hint">
                    <i class="bi bi-shield-check text-success opacity-75"></i>
                    Must match the password you entered above.
                </p>
            </div>
        </div>

        {{-- Footer --}}
        <div class="d-flex align-items-center justify-content-between mt-4 pt-2 border-top">
            <a class="small text-muted text-decoration-none" href="{{ route('login') }}">
                <i class="bi bi-arrow-left me-1"></i> Already have an account?
            </a>
            <button type="submit" class="btn btn-primary px-4" data-loading-text="Creating account…">
                <i class="bi bi-person-plus me-1"></i> Create Account
            </button>
        </div>
    </form>

    <script>
        // Show/hide password toggles — UI only
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
