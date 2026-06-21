@extends('layouts.applicant')
@section('title', 'Application Form')
@section('content')

    @php
        $step  = (int) $application->current_step;
        $steps = [1 => 'Personal Info', 2 => 'Education', 3 => 'Documents', 4 => 'Review'];
    @endphp

    <div class="container py-4" style="max-width: 880px;">

        <div class="text-center mb-4">
            <h3 class="fw-bold mb-1">Grade 11 Application</h3>
            <p class="text-muted small mb-0">Complete all steps to submit your application to {{ config('school.short', 'CISHS') }}.</p>
        </div>

        @if ($application->isInvalid())
            <div class="alert alert-warning d-flex align-items-start gap-2 border-0 shadow-sm">
                <i class="bi bi-exclamation-triangle-fill fs-5"></i>
                <div>
                    <strong>Your application was returned for correction.</strong>
                    <div class="small">{{ $application->remarks ?: 'Please review your details and re-upload the correct documents, then resubmit.' }}</div>
                </div>
            </div>
        @endif

        {{-- Progress bar --}}
        <div class="position-relative mb-4 px-2">
            {{-- connector line behind the circles --}}
            <div class="position-absolute" style="top:18px; left:9%; right:9%; height:2px; background:#dee2e6; z-index:0;"></div>
            <div class="d-flex justify-content-between position-relative" style="z-index:1;">
                @foreach ($steps as $n => $label)
                    <div class="text-center" style="width:90px;">
                        <div class="rounded-circle d-inline-flex align-items-center justify-content-center fw-bold mx-auto
                                    {{ $n < $step ? 'bg-success text-white' : ($n === $step ? 'bg-primary text-white' : 'bg-white text-muted border') }}"
                             style="width: 38px; height: 38px;">
                            @if ($n < $step) <i class="bi bi-check-lg"></i> @else {{ $n }} @endif
                        </div>
                        <div class="small mt-1 {{ $n === $step ? 'fw-bold text-primary' : 'text-muted' }}">{{ $label }}</div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                @if ($step === 1)
                    @include('application.partials.step-personal')
                @elseif ($step === 2)
                    @include('application.partials.step-education')
                @elseif ($step === 3)
                    @include('application.partials.step-documents')
                @else
                    @include('application.partials.step-review')
                @endif
            </div>
        </div>

        <div class="text-center mt-3">
            <form method="POST" action="{{ route('logout') }}" class="d-inline">
                @csrf
                <button class="btn btn-link text-muted small text-decoration-none">
                    <i class="bi bi-box-arrow-right me-1"></i> Save &amp; Log out
                </button>
            </form>
        </div>
    </div>

@endsection
