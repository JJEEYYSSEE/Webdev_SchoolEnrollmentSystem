@extends('layouts.student')
@section('title', 'My Schedule')
@section('content')

    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4 pb-2 border-bottom">
        <div>
            <h3 class="fw-bold mb-0 text-dark">My Schedule</h3>
            <p class="text-muted small mb-0">Your weekly class timetable.</p>
        </div>
    </div>

    @if (! $section)
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center text-muted py-5">
                <i class="bi bi-calendar-x fs-1 d-block mb-2 opacity-50"></i>
                You don't have a section yet. Enroll first to get your class schedule.
            </div>
        </div>
    @else
        @if ($enrollment && $enrollment->status !== 'approved')
            <div class="alert alert-info d-flex align-items-center gap-2 border-0 shadow-sm">
                <i class="bi bi-info-circle-fill"></i>
                This schedule is for <strong>{{ $section->displayName() }}</strong> and becomes final once your enrollment is approved.
            </div>
        @endif

        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex flex-wrap gap-3 small text-muted mb-3">
                    <span><i class="bi bi-collection me-1"></i>{{ $section->displayName() }}</span>
                    <span><i class="bi bi-clock-fill me-1"></i>{{ $section->time_period }} session</span>
                </div>

                @include('partials.schedule-grid', ['subjects' => $section->subjects])
            </div>
        </div>
    @endif

@endsection
