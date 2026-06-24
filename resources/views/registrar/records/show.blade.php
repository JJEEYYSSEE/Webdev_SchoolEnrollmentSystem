@extends('layouts.registrar')
@section('title', 'Student Semester Records')
@section('content')

    {{-- Page Header --}}
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-4 pb-2 border-bottom">
        <div class="d-flex align-items-center gap-3">
            <a href="{{ route('registrar.showStudent', $student->id) }}" class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center gap-1">
                <i class="bi bi-arrow-left"></i> <span>Back</span>
            </a>
            <div>
                <h4 class="fw-bold mb-0 text-dark">{{ $student->first_name }} {{ $student->last_name }}</h4>
                <span class="text-muted small">Student No: <strong>{{ $student->student_number }}</strong></span>
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

    {{-- Records Table Card --}}
    <div class="card border-0 shadow-sm mb-4 rounded-3 overflow-hidden">
        <div class="card-header bg-white border-0 py-3">
            <h5 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                <i class="bi bi-bar-chart-line text-primary fs-5"></i> Semester Records
            </h5>
        </div>
        @if ($records->isEmpty())
            <div class="card-body text-muted text-center py-5">
                <i class="bi bi-inbox fs-1 mb-2 d-block opacity-50"></i>
                <span>No semester records found for this student.</span>
            </div>
        @else
            <div class="accordion accordion-flush" id="recRecords">
                @foreach ($records as $i => $record)
                    @php $enr = $enrollments[$record->school_year_id.'-'.$record->semester] ?? null; @endphp
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#srec{{ $i }}">
                                <div class="d-flex flex-wrap align-items-center gap-3 w-100 pe-3">
                                    <span class="fw-bold text-dark">{{ $record->schoolYear->year_label ?? '—' }}</span>
                                    <span class="badge bg-light text-secondary border">{{ $record->semester }} Semester</span>
                                    <span class="text-muted small"><i class="bi bi-collection me-1"></i>{{ $enr?->section?->section_name ?? '—' }}</span>
                                    <span class="ms-auto d-flex align-items-center gap-2">
                                        <span class="text-muted small">GPA</span>
                                        <span class="fw-bold text-primary">{{ $record->gpa !== null ? number_format($record->gpa, 2) : '—' }}</span>
                                        @if ($record->is_locked)
                                            <span class="badge bg-success rounded-pill"><i class="bi bi-lock-fill me-1"></i>Locked</span>
                                        @else
                                            <span class="badge bg-warning text-dark rounded-pill"><i class="bi bi-unlock-fill me-1"></i>Open</span>
                                        @endif
                                    </span>
                                </div>
                            </button>
                        </h2>
                        <div id="srec{{ $i }}" class="accordion-collapse collapse" data-bs-parent="#recRecords">
                            <div class="accordion-body p-0">
                                @if ($enr && $enr->subjects->isNotEmpty())
                                    <table class="table table-sm table-hover mb-0 align-middle">
                                        <thead class="table-light">
                                            <tr>
                                                <th class="px-4">Code</th><th>Subject</th>
                                                <th class="text-center">Units</th><th class="text-center">Grade</th><th class="text-center px-4">Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($enr->subjects as $subject)
                                                <tr>
                                                    <td class="px-4 text-muted fw-bold">{{ $subject->subject_code }}</td>
                                                    <td>{{ $subject->subject_name }}</td>
                                                    <td class="text-center text-muted">{{ $subject->units }}</td>
                                                    <td class="text-center fw-bold {{ ($subject->pivot->grade ?? 0) >= 75 ? 'text-success' : 'text-danger' }}">
                                                        {{ $subject->pivot->grade !== null ? number_format($subject->pivot->grade, 2) : '—' }}
                                                    </td>
                                                    <td class="text-center px-4"><span class="badge {{ $subject->pivot->status === 'passed' ? 'bg-success' : ($subject->pivot->status === 'failed' ? 'bg-danger' : 'bg-secondary') }} rounded-pill">{{ ucfirst($subject->pivot->status) }}</span></td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                @else
                                    <div class="p-3 text-muted small">No subject breakdown for this semester.</div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- Manual Record Entry/Override Form --}}
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-header bg-white border-0 py-3">
            <h5 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                <i class="bi bi-pencil-square text-primary fs-5"></i> Add / Update a Record
            </h5>
        </div>
        <div class="card-body pt-0">
            <form method="POST" action="{{ route('registrar.updateSemesterRecord', $student->id) }}" class="row g-3 align-items-end">
                @csrf 
                @method('PUT')
                
                <div class="col-md-4">
                    <label for="school_year_id" class="form-label fw-semibold small text-muted">School Year</label>
                    <select id="school_year_id" name="school_year_id" class="form-select" required>
                        @foreach ($schoolYears as $sy)
                            <option value="{{ $sy->id }}">{{ $sy->year_label }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="col-md-3">
                    <label for="semester" class="form-label fw-semibold small text-muted">Semester</label>
                    <select id="semester" name="semester" class="form-select" required>
                        <option value="1st">1st Semester</option>
                        <option value="2nd">2nd Semester</option>
                    </select>
                </div>
                
                <div class="col-md-2">
                    <label for="gpa" class="form-label fw-semibold small text-muted">GPA</label>
                    <input id="gpa" name="gpa" type="number" step="0.01" min="60" max="100" class="form-control" placeholder="85.00">
                </div>
                
                <div class="col-md-1.5 pb-2">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="is_locked" value="1" id="is_locked">
                        <label class="form-check-label fw-semibold small text-muted" for="is_locked">
                            <i class="bi bi-lock-fill"></i> Lock Record
                        </label>
                    </div>
                </div>
                
                <div class="col-auto ms-auto">
                    <button type="submit" class="btn btn-primary px-4 d-inline-flex align-items-center gap-1">
                        <i class="bi bi-save-fill"></i> Save Record
                    </button>
                </div>
            </form>
        </div>
    </div>

@endsection
