@extends('layouts.student')
@section('title', 'My Records')
@section('content')

    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4 pb-2 border-bottom">
        <div>
            <h3 class="fw-bold mb-0 text-dark">Semester Records</h3>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('student.showDashboard') }}" class="text-decoration-none">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">My Records</li>
                </ol>
            </nav>
        </div>
    </div>

    @if ($records->isEmpty())
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-body text-center text-muted py-5">
                <i class="bi bi-inbox fs-1 mb-2 d-block opacity-50"></i>
                <h5 class="fw-bold text-dark mb-1">No semester records yet</h5>
                <p class="small text-muted mb-0">Your past semester records appear here once finalized. Current-semester grades show under <strong>My Subjects</strong>.</p>
            </div>
        </div>
    @else
        <p class="text-muted small mb-3"><i class="bi bi-info-circle me-1"></i> Click a semester to view its subject breakdown.</p>

        <div class="accordion" id="recordsAccordion">
            @foreach ($records as $i => $record)
                @php $enr = $enrollments[$record->school_year_id.'-'.$record->semester] ?? null; @endphp
                <div class="accordion-item border-0 shadow-sm rounded-3 mb-3 overflow-hidden">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed bg-white" type="button" data-bs-toggle="collapse" data-bs-target="#rec{{ $i }}">
                            <div class="d-flex flex-wrap align-items-center gap-3 w-100 pe-3">
                                <span class="fw-bold text-dark">{{ $record->schoolYear->year_label ?? '—' }}</span>
                                <span class="badge bg-light text-secondary border">{{ $record->semester }} Semester</span>
                                <span class="text-muted small"><i class="bi bi-collection me-1"></i>{{ $enr?->section?->section_name ?? '—' }}</span>
                                <span class="ms-auto d-flex align-items-center gap-2">
                                    <span class="text-muted small">GPA</span>
                                    <span class="fw-bold text-success fs-6">{{ $record->gpa !== null ? number_format($record->gpa, 2) : '—' }}</span>
                                    @if ($record->is_locked)
                                        <span class="badge bg-success rounded-pill"><i class="bi bi-lock-fill me-1"></i>Finalized</span>
                                    @else
                                        <span class="badge bg-warning text-dark rounded-pill"><i class="bi bi-unlock-fill me-1"></i>Ongoing</span>
                                    @endif
                                </span>
                            </div>
                        </button>
                    </h2>
                    <div id="rec{{ $i }}" class="accordion-collapse collapse" data-bs-parent="#recordsAccordion">
                        <div class="accordion-body p-0">
                            @if ($enr && $enr->subjects->isNotEmpty())
                                <table class="table table-sm table-hover mb-0 align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="px-4">Code</th>
                                            <th>Subject</th>
                                            <th class="text-center">Units</th>
                                            <th class="text-center">Grade</th>
                                            <th class="text-center px-4">Status</th>
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
                                                <td class="text-center px-4">
                                                    <span class="badge {{ $subject->pivot->status === 'passed' ? 'bg-success' : ($subject->pivot->status === 'failed' ? 'bg-danger' : 'bg-secondary') }} rounded-pill">{{ ucfirst($subject->pivot->status) }}</span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @else
                                <div class="p-3 text-muted small">No subject breakdown available for this semester.</div>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

@endsection
