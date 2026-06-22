@extends('layouts.registrar')
@section('title', 'Enrollment Queue')
@section('content')

    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-0 text-dark">Enrollment Queue</h3>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('registrar.showDashboard') }}" class="text-decoration-none">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Enrollments</li>
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

    @php
        $currentStatus = request('status');
        // Batch approve only on the Pending tab
        $showBatch = $currentStatus === 'pending';
    @endphp

    {{-- Status filter tabs as modern pills --}}
    <ul class="nav nav-pills gap-2 mb-3">
        @php $tabs = ['' => 'All', 'pending' => 'Pending', 'approved' => 'Approved', 'rejected' => 'Rejected']; @endphp
        @foreach ($tabs as $value => $label)
            <li class="nav-item">
                <a href="{{ route('registrar.showEnrollments', array_filter(['status' => $value, 'strand' => request('strand'), 'grade' => request('grade'), 'section' => request('section')])) }}"
                   class="nav-link px-3 py-2 fw-semibold {{ ($currentStatus === $value || (!$currentStatus && $value === '')) ? 'active' : 'bg-white text-secondary border' }}">
                    {{ $label }}
                </a>
            </li>
        @endforeach
    </ul>

    {{-- Strand / grade filters --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body py-3">
            <form method="GET" action="{{ route('registrar.showEnrollments') }}" class="row g-2 align-items-center">
                <input type="hidden" name="status" value="{{ $currentStatus }}">
                <div class="col-md-3">
                    <label class="form-label small fw-bold text-muted mb-1">Strand</label>
                    <select name="strand" class="form-select" onchange="this.form.submit()">
                        <option value="">All strands</option>
                        @foreach ($strands as $strand)
                            <option value="{{ $strand->id }}" {{ request('strand') == $strand->id ? 'selected' : '' }}>{{ $strand->strand_code }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-bold text-muted mb-1">Grade Level</label>
                    <select name="grade" class="form-select" onchange="this.form.submit()">
                        <option value="">All grades</option>
                        <option value="11" {{ request('grade') == '11' ? 'selected' : '' }}>Grade 11</option>
                        <option value="12" {{ request('grade') == '12' ? 'selected' : '' }}>Grade 12</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-bold text-muted mb-1">Section</label>
                    <select name="section" class="form-select" onchange="this.form.submit()">
                        <option value="">All sections</option>
                        @foreach ($sections as $section)
                            <option value="{{ $section->id }}" {{ request('section') == $section->id ? 'selected' : '' }}>
                                {{ $section->strand->strand_code ?? '' }} - {{ $section->section_name }} (G{{ $section->grade_level }})
                            </option>
                        @endforeach
                    </select>
                </div>
                @if (request()->hasAny(['strand', 'grade', 'section']))
                    <div class="col-md-2 align-self-end">
                        <a href="{{ route('registrar.showEnrollments', array_filter(['status' => $currentStatus])) }}" class="btn btn-outline-secondary w-100">
                            <i class="bi bi-x-lg me-1"></i> Clear
                        </a>
                    </div>
                @endif
            </form>
        </div>
    </div>

    <form method="POST" action="{{ route('registrar.batchApproveEnrollments') }}">
        @csrf
        <div class="card border-0 shadow-sm rounded-3">
            @if ($enrollments->isEmpty())
                <div class="card-body text-center text-muted py-5">
                    <i class="bi bi-inbox fs-1 mb-2 d-block opacity-50"></i>
                    <span>No enrollment records found.</span>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                @if ($showBatch)
                                    <th style="width: 1%;" class="px-4">
                                        <input type="checkbox" class="form-check-input" onclick="document.querySelectorAll('.row-check').forEach(c => c.checked = this.checked)">
                                    </th>
                                @endif
                                <th class="{{ $showBatch ? '' : 'px-4' }}">Student</th>
                                <th>Section</th>
                                <th>Status</th>
                                <th>Submitted</th>
                                <th class="text-end px-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($enrollments as $enrollment)
                                <tr>
                                    @if ($showBatch)
                                        <td class="px-4">
                                            @if ($enrollment->status === 'pending')
                                                <input type="checkbox" class="row-check form-check-input" name="enrollment_ids[]" value="{{ $enrollment->id }}">
                                            @endif
                                        </td>
                                    @endif
                                    <td class="{{ $showBatch ? '' : 'px-4' }}">
                                        <a href="{{ route('registrar.showEnrollment', $enrollment->id) }}" class="text-decoration-none fw-semibold text-dark">
                                            {{ $enrollment->student->first_name }} {{ $enrollment->student->last_name }}
                                        </a>
                                        <div class="small text-muted" style="font-size: 0.75rem;">No: {{ $enrollment->student->student_number ?? '—' }}</div>
                                    </td>
                                    <td>
                                        <span class="fw-semibold text-secondary">{{ $enrollment->section->strand->strand_code ?? '' }}</span>
                                        <span class="text-muted">- {{ $enrollment->section->section_name }}</span>
                                        <span class="badge bg-light text-dark rounded-pill ms-1" style="font-size: 0.7rem;">G{{ $enrollment->section->grade_level }}</span>
                                    </td>
                                    <td>
                                        @php
                                            $badgeClass = match($enrollment->status) {
                                                'approved' => 'bg-success',
                                                'rejected' => 'bg-danger',
                                                default    => 'bg-warning text-dark',
                                            };
                                            $statusIcon = match($enrollment->status) {
                                                'approved' => 'bi-check-circle-fill',
                                                'rejected' => 'bi-x-circle-fill',
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
                                        @if ($enrollment->status === 'pending')
                                            <div class="d-inline-flex gap-2">
                                                <button type="submit"
                                                        formaction="{{ route('registrar.approveEnrollment', $enrollment->id) }}"
                                                        class="btn btn-sm btn-success d-inline-flex align-items-center gap-1">
                                                    <i class="bi bi-check-lg"></i> Approve
                                                </button>
                                                <a href="{{ route('registrar.showEnrollment', $enrollment->id) }}" class="btn btn-sm btn-outline-danger d-inline-flex align-items-center gap-1">
                                                    <i class="bi bi-x-lg"></i> Reject
                                                </a>
                                            </div>
                                        @else
                                            <a href="{{ route('registrar.showEnrollment', $enrollment->id) }}" class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center gap-1">
                                                <i class="bi bi-eye"></i> View
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        @if ($enrollments->isNotEmpty())
            <div class="d-flex justify-content-between align-items-center mt-3 flex-wrap gap-2">
                @if ($showBatch)
                    <button type="submit" class="btn btn-success btn-sm d-inline-flex align-items-center gap-1"
                            onclick="return confirm('Approve all selected enrollments? This cannot be undone.')">
                        <i class="bi bi-check2-all"></i> Batch Approve Selected
                    </button>
                @else
                    <span></span>
                @endif
                <div>{{ $enrollments->links() }}</div>
            </div>
        @endif
    </form>

@endsection
