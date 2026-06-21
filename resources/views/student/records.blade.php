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
                <p class="small text-muted mb-0">Records will appear here once the registrar finalizes and locks the semester records.</p>
            </div>
        </div>
    @else
        <div class="card border-0 shadow-sm rounded-3 overflow-hidden bg-white">
            <div class="card-header bg-white border-0 py-3">
                <h5 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                    <i class="bi bi-bar-chart-line-fill text-success fs-5"></i> Academic History
                </h5>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th class="px-4">School Year</th>
                            <th>Semester</th>
                            <th class="text-center">GPA</th>
                            <th class="text-center px-4">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($records as $record)
                            <tr>
                                <td class="px-4 fw-semibold text-dark">{{ $record->schoolYear->year_label ?? '—' }}</td>
                                <td class="fw-semibold text-secondary">{{ $record->semester }} Semester</td>
                                <td class="text-center fw-bold text-success fs-6">
                                    {{ $record->gpa !== null ? number_format($record->gpa, 2) : '—' }}
                                </td>
                                <td class="text-center px-4">
                                    @if ($record->is_locked)
                                        <span class="badge bg-success rounded-pill px-2.5 py-1.5 d-inline-flex align-items-center gap-1" style="font-size: 0.75rem;">
                                            <i class="bi bi-lock-fill"></i> Finalized
                                        </span>
                                    @else
                                        <span class="badge bg-warning text-dark rounded-pill px-2.5 py-1.5 d-inline-flex align-items-center gap-1" style="font-size: 0.75rem;">
                                            <i class="bi bi-unlock-fill"></i> Ongoing
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

@endsection
