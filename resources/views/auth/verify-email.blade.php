@extends('layouts.guest')
@section('title', 'Verify Email')
@section('content')

    <div class="text-center mb-4">
        <div class="rounded-circle bg-success bg-opacity-10 d-inline-flex align-items-center justify-content-center" style="width: 72px; height: 72px;">
            <i class="bi bi-envelope-check-fill text-success" style="font-size: 2.2rem;"></i>
        </div>
    </div>

    <p class="text-muted small mb-4 text-center">
        Thanks for signing up! Before getting started, please verify your email address by clicking
        the verification link we sent to your inbox. If you didn't receive the email, click the
        button below and we'll send another one.
    </p>

    @if (session('status') == 'verification-link-sent')
        <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 mb-4 border-0 shadow-sm" role="alert">
            <i class="bi bi-check-circle-fill text-success fs-5"></i>
            <div>A new verification link has been sent to your email address.</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="d-grid mb-3">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit" class="btn btn-auth-student w-100 btn-lg">
                <i class="bi bi-send-fill"></i> Resend Verification Email
            </button>
        </form>
    </div>

    <div class="text-center pt-2 border-top">
        <form method="POST" action="{{ route('logout') }}" class="d-inline">
            @csrf
            <button type="submit" class="btn btn-link text-muted p-0 small fw-semibold text-decoration-none">
                <i class="bi bi-box-arrow-right"></i> Log Out
            </button>
        </form>
    </div>

@endsection
