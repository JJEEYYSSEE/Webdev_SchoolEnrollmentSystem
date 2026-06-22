@extends('layouts.registrar')
@section('title', 'Semester Management')
@section('content')

    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4 pb-2 border-bottom">
        <div>
            <h3 class="fw-bold mb-0 text-dark">Semester Management</h3>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('registrar.showDashboard') }}" class="text-decoration-none">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Semester Settings</li>
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
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
            <div class="d-flex align-items-center gap-2 mb-2">
                <i class="bi bi-exclamation-octagon-fill text-danger fs-5"></i>
                <strong class="text-danger">Please correct the errors below:</strong>
            </div>
            <ul class="mb-0 ps-3 small">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Create New School Year --}}
    <div class="card border-0 shadow-sm mb-4 rounded-3">
        <div class="card-header bg-white border-0 py-3">
            <h5 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                <i class="bi bi-calendar-plus-fill text-primary fs-5"></i> Create School Year
            </h5>
        </div>
        <div class="card-body pt-0">
            <form method="POST" action="{{ route('registrar.semester.store') }}" class="row g-3 align-items-end">
                @csrf
                <div class="col-md-5">
                    <label for="year_label" class="form-label fw-semibold small text-muted">Year Label <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-calendar3"></i></span>
                        <input id="year_label" name="year_label" type="text" class="form-control"
                               placeholder="e.g. 2027-2028" required>
                    </div>
                </div>
                <div class="col-md-4">
                    <label for="active_semester" class="form-label fw-semibold small text-muted">Active Semester <span class="text-danger">*</span></label>
                    <select id="active_semester" name="active_semester" class="form-select" required>
                        <option value="1st">1st Semester</option>
                        <option value="2nd">2nd Semester</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary w-100 d-inline-flex align-items-center justify-content-center gap-1">
                        <i class="bi bi-plus-lg"></i> Create Year
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- School Year List --}}
    <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
        <div class="card-header bg-white border-0 py-3">
            <h5 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                <i class="bi bi-calendar-event-fill text-primary fs-5"></i> School Years
            </h5>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th class="px-4">School Year</th>
                        <th>Active Status</th>
                        <th>Active Semester</th>
                        <th>Enrollment Queue</th>
                        <th class="text-end px-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($schoolYears as $sy)
                        <tr>
                            <td class="px-4 fw-bold text-dark fs-6">{{ $sy->year_label }}</td>
                            <td>
                                @if ($sy->is_active)
                                    <span class="badge bg-success rounded-pill px-2.5 py-1.5 d-inline-flex align-items-center gap-1" style="font-size: 0.75rem;">
                                        <i class="bi bi-check-circle-fill"></i> Active
                                    </span>
                                @else
                                    <span class="badge bg-secondary rounded-pill px-2.5 py-1.5 d-inline-flex align-items-center gap-1" style="font-size: 0.75rem;">
                                        <i class="bi bi-dash-circle"></i> Inactive
                                    </span>
                                @endif
                            </td>
                            <td>
                                @if ($sy->is_active)
                                    {{-- Switch active semester inline --}}
                                    <form method="POST" action="{{ route('registrar.semester.setSemester', $sy->id) }}" class="d-flex gap-2 align-items-center">
                                        @csrf 
                                        @method('PATCH')
                                        <select name="active_semester" class="form-select form-select-sm" style="width: auto;" onchange="this.form.submit()">
                                            <option value="1st" {{ $sy->active_semester === '1st' ? 'selected' : '' }}>1st Sem</option>
                                            <option value="2nd" {{ $sy->active_semester === '2nd' ? 'selected' : '' }}>2nd Sem</option>
                                        </select>
                                    </form>
                                @else
                                    {{-- Semester picker for inactive rows (submit via the Set Active form below) --}}
                                    <select name="active_semester" form="activateForm{{ $sy->id }}" class="form-select form-select-sm" style="width: auto;">
                                        <option value="1st" {{ $sy->active_semester === '1st' ? 'selected' : '' }}>1st Sem</option>
                                        <option value="2nd" {{ $sy->active_semester === '2nd' ? 'selected' : '' }}>2nd Sem</option>
                                    </select>
                                @endif
                            </td>
                            <td>
                                @if ($sy->is_enrollment_open)
                                    <span class="badge bg-primary rounded-pill px-2.5 py-1.5 d-inline-flex align-items-center gap-1" style="font-size: 0.75rem;">
                                        <i class="bi bi-unlock-fill"></i> Open
                                    </span>
                                @else
                                    <span class="badge bg-light text-dark border rounded-pill px-2.5 py-1.5 d-inline-flex align-items-center gap-1" style="font-size: 0.75rem;">
                                        <i class="bi bi-lock-fill text-muted"></i> Closed
                                    </span>
                                @endif
                            </td>
                            <td class="text-end px-4">
                                <div class="d-inline-flex gap-2 align-items-center">
                                    @unless ($sy->is_active)
                                        <form id="activateForm{{ $sy->id }}" method="POST" action="{{ route('registrar.semester.activate', $sy->id) }}" class="d-inline">
                                            @csrf 
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-sm btn-outline-success d-inline-flex align-items-center gap-1">
                                                <i class="bi bi-check-lg"></i> Set Active
                                            </button>
                                        </form>
                                    @endunless

                                    <form method="POST" action="{{ route('registrar.semester.toggleEnrollment', $sy->id) }}" class="d-inline">
                                        @csrf 
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-sm {{ $sy->is_enrollment_open ? 'btn-outline-danger' : 'btn-outline-primary' }} d-inline-flex align-items-center gap-1">
                                            @if ($sy->is_enrollment_open)
                                                <i class="bi bi-lock"></i> Close
                                            @else
                                                <i class="bi bi-unlock"></i> Open
                                            @endif
                                        </button>
                                    </form>

                                    <button type="button" class="btn btn-sm btn-outline-dark d-inline-flex align-items-center gap-1"
                                            data-bs-toggle="modal" data-bs-target="#finalize{{ $sy->id }}">
                                        <i class="bi bi-clipboard-check"></i> Finalize
                                    </button>
                                </div>

                                {{-- Finalize modal --}}
                                <div class="modal fade text-start" id="finalize{{ $sy->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <form method="POST" action="{{ route('registrar.semester.finalize', $sy->id) }}" class="modal-content border-0 shadow-lg">
                                            @csrf
                                            <div class="modal-header bg-dark text-white border-0 py-3">
                                                <h5 class="modal-title fw-bold d-flex align-items-center gap-2">
                                                    <i class="bi bi-clipboard-check-fill"></i> Finalize {{ $sy->year_label }}
                                                </h5>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body p-4">
                                                <div class="alert alert-warning border-0 d-flex gap-2">
                                                    <i class="bi bi-exclamation-triangle-fill fs-5 text-warning"></i>
                                                    <div>
                                                        <strong>Warning:</strong> This process will calculate the final GPAs for all students and lock their records for the selected semester.
                                                    </div>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="semester{{ $sy->id }}" class="form-label fw-semibold small text-muted">Select Semester to Finalize</label>
                                                    <select name="semester" id="semester{{ $sy->id }}" class="form-select" required>
                                                        <option value="1st">1st Semester</option>
                                                        <option value="2nd">2nd Semester</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="modal-footer border-top-0 pt-0">
                                                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-dark">Finalize &amp; Lock</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

@endsection
