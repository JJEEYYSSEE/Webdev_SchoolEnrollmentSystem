@extends('layouts.registrar')
@section('title', 'Registrar Dashboard')
@section('content')

    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-0 text-dark">Registrar Dashboard</h3>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('registrar.showDashboard') }}" class="text-decoration-none">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
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

    @if ($schoolYear)
        <div class="alert {{ $schoolYear->is_enrollment_open ? 'alert-success text-success-emphasis' : 'alert-secondary text-secondary-emphasis' }} d-flex justify-content-between align-items-center border-0 shadow-sm rounded-3 py-3 px-4 mb-4">
            <span class="d-flex align-items-center gap-2">
                <i class="bi bi-calendar3 fs-5"></i>
                <span>
                    Active S.Y. <strong>{{ $schoolYear->year_label }}</strong>
                    &middot; <strong>{{ $schoolYear->active_semester }} Semester</strong> —
                    Enrollment is <strong>{{ $schoolYear->is_enrollment_open ? 'OPEN' : 'CLOSED' }}</strong>
                </span>
            </span>
            <a href="{{ route('registrar.semester.index') }}" class="btn btn-sm btn-outline-primary">
                <i class="bi bi-gear-fill me-1"></i> Manage
            </a>
        </div>
    @else
        <div class="alert alert-warning border-0 shadow-sm d-flex justify-content-between align-items-center mb-4">
            <span class="d-flex align-items-center gap-2">
                <i class="bi bi-exclamation-circle-fill fs-5"></i>
                <span>No active school year setup. Please configure the academic year.</span>
            </span>
            <a href="{{ route('registrar.semester.index') }}" class="btn btn-sm btn-warning">Set one up</a>
        </div>
    @endif

    {{-- Stat Cards --}}
    <div class="row g-3 mb-4">
        {{-- Pending Card --}}
        <div class="col-xl-3 col-md-6 col-12">
            <div class="card h-100 border-0 border-start border-4 border-warning shadow-sm bg-white">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <p class="text-uppercase fw-bold text-muted mb-1" style="font-size: 0.72rem; letter-spacing: 0.05em;">Pending Review</p>
                        <h2 class="fw-bold text-warning mb-1">{{ $pendingCount }}</h2>
                        <a href="{{ route('registrar.showEnrollments', ['status' => 'pending']) }}" class="small text-decoration-none d-inline-flex align-items-center gap-1">
                            Review <i class="bi bi-arrow-right-short"></i>
                        </a>
                    </div>
                    <div class="rounded-circle bg-warning bg-opacity-10 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="bi bi-hourglass-split text-warning fs-4"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- Approved Card --}}
        <div class="col-xl-3 col-md-6 col-12">
            <div class="card h-100 border-0 border-start border-4 border-success shadow-sm bg-white">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <p class="text-uppercase fw-bold text-muted mb-1" style="font-size: 0.72rem; letter-spacing: 0.05em;">Approved</p>
                        <h2 class="fw-bold text-success mb-1">{{ $approvedCount }}</h2>
                        <a href="{{ route('registrar.showEnrollments', ['status' => 'approved']) }}" class="small text-decoration-none text-success d-inline-flex align-items-center gap-1">
                            View Approved <i class="bi bi-arrow-right-short"></i>
                        </a>
                    </div>
                    <div class="rounded-circle bg-success bg-opacity-10 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="bi bi-check-circle-fill text-success fs-4"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- Invalid / Returned Card --}}
        <div class="col-xl-3 col-md-6 col-12">
            <div class="card h-100 border-0 border-start border-4 border-warning shadow-sm bg-white">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <p class="text-uppercase fw-bold text-muted mb-1" style="font-size: 0.72rem; letter-spacing: 0.05em;">Invalid / Returned</p>
                        <h2 class="fw-bold text-warning-emphasis mb-1">{{ $rejectedCount }}</h2>
                        <a href="{{ route('registrar.showEnrollments', ['status' => 'invalid']) }}" class="small text-decoration-none text-warning-emphasis d-inline-flex align-items-center gap-1">
                            View Invalid <i class="bi bi-arrow-right-short"></i>
                        </a>
                    </div>
                    <div class="rounded-circle bg-warning bg-opacity-10 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="bi bi-exclamation-triangle-fill text-warning fs-4"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- Strand Summary Card --}}
        <div class="col-xl-3 col-md-6 col-12">
            <div class="card h-100 border-0 border-start border-4 border-primary shadow-sm bg-white">
                <div class="card-body">
                    <p class="text-uppercase fw-bold text-muted mb-2" style="font-size: 0.72rem; letter-spacing: 0.05em;">Approved by Strand</p>
                    <div class="d-flex flex-column gap-1" style="max-height: 80px; overflow-y: auto;">
                        @forelse ($perStrand as $strand => $total)
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="small text-secondary fw-semibold">{{ $strand }}</span>
                                <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-2 py-0.5" style="font-size: 0.75rem;">{{ $total }}</span>
                            </div>
                        @empty
                            <div class="text-muted small py-1">No strand enrollments yet.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Recent Enrollments Card --}}
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                <i class="bi bi-file-earmark-text text-primary fs-5"></i>
                <span>Recent Enrollments</span>
            </h5>
            <a href="{{ route('registrar.showEnrollments') }}" class="btn btn-sm btn-outline-primary d-inline-flex align-items-center gap-1">
                View all <i class="bi bi-arrow-right-short"></i>
            </a>
        </div>
        @if ($recentEnrollments->isEmpty())
            <div class="card-body text-center text-muted py-5">
                <i class="bi bi-inbox fs-1 mb-2 d-block opacity-50"></i>
                <span>No enrollment records found.</span>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="px-4">Student</th>
                            <th>Section</th>
                            <th>Status</th>
                            <th>Submitted</th>
                            <th class="text-end px-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($recentEnrollments as $enrollment)
                            <tr>
                                <td class="px-4">
                                    <div class="fw-semibold text-dark">{{ $enrollment->student->first_name }} {{ $enrollment->student->last_name }}</div>
                                    <div class="small text-muted" style="font-size: 0.75rem;">No: {{ $enrollment->student->student_number ?? '—' }}</div>
                                </td>
                                <td>
                                    <span class="fw-semibold text-secondary">{{ $enrollment->section->strand->strand_code ?? '' }}</span>
                                    <span class="text-muted">- {{ $enrollment->section->section_name }}</span>
                                </td>
                                <td>
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
                                    <span class="badge {{ $badgeClass }} px-2 py-1 d-inline-flex align-items-center gap-1 rounded-pill" style="font-size: 0.75rem;">
                                        <i class="bi {{ $statusIcon }}"></i>
                                        {{ ucfirst($enrollment->status) }}
                                    </span>
                                </td>
                                <td class="text-muted">{{ $enrollment->submitted_at?->format('M d, Y') }}</td>
                                <td class="text-end px-4">
                                    <a href="{{ route('registrar.showEnrollment', $enrollment->id) }}" class="btn btn-sm btn-primary d-inline-flex align-items-center gap-1 py-1">
                                        <i class="bi bi-eye"></i> View
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
