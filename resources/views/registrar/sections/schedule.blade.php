@extends('layouts.registrar')
@section('title', 'Section Schedule')
@section('content')

    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4 pb-2 border-bottom">
        <div>
            <h3 class="fw-bold mb-0 text-dark">{{ $section->displayName() }}</h3>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('registrar.sections.showSections') }}" class="text-decoration-none">Sections</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Schedule</li>
                </ol>
            </nav>
        </div>
        <form method="POST" action="{{ route('registrar.sections.generateSchedule', $section->id) }}"
              onsubmit="return confirm('Auto-generate the weekly schedule for this section? This replaces any existing schedule.');">
            @csrf
            <button type="submit" class="btn btn-primary d-inline-flex align-items-center gap-1" data-loading-text="Generating…">
                <i class="bi bi-magic"></i> {{ $section->hasSchedule() ? 'Regenerate' : 'Generate' }} Schedule
            </button>
        </form>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 border-0 shadow-sm mb-4" role="alert">
            <i class="bi bi-check-circle-fill text-success fs-5"></i>
            <div>{{ session('success') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="d-flex flex-wrap gap-3 small text-muted mb-3">
                <span><i class="bi bi-tag-fill me-1"></i>{{ optional($section->strand)->strand_code }}</span>
                <span><i class="bi bi-clock-fill me-1"></i>{{ $section->time_period }} session</span>
                <span><i class="bi bi-journal-text me-1"></i>{{ $section->subjects->count() }} subjects</span>
            </div>

            @include('partials.schedule-grid', ['subjects' => $section->subjects])
        </div>
    </div>

@endsection
