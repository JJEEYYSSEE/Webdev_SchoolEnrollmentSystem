@extends('layouts.guest')
@section('title', 'Forgot Password')
@section('content')

    <p class="text-muted small mb-4">
        <i class="bi bi-envelope-open text-success me-1"></i>
        Forgot your password? No problem — enter your registered email address below and we'll
        send you a link to reset it.
    </p>

    @if (session('status'))
        <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 mb-3 border-0 shadow-sm" role="alert">
            <i class="bi bi-check-circle-fill text-success fs-5"></i>
            <div>{{ session('status') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <div class="mb-4">
            <label for="email" class="form-label fw-semibold text-secondary small">Email Address</label>
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0">
                    <i class="bi bi-envelope text-muted"></i>
                </span>
                <input id="email" type="email" name="email" value="{{ old('email') }}"
                       class="form-control border-start-0 ps-1 @error('email') is-invalid @enderror"
                       placeholder="you@example.com" required autofocus>
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="d-grid mb-3">
            <button type="submit" class="btn btn-lg btn-auth-student">
                <i class="bi bi-send-fill"></i> Send Reset Link
            </button>
        </div>

        <p class="text-center small text-muted mb-0 mt-3">
            <a href="{{ route('login') }}" class="text-success fw-bold text-decoration-none">
                <i class="bi bi-arrow-left"></i> Back to Log In
            </a>
        </p>
    </form>

@endsection
