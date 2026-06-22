@extends('layouts.student')
@section('title', 'Dashboard')
@section('content')

    {{-- Welcome Card with Teal Gradient and 3-Step Progress Indicator --}}
    <div class="card border-0 shadow-sm mb-4 text-white overflow-hidden rounded-4 animate-fade-in" 
         style="background: linear-gradient(135deg, #0d6e5f 0%, #1aa086 100%);">
        <div class="card-body p-4 p-md-5">
            <div class="row align-items-center">
                <div class="col-md-7 mb-4 mb-md-0">
                    <span class="badge bg-white text-success px-3 py-2 rounded-pill fw-bold text-uppercase mb-2 shadow-sm" style="font-size:0.75rem; letter-spacing: 0.05em;">
                        <i class="bi bi-mortarboard-fill"></i> Student Portal
                    </span>
                    <h2 class="fw-bold mb-1 display-6">Welcome, {{ $student->first_name ?? auth()->user()->name }}!</h2>
                    <p class="opacity-90 mb-0">
                        Student No: <strong>{{ $student->student_number ?? '—' }}</strong>
                        @if ($student?->strand)
                            &middot; {{ $student->strand->strand_code }} &middot; Grade {{ $student->grade_level }}
                        @endif
                    </p>
                </div>
                
                {{-- Status card --}}
                <div class="col-md-5 d-flex justify-content-md-end">
                    <div class="bg-white rounded-4 shadow-sm px-4 py-3 w-100" style="max-width: 260px;">
                        <p class="small text-muted text-uppercase fw-bold mb-2" style="letter-spacing: 0.07em; font-size: 0.65rem;">Current Status</p>
                        @if ($enrollment)
                            @php
                                $badgeClass = match($enrollment->status) {
                                    'approved' => 'text-bg-success',
                                    'invalid'  => 'text-bg-warning',
                                    default    => 'text-bg-secondary',
                                };
                                $statusIcon = match($enrollment->status) {
                                    'approved' => 'bi-check-circle-fill',
                                    'invalid'  => 'bi-exclamation-triangle-fill',
                                    default    => 'bi-hourglass-split',
                                };
                                $statusHint = match($enrollment->status) {
                                    'approved' => 'Your enrollment is confirmed.',
                                    'invalid'  => 'Returned — fix and re-submit.',
                                    default    => 'Under registrar review.',
                                };
                            @endphp
                            <span class="badge {{ $badgeClass }} fs-6 px-3 py-2 rounded-pill d-inline-flex align-items-center gap-1.5">
                                <i class="bi {{ $statusIcon }}"></i>
                                {{ ucfirst($enrollment->status) }}
                            </span>
                            <p class="small text-muted mb-0 mt-2">{{ $statusHint }}</p>
                        @else
                            <span class="badge text-bg-secondary fs-6 px-3 py-2 rounded-pill">Not Enrolled</span>
                            <p class="small text-muted mb-0 mt-2">Submit your enrollment form to get started.</p>
                        @endif
                    </div>
                </div>
            </div>
            
            {{-- Progress indicator --}}
            <hr class="my-4 border-white border-opacity-20">
            <div class="row text-center gy-3 justify-content-between">
                @php
                    $step1Class = $enrollment ? 'text-white' : 'opacity-50 text-white-50';
                    $step2Class = ($enrollment && in_array($enrollment->status, ['pending', 'approved', 'invalid'])) ? 'text-white' : 'opacity-50 text-white-50';
                    $step3Class = ($enrollment && $enrollment->status === 'approved') ? 'text-white' : 'opacity-50 text-white-50';
                @endphp
                <div class="col-md-4 d-flex align-items-center justify-content-center gap-2 {{ $step1Class }}">
                    <div class="rounded-circle d-flex align-items-center justify-content-center bg-white text-success fw-bold" style="width: 28px; height: 28px;">1</div>
                    <span class="fw-bold">Submit Form</span>
                </div>
                <div class="col-md-4 d-flex align-items-center justify-content-center gap-2 {{ $step2Class }}">
                    <div class="rounded-circle d-flex align-items-center justify-content-center @if($enrollment && in_array($enrollment->status, ['pending', 'approved', 'invalid'])) bg-white text-success @else bg-white bg-opacity-20 text-white @endif fw-bold" style="width: 28px; height: 28px;">2</div>
                    <span class="fw-bold">Registrar Review</span>
                </div>
                <div class="col-md-4 d-flex align-items-center justify-content-center gap-2 {{ $step3Class }}">
                    <div class="rounded-circle d-flex align-items-center justify-content-center @if($enrollment && $enrollment->status === 'approved') bg-white text-success @else bg-white bg-opacity-20 text-white @endif fw-bold" style="width: 28px; height: 28px;">3</div>
                    <span class="fw-bold">Enrolled</span>
                </div>
            </div>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 border-0 shadow-sm mb-4" role="alert">
            <i class="bi bi-check-circle-fill text-success fs-5"></i>
            <div>{{ session('success') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Academic Year Status Banner --}}
    @if ($schoolYear)
        <div class="alert {{ $schoolYear->is_enrollment_open ? 'alert-primary border-0 text-primary-emphasis bg-primary bg-opacity-10' : 'alert-secondary border-0 text-secondary-emphasis' }} d-flex align-items-center gap-3 mb-4 p-3 shadow-sm rounded-3">
            <i class="bi bi-calendar3 fs-4 text-primary"></i>
            <div>
                <div class="fw-bold">S.Y. {{ $schoolYear->year_label }} &middot; {{ $schoolYear->active_semester }} Semester</div>
                <div class="small">
                    @if ($schoolYear->is_enrollment_open)
                        <span class="text-success fw-bold"><i class="bi bi-unlock-fill me-1"></i>Online Enrollment is currently OPEN</span>
                    @else
                        <span class="text-muted"><i class="bi bi-lock-fill me-1"></i>Enrollment is closed</span>
                    @endif
                </div>
            </div>
        </div>
    @else
        <div class="alert alert-warning border-0 d-flex align-items-center gap-3 mb-4 shadow-sm">
            <i class="bi bi-exclamation-circle-fill fs-4 text-warning"></i>
            <div>No active school year set. Please check back later.</div>
        </div>
    @endif

    {{-- Section / Grade Overview --}}
    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body py-3.5 d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-success bg-opacity-10 d-flex align-items-center justify-content-center text-success" style="width: 48px; height: 48px;">
                        <i class="bi bi-collection fs-4"></i>
                    </div>
                    <div>
                        <p class="small text-muted text-uppercase fw-bold mb-0.5" style="letter-spacing: 0.05em; font-size:0.7rem;">Assigned Section</p>
                        <h5 class="fw-bold mb-0 text-dark">{{ $enrollment->section->section_name ?? '— Not Assigned —' }}</h5>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body py-3.5 d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-success bg-opacity-10 d-flex align-items-center justify-content-center text-success" style="width: 48px; height: 48px;">
                        <i class="bi bi-shield-check fs-4"></i>
                    </div>
                    <div>
                        <p class="small text-muted text-uppercase fw-bold mb-0.5" style="letter-spacing: 0.05em; font-size:0.7rem;">Grade Level</p>
                        <h5 class="fw-bold mb-0 text-dark">
                            @if ($enrollment)
                                Grade {{ $enrollment->section->grade_level ?? $student->grade_level }}
                            @else
                                Grade {{ $student->grade_level ?? '—' }}
                            @endif
                        </h5>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Quick Actions --}}
    <h6 class="text-uppercase fw-bold text-muted mb-3 d-flex align-items-center gap-2" style="font-size:0.75rem; letter-spacing:0.05em;">
        <i class="bi bi-lightning-fill text-warning"></i> Quick Actions
    </h6>
    <div class="row g-3">
        @if (!$enrollment || $enrollment->status === 'invalid')
            <div class="col-md-6">
                <a href="{{ route('student.showEnrollForm') }}" class="card text-decoration-none h-100 border border-success border-opacity-25 card-hover bg-white">
                    <div class="card-body d-flex align-items-center gap-3 p-4">
                        <div class="rounded-circle bg-success bg-opacity-10 p-3 d-flex align-items-center justify-content-center" style="width:56px; height:56px;">
                            <i class="bi bi-file-earmark-text-fill fs-3 text-success"></i>
                        </div>
                        <div>
                            <span class="fw-bold fs-5 text-success d-block">Enroll Now</span>
                            <span class="small text-muted">Submit your application for the active semester</span>
                        </div>
                    </div>
                </a>
            </div>
        @else
            <div class="col-md-6">
                <a href="{{ route('student.showEnrollStatus') }}" class="card text-decoration-none h-100 card-hover bg-white">
                    <div class="card-body d-flex align-items-center gap-3 p-4">
                        <div class="rounded-circle bg-secondary bg-opacity-10 p-3 d-flex align-items-center justify-content-center" style="width:56px; height:56px;">
                            <i class="bi bi-hourglass-split fs-3 text-secondary"></i>
                        </div>
                        <div>
                            <span class="fw-bold fs-5 text-dark d-block">Enrollment Status</span>
                            <span class="small text-muted">View details and check verification progress</span>
                        </div>
                    </div>
                </a>
            </div>
        @endif

        <div class="col-md-6">
            <a href="{{ route('student.showSubjects') }}" class="card text-decoration-none h-100 card-hover bg-white">
                <div class="card-body d-flex align-items-center gap-3 p-4">
                    <div class="rounded-circle bg-secondary bg-opacity-10 p-3 d-flex align-items-center justify-content-center" style="width:56px; height:56px;">
                        <i class="bi bi-book-fill fs-3 text-secondary"></i>
                    </div>
                    <div>
                        <span class="fw-bold fs-5 text-dark d-block">My Subjects</span>
                        <span class="small text-muted">View and check grades for active subjects</span>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-md-6">
            <a href="{{ route('student.showRecords') }}" class="card text-decoration-none h-100 card-hover bg-white">
                <div class="card-body d-flex align-items-center gap-3 p-4">
                    <div class="rounded-circle bg-secondary bg-opacity-10 p-3 d-flex align-items-center justify-content-center" style="width:56px; height:56px;">
                        <i class="bi bi-bar-chart-line-fill fs-3 text-secondary"></i>
                    </div>
                    <div>
                        <span class="fw-bold fs-5 text-dark d-block">My Academic Records</span>
                        <span class="small text-muted">Review GPA history and finalized semestral grades</span>
                    </div>
                </div>
            </a>
        </div>
    </div>

@endsection
