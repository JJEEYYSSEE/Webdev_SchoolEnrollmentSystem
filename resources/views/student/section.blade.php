@extends('layouts.student')
@section('title', 'My Section')
@section('content')

    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4 pb-2 border-bottom">
        <div>
            <h3 class="fw-bold mb-0 text-dark">My Section</h3>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('student.showDashboard') }}" class="text-decoration-none">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">My Section</li>
                </ol>
            </nav>
        </div>
    </div>

    @if (! $section)
        <div class="alert alert-info border-0 d-flex gap-3 mb-4 shadow-sm rounded-3">
            <i class="bi bi-info-circle-fill fs-4 text-info"></i>
            <div>
                <div class="fw-bold text-dark">No Section Assigned</div>
                <div class="small text-muted">You don't have an assigned section yet. Complete your enrollment selection first. Your section is finalized once your application is reviewed and approved.</div>
            </div>
        </div>
    @else
        {{-- Section Overview Card --}}
        <div class="card border-0 border-start border-4 border-success shadow-sm mb-4 bg-white">
            <div class="card-body p-4">
                <div class="row align-items-center">
                    <div class="col-md-8 mb-3 mb-md-0 d-flex align-items-center gap-3">
                        <div class="rounded-circle bg-success bg-opacity-10 d-flex align-items-center justify-content-center text-success" style="width: 56px; height: 56px;">
                            <i class="bi bi-people-fill fs-3"></i>
                        </div>
                        <div>
                            <h4 class="fw-bold mb-1 text-dark">{{ $section->section_name }}</h4>
                            <div class="text-muted small">
                                <span>Strand: <strong>{{ $section->strand->strand_code ?? '' }}</strong></span>
                                &middot;
                                <span>Grade: <strong>{{ $section->grade_level }}</strong></span>
                                &middot;
                                <span>Schedule: <strong>{{ $section->time_period }}</strong></span>
                                &middot;
                                <span>Semester: <strong>{{ $section->semester }} Semester</strong></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 text-md-end">
                        @php
                            $badgeClass = match($enrollment->status) {
                                'approved' => 'bg-success',
                                'invalid'  => 'bg-warning text-dark',
                                default    => 'bg-secondary',
                            };
                            $statusIcon = match($enrollment->status) {
                                'approved' => 'bi-check-circle-fill',
                                'invalid'  => 'bi-exclamation-triangle-fill',
                                default    => 'bi-hourglass-split',
                            };
                        @endphp
                        <span class="badge {{ $badgeClass }} px-3 py-2 rounded-pill shadow-sm d-inline-flex align-items-center gap-1.5" style="font-size:0.85rem;">
                            <i class="bi {{ $statusIcon }}"></i>
                            {{ ucfirst($enrollment->status) }}
                        </span>
                        <div class="text-muted small mt-1.5" style="font-size:0.75rem;">School Year {{ $section->schoolYear->year_label ?? '' }}</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Curriculum subjects --}}
        <div class="card border-0 shadow-sm rounded-3 overflow-hidden bg-white">
            <div class="card-header bg-white border-0 py-3">
                <h5 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                    <i class="bi bi-book text-success fs-5"></i>
                    <span>Section Subjects ({{ $section->subjects->count() }})</span>
                </h5>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th class="px-4">Code</th>
                            <th>Subject</th>
                            <th class="text-center px-4">Units</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($section->subjects as $subject)
                            <tr>
                                <td class="px-4 text-muted fw-bold">{{ $subject->subject_code }}</td>
                                <td class="fw-semibold text-dark">{{ $subject->subject_name }}</td>
                                <td class="text-center px-4 fw-bold text-secondary">{{ $subject->units }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

@endsection
