@extends('layouts.student')
@section('title', 'Enrollment Status')
@section('content')

    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4 pb-2 border-bottom">
        <div>
            <h3 class="fw-bold mb-0 text-dark">Enrollment Status</h3>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('student.showDashboard') }}" class="text-decoration-none">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Enrollment Status</li>
                </ol>
            </nav>
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

    @if (! $enrollment)
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-body text-center py-5">
                <div class="rounded-circle bg-light d-inline-flex align-items-center justify-content-center mb-3" style="width: 72px; height: 72px;">
                    <i class="bi bi-inbox text-muted fs-2"></i>
                </div>
                <h5 class="fw-bold mt-2 text-dark">No enrollment record found</h5>
                <p class="text-muted small mx-auto mb-4" style="max-width: 420px;">You haven't submitted an enrollment form for the active semester yet. Click below to start.</p>
                <a href="{{ route('student.showEnrollForm') }}" class="btn btn-success d-inline-flex align-items-center gap-1">
                    <i class="bi bi-pencil-square"></i> Enroll Now
                </a>
            </div>
        </div>
    @else
        @php
            $badgeClass = match($enrollment->status) {
                'approved' => 'bg-success',
                'invalid'  => 'bg-warning text-dark',
                default    => 'bg-secondary',
            };
            $borderClass = match($enrollment->status) {
                'approved' => 'border-success',
                'invalid'  => 'border-warning',
                default    => 'border-secondary',
            };
            $icon = match($enrollment->status) {
                'approved' => 'bi-check-circle-fill text-success',
                'invalid'  => 'bi-exclamation-triangle-fill text-warning',
                default    => 'bi-hourglass-split text-secondary',
            };
        @endphp

        {{-- Status highlight panel --}}
        <div class="card border-0 border-start border-4 {{ $borderClass }} shadow-sm mb-4 bg-white">
            <div class="card-body p-4 d-flex align-items-start gap-4">
                <i class="bi {{ $icon }} fs-1 mt-0.5"></i>
                <div class="flex-grow-1">
                    <p class="small text-muted text-uppercase fw-bold mb-1" style="letter-spacing: 0.05em; font-size:0.7rem;">Application Status</p>
                    <span class="badge {{ $badgeClass }} fs-5 px-3 py-2 rounded-pill shadow-sm mb-3">{{ ucfirst($enrollment->status) }}</span>
                    
                    @if ($enrollment->status === 'pending')
                        <div class="text-secondary small">
                            <i class="bi bi-info-circle me-1"></i> Your enrollment is currently under review by the registrar. We will process your section assignment and subject units shortly.
                        </div>
                    @elseif ($enrollment->status === 'approved')
                        <div class="text-success small fw-semibold">
                            <i class="bi bi-check2 me-1"></i> Approved — your section and subjects are locked in. You may view your curriculum and schedules below.
                        </div>
                    @elseif ($enrollment->status === 'invalid')
                        <div class="p-3 bg-warning bg-opacity-10 border border-warning border-opacity-25 rounded-3 mb-3">
                            <h6 class="fw-bold text-warning-emphasis mb-1"><i class="bi bi-exclamation-triangle-fill"></i> Registrar Feedback:</h6>
                            <p class="text-warning-emphasis small mb-0">{{ $enrollment->remarks ?? 'No reason given.' }}</p>
                        </div>
                        <p class="text-muted small mb-0">
                            Your submission was <strong>returned for compliance</strong>. Please fix the issue above,
                            then <a href="{{ route('student.showEnrollForm') }}" class="fw-semibold">submit your enrollment again</a>.
                        </p>
                    @endif
                </div>
            </div>
        </div>

        {{-- Officially enrolled — Certificate of Registration --}}
        @if ($enrollment->status === 'approved')
            <div class="card border-0 border-start border-4 border-success shadow-sm mb-4 bg-white">
                <div class="card-body p-4 text-center">
                    <i class="bi bi-patch-check-fill text-success fs-1"></i>
                    <h4 class="fw-bold text-dark mt-2 mb-1">You are officially enrolled.</h4>
                    <p class="text-muted small mb-3">
                        (S.Y. {{ $enrollment->section->schoolYear->year_label ?? '' }} &middot; {{ $enrollment->section->semester }} Semester)
                    </p>
                    <a href="{{ route('student.showCertificate') }}" target="_blank"
                       class="btn btn-success px-4 d-inline-flex align-items-center gap-2">
                        <i class="bi bi-file-earmark-text"></i> Certificate of Registration
                    </a>
                </div>
            </div>
        @endif

        {{-- Stats Grid --}}
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm rounded-3">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="rounded-circle bg-success bg-opacity-10 text-success d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                            <i class="bi bi-calendar3 fs-5"></i>
                        </div>
                        <div>
                            <p class="small text-muted text-uppercase fw-bold mb-0.5" style="letter-spacing: 0.05em; font-size:0.65rem;">Academic Year</p>
                            <h6 class="fw-bold text-dark mb-0">{{ $enrollment->section->schoolYear->year_label ?? '—' }}</h6>
                            <span class="small text-muted" style="font-size:0.75rem;">{{ $enrollment->section->semester ?? '' }} Semester</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm rounded-3">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="rounded-circle bg-success bg-opacity-10 text-success d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                            <i class="bi bi-collection-play fs-5"></i>
                        </div>
                        <div>
                            <p class="small text-muted text-uppercase fw-bold mb-0.5" style="letter-spacing: 0.05em; font-size:0.65rem;">Section Details</p>
                            <h6 class="fw-bold text-dark mb-0">{{ $enrollment->section->section_name }}</h6>
                            <span class="small text-muted" style="font-size:0.75rem;">{{ $enrollment->section->strand->strand_code ?? '' }} &middot; G{{ $enrollment->section->grade_level }}</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm rounded-3">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="rounded-circle bg-success bg-opacity-10 text-success d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                            <i class="bi bi-clock-history fs-5"></i>
                        </div>
                        <div>
                            <p class="small text-muted text-uppercase fw-bold mb-0.5" style="letter-spacing: 0.05em; font-size:0.65rem;">Submitted Date</p>
                            <h6 class="fw-bold text-dark mb-0">{{ $enrollment->submitted_at?->format('M d, Y') ?? '—' }}</h6>
                            <span class="small text-muted" style="font-size:0.75rem;">{{ $enrollment->submitted_at?->format('g:i A') ?? '' }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Enrolled subjects list --}}
        <div class="card border-0 shadow-sm rounded-3 overflow-hidden bg-white">
            <div class="card-header bg-white border-0 py-3">
                <h5 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                    <i class="bi bi-book text-success fs-5"></i>
                    <span>Enrolled Subjects ({{ $enrollment->subjects->count() }})</span>
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
                        @foreach ($enrollment->subjects as $subject)
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
