@extends('layouts.registrar')
@section('title', 'Student Detail')
@section('content')

    {{-- Page Header --}}
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-4 pb-2 border-bottom">
        <div class="d-flex align-items-center gap-3">
            <a href="{{ route('registrar.showStudents') }}" class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center gap-1">
                <i class="bi bi-arrow-left"></i> <span>Back</span>
            </a>
            <div>
                <h4 class="fw-bold mb-0 text-dark">{{ $student->first_name }} {{ $student->last_name }}</h4>
                <span class="text-muted small">Student No: <strong>{{ $student->student_number }}</strong></span>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        {{-- Profile Card --}}
        <div class="col-md-8">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header bg-white border-0 py-3 fw-bold text-dark d-flex align-items-center gap-2">
                    <i class="bi bi-person-badge-fill text-primary fs-5"></i>
                    <span>Student Profile</span>
                </div>
                <div class="card-body pt-0">
                    <dl class="row mb-0 gy-2">
                        <dt class="col-sm-4 text-muted small text-uppercase">Student No.</dt>
                        <dd class="col-sm-8 fw-semibold text-dark">{{ $student->student_number }}</dd>

                        <dt class="col-sm-4 text-muted small text-uppercase">Full Name</dt>
                        <dd class="col-sm-8 fw-semibold text-dark">{{ $student->first_name }} {{ $student->last_name }}</dd>

                        <dt class="col-sm-4 text-muted small text-uppercase">Strand / Grade</dt>
                        <dd class="col-sm-8 text-dark">
                            <span class="badge bg-light text-dark fw-bold border">{{ $student->strand->strand_code ?? '—' }}</span>
                            &middot; Grade {{ $student->grade_level }}
                        </dd>

                        <dt class="col-sm-4 text-muted small text-uppercase">Email</dt>
                        <dd class="col-sm-8 text-dark">{{ $student->user->email ?? '—' }}</dd>

                        <dt class="col-sm-4 text-muted small text-uppercase">Phone</dt>
                        <dd class="col-sm-8 text-dark">{{ $student->phone ?? '—' }}</dd>

                        <dt class="col-sm-4 text-muted small text-uppercase">Birthdate</dt>
                        <dd class="col-sm-8 text-dark">{{ $student->birthdate ?? '—' }}</dd>
                    </dl>
                </div>
            </div>
        </div>

        {{-- Actions Cards --}}
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-3 h-100 bg-white">
                <div class="card-header bg-white border-0 py-3 fw-bold text-dark d-flex align-items-center gap-2">
                    <i class="bi bi-lightning-charge-fill text-primary fs-5"></i>
                    <span>Actions</span>
                </div>
                <div class="card-body pt-0 d-flex flex-column gap-2">
                    <a href="{{ route('registrar.showSemesterRecord', $student->id) }}" class="btn btn-outline-primary d-flex align-items-center justify-content-between p-3 rounded-3 text-start">
                        <div>
                            <div class="fw-bold"><i class="bi bi-bar-chart-line me-1"></i> Semester Records</div>
                            <div class="small text-muted" style="font-size:0.75rem;">View and manage GPA and locks</div>
                        </div>
                        <i class="bi bi-chevron-right text-muted"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Enrollment History Card --}}
    <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
        <div class="card-header bg-white border-0 py-3">
            <h5 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                <i class="bi bi-clock-history text-primary fs-5"></i> Enrollment History
            </h5>
        </div>
        @if ($student->enrollments->isEmpty())
            <div class="card-body text-center text-muted py-5">
                <i class="bi bi-inbox fs-1 mb-2 d-block opacity-50"></i>
                <span>No enrollment history records found.</span>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th class="px-4">School Year</th>
                            <th>Section</th>
                            <th class="text-center">Status</th>
                            <th>Submitted</th>
                            <th class="text-end px-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($student->enrollments->sortByDesc('submitted_at') as $enrollment)
                            <tr>
                                <td class="px-4 fw-semibold text-dark">{{ $enrollment->section->schoolYear->year_label ?? '—' }}</td>
                                <td>
                                    <span class="fw-semibold text-secondary">{{ $enrollment->section->strand->strand_code ?? '' }}</span>
                                    <span class="text-muted">- {{ $enrollment->section->section_name }}</span>
                                    <span class="badge bg-light text-dark rounded-pill ms-1" style="font-size: 0.7rem;">G{{ $enrollment->section->grade_level }}</span>
                                </td>
                                <td class="text-center">
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
                                    <span class="badge {{ $badgeClass }} px-2.5 py-1.5 d-inline-flex align-items-center gap-1 rounded-pill" style="font-size: 0.75rem;">
                                        <i class="bi {{ $statusIcon }}"></i>
                                        {{ ucfirst($enrollment->status) }}
                                    </span>
                                </td>
                                <td class="text-muted">{{ $enrollment->submitted_at?->format('M d, Y') }}</td>
                                <td class="text-end px-4">
                                    <a href="{{ route('registrar.showEnrollment', $enrollment->id) }}" class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center gap-1">
                                        <i class="bi bi-eye"></i> View Detail
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

@endsection
