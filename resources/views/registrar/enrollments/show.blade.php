@extends('layouts.registrar')
@section('title', 'Enrollment Detail')
@section('content')

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

    {{-- Page Header --}}
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-4 pb-2 border-bottom">
        <div class="d-flex align-items-center gap-3">
            <a href="{{ route('registrar.showEnrollments') }}" class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center gap-1">
                <i class="bi bi-arrow-left"></i> <span>Back</span>
            </a>
            <h4 class="fw-bold mb-0 text-dark">Enrollment #{{ $enrollment->id }}</h4>
            <span class="badge {{ $badgeClass }} px-2.5 py-1.5 d-inline-flex align-items-center gap-1 rounded-pill" style="font-size: 0.85rem;">
                <i class="bi {{ $statusIcon }}"></i>
                {{ ucfirst($enrollment->status) }}
            </span>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 border-0 shadow-sm mb-4" role="alert">
            <i class="bi bi-check-circle-fill text-success fs-5"></i>
            <div>{{ session('success') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center gap-2 border-0 shadow-sm mb-4" role="alert">
            <i class="bi bi-exclamation-triangle-fill text-danger fs-5"></i>
            <div>{{ session('error') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row g-4">
        {{-- Student Info --}}
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 py-3 fw-bold text-dark d-flex align-items-center gap-2">
                    <i class="bi bi-person-fill text-primary fs-5"></i>
                    <span>Student Information</span>
                </div>
                <div class="card-body pt-0">
                    <dl class="row mb-0 gy-2">
                        <dt class="col-sm-4 text-muted small text-uppercase">Student No.</dt>
                        <dd class="col-sm-8 fw-semibold text-dark">{{ $enrollment->student->student_number }}</dd>

                        <dt class="col-sm-4 text-muted small text-uppercase">Full Name</dt>
                        <dd class="col-sm-8 fw-semibold text-dark">{{ $enrollment->student->first_name }} {{ $enrollment->student->last_name }}</dd>

                        <dt class="col-sm-4 text-muted small text-uppercase">Email</dt>
                        <dd class="col-sm-8 text-dark">{{ $enrollment->student->user->email ?? '—' }}</dd>

                        <dt class="col-sm-4 text-muted small text-uppercase">Strand / Grade</dt>
                        <dd class="col-sm-8 text-dark">
                            <span class="badge bg-light text-dark fw-bold border">{{ $enrollment->student->strand->strand_code ?? '—' }}</span>
                            &middot; Grade {{ $enrollment->student->grade_level }}
                        </dd>

                        <dt class="col-sm-4 text-muted small text-uppercase">Phone</dt>
                        <dd class="col-sm-8 text-dark">{{ $enrollment->student->phone ?? '—' }}</dd>
                    </dl>
                </div>
            </div>
        </div>

        {{-- Enrollment Info --}}
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 py-3 fw-bold text-dark d-flex align-items-center gap-2">
                    <i class="bi bi-file-earmark-text text-primary fs-5"></i>
                    <span>Enrollment Details</span>
                </div>
                <div class="card-body pt-0">
                    <dl class="row mb-0 gy-2">
                        <dt class="col-sm-4 text-muted small text-uppercase">School Year</dt>
                        <dd class="col-sm-8 fw-semibold text-dark">{{ $enrollment->section->schoolYear->year_label ?? '—' }}</dd>

                        <dt class="col-sm-4 text-muted small text-uppercase">Semester</dt>
                        <dd class="col-sm-8 text-dark">{{ $enrollment->section->semester }} Semester</dd>

                        <dt class="col-sm-4 text-muted small text-uppercase">Section</dt>
                        <dd class="col-sm-8 text-dark">
                            <span class="fw-semibold">{{ $enrollment->section->section_name }}</span>
                            <span class="badge bg-light text-secondary ms-1">{{ $enrollment->section->time_period }}</span>
                        </dd>

                        <dt class="col-sm-4 text-muted small text-uppercase">Submitted</dt>
                        <dd class="col-sm-8 text-muted small">{{ $enrollment->submitted_at?->format('M d, Y g:i A') }}</dd>

                        @if ($enrollment->reviewed_at)
                            <dt class="col-sm-4 text-muted small text-uppercase">Reviewed</dt>
                            <dd class="col-sm-8 text-muted small">
                                {{ $enrollment->reviewed_at->format('M d, Y') }} by {{ $enrollment->approver->first_name ?? '—' }}
                            </dd>
                        @endif

                        @if ($enrollment->remarks)
                            <dt class="col-sm-4 text-muted small text-uppercase">Remarks</dt>
                            <dd class="col-sm-8 text-danger fw-semibold">{{ $enrollment->remarks }}</dd>
                        @endif
                    </dl>
                </div>
            </div>
        </div>

        {{-- Subjects Table --}}
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                    <span class="fw-bold text-dark fs-5">Subjects ({{ $enrollment->subjects->count() }})</span>
                    @if ($enrollment->status === 'approved')
                        <a href="{{ route('registrar.showGradeForm', $enrollment->id) }}" class="btn btn-sm btn-primary d-inline-flex align-items-center gap-1">
                            <i class="bi bi-pencil-square"></i> Encode Grades
                        </a>
                    @endif
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th class="px-4">Code</th>
                                <th>Subject Name</th>
                                <th class="text-center">Units</th>
                                <th class="text-center px-4">Grade</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($enrollment->subjects as $subject)
                                <tr>
                                    <td class="px-4 text-muted fw-bold">{{ $subject->subject_code }}</td>
                                    <td>{{ $subject->subject_name }}</td>
                                    <td class="text-center fw-semibold text-secondary">{{ $subject->units }}</td>
                                    <td class="text-center px-4">
                                        @if ($subject->pivot->grade !== null)
                                            <span class="badge bg-primary text-white rounded-pill px-2.5 py-1.5" style="font-size: 0.8rem;">
                                                {{ number_format($subject->pivot->grade, 2) }}
                                            </span>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Enrollment requirements (Grade 12) --}}
        @if ($enrollment->documents->isNotEmpty())
            <div class="col-12">
                <div class="card border-0 shadow-sm rounded-3">
                    <div class="card-header bg-white border-0 py-3 fw-bold text-dark d-flex align-items-center gap-2">
                        <i class="bi bi-folder2-open text-primary fs-5"></i>
                        <span>Submitted Requirements</span>
                    </div>
                    <div class="card-body pt-0">
                        @foreach ($enrollment->documents as $doc)
                            <a href="{{ $doc->url() }}" target="_blank"
                               class="d-flex align-items-center justify-content-between border rounded-3 p-2 mb-2 text-decoration-none">
                                <span class="small text-dark">
                                    <i class="bi bi-file-earmark-text me-1 text-primary"></i>
                                    <strong>{{ $doc->label() }}</strong>
                                    <span class="text-muted">— {{ $doc->original_name ?? 'file' }}</span>
                                </span>
                                <i class="bi bi-box-arrow-up-right text-muted"></i>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif
    </div>

    {{-- Revert action — reopen an invalid (returned) application --}}
    @if ($enrollment->status === 'invalid')
        <div class="mt-4">
            <button type="button" class="btn btn-outline-secondary d-inline-flex align-items-center gap-1" data-bs-toggle="modal" data-bs-target="#revertModal">
                <i class="bi bi-arrow-counterclockwise"></i> Reopen (Revert to Pending)
            </button>
        </div>

        <div class="modal fade" id="revertModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <form method="POST" action="{{ route('registrar.revertEnrollment', $enrollment->id) }}" class="modal-content border-0 shadow-lg">
                    @csrf
                    <div class="modal-header bg-warning text-dark border-0 py-3">
                        <h5 class="modal-title fw-bold d-flex align-items-center gap-2">
                            <i class="bi bi-arrow-counterclockwise"></i> Reopen Enrollment
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <p class="text-muted small">
                            This sets the application back to <strong class="text-warning">pending</strong> so it can be reviewed again.
                            A valid reason is required (e.g. the student complied with the missing requirements).
                        </p>
                        <div class="mb-3">
                            <label for="revert_reason" class="form-label fw-bold small text-muted">Reason for reopening</label>
                            <textarea name="revert_reason" id="revert_reason" rows="3" class="form-control"
                                      minlength="5" required
                                      placeholder="e.g. Student submitted the missing Form 138."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer border-top-0 pt-0">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-warning">Reopen Application</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- Approve / Reject actions --}}
    @if ($enrollment->status === 'pending')
        <div class="d-flex gap-2 mt-4">
            <form method="POST" action="{{ route('registrar.approveEnrollment', $enrollment->id) }}">
                @csrf
                <button type="submit" class="btn btn-success d-inline-flex align-items-center gap-1 px-4 py-2">
                    <i class="bi bi-check-lg fs-5"></i> Approve Enrollment
                </button>
            </form>

            <button type="button" class="btn btn-warning d-inline-flex align-items-center gap-1 px-4 py-2" data-bs-toggle="modal" data-bs-target="#rejectModal">
                <i class="bi bi-exclamation-triangle-fill fs-5"></i> Return as Invalid
            </button>
        </div>

        {{-- Return-for-compliance modal with remarks --}}
        <div class="modal fade" id="rejectModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <form method="POST" action="{{ route('registrar.rejectEnrollment', $enrollment->id) }}" class="modal-content border-0 shadow-lg">
                    @csrf
                    <div class="modal-header bg-warning text-dark border-0 py-3">
                        <h5 class="modal-title fw-bold d-flex align-items-center gap-2">
                            <i class="bi bi-exclamation-triangle-fill"></i> Return for Compliance
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <p class="text-muted small">The student will be asked to fix the issue and re-submit. This is not a permanent rejection.</p>
                        <div class="mb-3">
                            <label for="remarks" class="form-label fw-bold small text-muted">What needs to be corrected?</label>
                            <textarea name="remarks" id="remarks" rows="3" class="form-control"
                                      placeholder="e.g. Incomplete requirements — missing Form 138" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer border-top-0 pt-0">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-warning">Return as Invalid</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

@endsection
