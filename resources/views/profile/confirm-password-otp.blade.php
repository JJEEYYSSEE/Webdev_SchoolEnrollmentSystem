@extends(Auth::user()?->isRegistrar() ? 'layouts.registrar' : 'layouts.student')
@section('title', 'Confirm Password Change')
@section('content')

    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4 pb-2 border-bottom">
        <div>
            <h3 class="fw-bold mb-0 text-dark">Confirm Password Change</h3>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item">
                        <a href="{{ Auth::user()?->isRegistrar() ? route('registrar.showDashboard') : route('student.showDashboard') }}" class="text-decoration-none">Home</a>
                    </li>
                    <li class="breadcrumb-item"><a href="{{ route('profile.edit') }}" class="text-decoration-none">Profile Settings</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Confirm OTP</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card border-0 shadow-sm rounded-3 bg-white">
                <div class="card-body p-4">
                    <div class="text-center mb-3">
                        <div class="rounded-circle bg-primary bg-opacity-10 d-inline-flex align-items-center justify-content-center text-primary" style="width: 64px; height: 64px;">
                            <i class="bi bi-shield-lock-fill fs-2"></i>
                        </div>
                    </div>

                    <h5 class="fw-bold text-center mb-2 text-dark">Enter Verification Code</h5>
                    <p class="text-muted small text-center mb-4">
                        We emailed a 6-digit verification code to your address. Enter it below to apply
                        your new password. The code expires in 10 minutes.
                    </p>

                    @if (session('status') === 'password-otp-sent')
                        <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 mb-4 border-0 shadow-sm text-success-emphasis" role="alert">
                            <i class="bi bi-check-circle-fill text-success fs-5"></i>
                            <div>A verification code has been sent to your email address.</div>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form method="post" action="{{ route('password.otp.confirm') }}" id="otpForm">
                        @csrf
                        <input type="hidden" name="code" id="otpCode">

                        <div class="mb-3 otp-group">
                            @for ($i = 0; $i < 6; $i++)
                                <input type="text" inputmode="numeric" maxlength="1"
                                       class="otp-box form-control @error('code') is-invalid @enderror"
                                       autocomplete="one-time-code" aria-label="Digit {{ $i + 1 }}"
                                       {{ $i === 0 ? 'autofocus' : '' }}>
                            @endfor
                        </div>

                        @error('code')
                            <p class="text-danger small text-center mb-3 fw-semibold"><i class="bi bi-exclamation-triangle-fill"></i> {{ $message }}</p>
                        @else
                            <div class="mb-3"></div>
                        @enderror

                        <div class="d-grid mb-3">
                            <button type="submit" class="btn btn-primary btn-lg d-inline-flex align-items-center justify-content-center gap-1.5"
                                    data-loading-text="Verifying…">
                                <i class="bi bi-check2-circle"></i> Confirm &amp; Update Password
                            </button>
                        </div>
                    </form>

                    <div class="d-flex justify-content-between align-items-center mt-3 pt-3 border-top">
                        <form method="post" action="{{ route('password.otp.resend') }}" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-link text-muted p-0 small fw-bold text-decoration-none"
                                    data-loading-text="Sending…">
                                <i class="bi bi-arrow-repeat me-1"></i> Resend code
                            </button>
                        </form>
                        <a href="{{ route('profile.edit') }}" class="btn btn-link text-muted p-0 small fw-semibold text-decoration-none">
                            Cancel
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        (function () {
            var boxes  = Array.prototype.slice.call(document.querySelectorAll('.otp-box'));
            var hidden = document.getElementById('otpCode');
            if (!boxes.length || !hidden) return;

            function sync() {
                hidden.value = boxes.map(function (b) { return b.value; }).join('');
            }

            boxes.forEach(function (box, i) {
                box.addEventListener('input', function () {
                    box.value = box.value.replace(/\D/g, '').slice(0, 1);
                    if (box.value && i < boxes.length - 1) boxes[i + 1].focus();
                    sync();
                });

                box.addEventListener('keydown', function (e) {
                    if (e.key === 'Backspace' && !box.value && i > 0) {
                        boxes[i - 1].focus();
                    } else if (e.key === 'ArrowLeft' && i > 0) {
                        e.preventDefault();
                        boxes[i - 1].focus();
                    } else if (e.key === 'ArrowRight' && i < boxes.length - 1) {
                        e.preventDefault();
                        boxes[i + 1].focus();
                    }
                });

                box.addEventListener('paste', function (e) {
                    e.preventDefault();
                    var text = (e.clipboardData || window.clipboardData).getData('text')
                                  .replace(/\D/g, '').slice(0, 6);
                    for (var j = 0; j < text.length && (i + j) < boxes.length; j++) {
                        boxes[i + j].value = text[j];
                    }
                    sync();
                    boxes[Math.min(i + text.length, boxes.length - 1)].focus();
                });
            });

            document.getElementById('otpForm').addEventListener('submit', sync);
            sync();
        })();
    </script>

@endsection
