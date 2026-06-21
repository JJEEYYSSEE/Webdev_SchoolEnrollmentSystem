@extends('layouts.app')
@section('title', 'Confirm Password Change')
@section('content')

    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="card shadow-sm">
                    <div class="card-body p-4">
                        <div class="text-center mb-3">
                            <i class="bi bi-shield-lock-fill text-primary" style="font-size: 2.5rem;"></i>
                        </div>

                        <h5 class="fw-bold text-center mb-1">Confirm Password Change</h5>
                        <p class="text-muted small text-center mb-4">
                            We emailed a 6-digit verification code to your address. Enter it below to apply
                            your new password. The code expires in 10 minutes.
                        </p>

                        @if (session('status') === 'password-otp-sent')
                            <div class="alert alert-success d-flex align-items-center gap-2 mb-4">
                                <i class="bi bi-check-circle-fill"></i>
                                A verification code has been sent to your email address.
                            </div>
                        @endif

                        <form method="post" action="{{ route('password.otp.confirm') }}" id="otpForm">
                            @csrf
                            <input type="hidden" name="code" id="otpCode">

                            <div class="mb-2 otp-group">
                                @for ($i = 0; $i < 6; $i++)
                                    <input type="text" inputmode="numeric" maxlength="1"
                                           class="otp-box form-control @error('code') is-invalid @enderror"
                                           autocomplete="one-time-code" aria-label="Digit {{ $i + 1 }}"
                                           {{ $i === 0 ? 'autofocus' : '' }}>
                                @endfor
                            </div>

                            @error('code')
                                <p class="text-danger small text-center mb-3">{{ $message }}</p>
                            @else
                                <div class="mb-3"></div>
                            @enderror

                            <div class="d-grid mb-3">
                                <button type="submit" class="btn btn-primary btn-lg"
                                        data-loading-text="Verifying…">
                                    <i class="bi bi-check2-circle me-1"></i> Confirm &amp; Update Password
                                </button>
                            </div>
                        </form>

                        <div class="d-flex justify-content-between align-items-center">
                            <form method="post" action="{{ route('password.otp.resend') }}" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-link text-muted p-0 small"
                                        data-loading-text="Sending…">
                                    <i class="bi bi-arrow-repeat me-1"></i> Resend code
                                </button>
                            </form>
                            <a href="{{ route('profile.edit') }}" class="btn btn-link text-muted p-0 small">
                                Cancel
                            </a>
                        </div>
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
