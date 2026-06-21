@extends('layouts.applicant')
@section('title', 'Application Status')
@section('content')

    @php
        $a   = $application;
        $ref = 'APP-'.now()->year.'-'.str_pad($a->id, 5, '0', STR_PAD_LEFT);
    @endphp

    <div class="container py-5" style="max-width: 560px;">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4 p-md-5 text-center">

                @if ($a->isQualified())
                    <div class="rounded-circle bg-success bg-opacity-10 d-inline-flex align-items-center justify-content-center text-success mb-3" style="width:72px;height:72px;">
                        <i class="bi bi-patch-check-fill" style="font-size:2.2rem;"></i>
                    </div>
                    <h4 class="fw-bold mb-2">You're Qualified!</h4>
                    <p class="text-muted mb-4">
                        Congratulations! Your application has been approved. We've emailed your
                        <strong>School ID</strong> and a <strong>default password</strong> — use them to log in
                        and access your dashboard.
                    </p>
                    <a href="{{ route('login') }}" class="btn btn-success">
                        <i class="bi bi-box-arrow-in-right me-1"></i> Go to Login
                    </a>
                @else
                    <div class="rounded-circle bg-primary bg-opacity-10 d-inline-flex align-items-center justify-content-center text-primary mb-3" style="width:72px;height:72px;">
                        <i class="bi bi-hourglass-split" style="font-size:2.2rem;"></i>
                    </div>
                    <h4 class="fw-bold mb-1">Application Submitted</h4>
                    <span class="badge bg-warning-subtle text-warning-emphasis mb-3">Pending Review</span>

                    {{-- Stepper --}}
                    <div class="d-flex justify-content-between align-items-center my-4 px-2">
                        @foreach (['Submitted' => true, 'Under Review' => true, 'Decision' => false] as $label => $done)
                            <div class="text-center flex-fill">
                                <div class="rounded-circle d-inline-flex align-items-center justify-content-center mx-auto {{ $done ? 'bg-primary text-white' : 'bg-light text-muted border' }}" style="width:32px;height:32px;">
                                    @if ($done) <i class="bi bi-check-lg"></i> @else <i class="bi bi-three-dots"></i> @endif
                                </div>
                                <div class="small mt-1 {{ $done ? 'fw-semibold' : 'text-muted' }}">{{ $label }}</div>
                            </div>
                        @endforeach
                    </div>

                    <p class="text-muted small mb-1">Reference No.</p>
                    <p class="fw-bold mb-4">{{ $ref }}</p>
                    <p class="text-muted small mb-0">
                        Thank you! The registrar is reviewing your application and documents.
                        We'll email you once a decision is made.
                    </p>
                @endif

                <div class="mt-4 pt-3 border-top">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="btn btn-link text-muted small text-decoration-none">
                            <i class="bi bi-box-arrow-right me-1"></i> Log Out
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection
