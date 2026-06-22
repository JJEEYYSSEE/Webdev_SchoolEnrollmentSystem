@extends('layouts.registrar')
@section('title', 'Students')
@section('content')

    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4 pb-2 border-bottom">
        <div>
            <h3 class="fw-bold mb-0 text-dark">Students</h3>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('registrar.showDashboard') }}" class="text-decoration-none">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Students</li>
                </ol>
            </nav>
        </div>
        <span class="badge bg-primary px-3 py-2 rounded-pill fs-6 fw-semibold d-inline-flex align-items-center gap-1.5 shadow-sm">
            <i class="bi bi-people-fill"></i>
            <span>{{ $students->count() }} Total</span>
        </span>
    </div>

    {{-- Search + filters --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body py-3">
            <form method="GET" action="{{ route('registrar.showStudents') }}" class="row g-2 align-items-end">
                <div class="col-md-5">
                    <label for="search" class="form-label small fw-bold text-muted mb-1">Search</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search text-muted"></i></span>
                        <input id="search" type="text" name="search" value="{{ request('search') }}" class="form-control"
                               placeholder="Search by name or student number">
                    </div>
                </div>
                
                <div class="col-md-2.5 col-sm-6">
                    <label for="strand" class="form-label small fw-bold text-muted mb-1">Strand</label>
                    <select id="strand" name="strand" class="form-select">
                        <option value="">All strands</option>
                        @foreach ($strands as $strand)
                            <option value="{{ $strand->id }}" {{ request('strand') == $strand->id ? 'selected' : '' }}>{{ $strand->strand_code }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="col-md-2.5 col-sm-6">
                    <label for="grade" class="form-label small fw-bold text-muted mb-1">Grade Level</label>
                    <select id="grade" name="grade" class="form-select">
                        <option value="">All grades</option>
                        <option value="11" {{ request('grade') == '11' ? 'selected' : '' }}>Grade 11</option>
                        <option value="12" {{ request('grade') == '12' ? 'selected' : '' }}>Grade 12</option>
                    </select>
                </div>
                
                <div class="col-auto d-inline-flex gap-1.5 ms-auto">
                    <button type="submit" class="btn btn-primary px-4 d-inline-flex align-items-center gap-1">
                        <i class="bi bi-funnel-fill"></i> Filter
                    </button>
                    @if (request()->hasAny(['search', 'strand', 'grade']))
                        <a href="{{ route('registrar.showStudents') }}" class="btn btn-outline-secondary d-inline-flex align-items-center gap-1">
                            <i class="bi bi-x-lg"></i> Clear
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    @if ($students->isEmpty())
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-body text-center text-muted py-5">
                <i class="bi bi-inbox fs-1 mb-2 d-block opacity-50"></i>
                <span>No student records match the filters.</span>
            </div>
        </div>
    @else
        {{-- Folder-style grouping: Grade level → Strand --}}
        @foreach ($grouped as $gradeLabel => $byStrand)
            <div class="mb-4">
                <h5 class="fw-bold text-dark mt-4 mb-3 d-inline-flex align-items-center gap-2">
                    <i class="bi bi-folder2-open text-primary fs-5"></i>
                    <span>{{ $gradeLabel }}</span>
                </h5>
                
                @foreach ($byStrand as $strandCode => $group)
                    <div class="card border-0 shadow-sm mb-3 rounded-3 overflow-hidden">
                        <div class="card-header bg-light border-0 py-2.5 d-flex justify-content-between align-items-center">
                            <span class="fw-bold text-secondary-emphasis" style="font-size:0.9rem;">
                                <i class="bi bi-tag-fill me-1 text-muted"></i> {{ $strandCode }}
                            </span>
                            <span class="badge bg-secondary rounded-pill px-2 py-0.5" style="font-size:0.7rem;">{{ $group->count() }} Students</span>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover mb-0 align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th class="px-4">Student No.</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th class="text-end px-4">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($group as $student)
                                        <tr>
                                            <td class="px-4 text-muted fw-bold">{{ $student->student_number }}</td>
                                            <td>
                                                <a href="{{ route('registrar.showStudent', $student->id) }}" class="text-decoration-none fw-bold text-dark">
                                                    {{ $student->last_name }}, {{ $student->first_name }}
                                                </a>
                                            </td>
                                            <td class="text-secondary">{{ $student->user->email ?? '—' }}</td>
                                            <td class="text-end px-4">
                                                <div class="d-inline-flex gap-2">
                                                    <a href="{{ route('registrar.showStudent', $student->id) }}" class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center gap-1">
                                                        <i class="bi bi-eye"></i> View Profile
                                                    </a>
                                                    <a href="{{ route('registrar.showSemesterRecord', $student->id) }}" class="btn btn-sm btn-outline-primary d-inline-flex align-items-center gap-1">
                                                        <i class="bi bi-bar-chart-line"></i> Records
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endforeach
            </div>
        @endforeach
    @endif

@endsection
