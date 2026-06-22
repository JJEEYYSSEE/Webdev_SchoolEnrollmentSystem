@extends('layouts.registrar')
@section('title', 'Sections')
@section('content')

    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4 pb-2 border-bottom">
        <div>
            <h3 class="fw-bold mb-0 text-dark">Sections</h3>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('registrar.showDashboard') }}" class="text-decoration-none">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Sections</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('registrar.sections.showCreateSection') }}" class="btn btn-primary d-inline-flex align-items-center gap-1">
            <i class="bi bi-plus-lg"></i> Add Section
        </a>
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

    @if ($sections->isEmpty())
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-body text-center text-muted py-5">
                <i class="bi bi-inbox fs-1 mb-2 d-block opacity-50"></i>
                <span>No sections found. Add a section to get started.</span>
            </div>
        </div>
    @else
        @foreach ($grouped as $gradeLabel => $byStrand)
            <div class="mb-4">
                <h5 class="fw-bold text-dark mt-4 mb-3 d-inline-flex align-items-center gap-2">
                    <i class="bi bi-folder2-open text-primary fs-5"></i>
                    <span>{{ $gradeLabel }}</span>
                </h5>
                
                @foreach ($byStrand as $strandCode => $group)
                    <div class="card border-0 shadow-sm mb-3 rounded-3 overflow-hidden">
                        <div class="card-header bg-light border-0 py-2.5 fw-bold text-secondary-emphasis" style="font-size: 0.9rem;">
                            <i class="bi bi-tag-fill me-1 text-muted"></i> {{ $strandCode }}
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover mb-0 align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th class="px-4">Section Name</th>
                                        <th>Time</th>
                                        <th>Semester</th>
                                        <th>School Year</th>
                                        <th class="text-center">Slots</th>
                                        <th class="text-end px-4">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($group as $section)
                                        <tr>
                                            <td class="px-4 fw-bold text-dark">{{ $section->section_name }}</td>
                                            <td>
                                                <span class="badge bg-light text-dark border px-2.5 py-1.5" style="font-size: 0.75rem;">
                                                    <i class="bi bi-clock-fill me-1 opacity-70"></i> {{ $section->time_period }}
                                                </span>
                                            </td>
                                            <td class="text-secondary fw-semibold">{{ $section->semester }} Semester</td>
                                            <td class="text-muted">{{ $section->schoolYear->year_label ?? '—' }}</td>
                                            <td class="text-center">
                                                <div class="d-flex flex-column align-items-center">
                                                    <span class="fw-semibold text-dark">{{ $section->approved_count }} / {{ $section->max_capacity }}</span>
                                                    @if ($section->approved_count >= $section->max_capacity)
                                                        <span class="badge bg-danger rounded-pill px-2 py-0.5 mt-1" style="font-size:0.65rem;">Full</span>
                                                    @else
                                                        @php
                                                            $percent = ($section->approved_count / $section->max_capacity) * 100;
                                                            $progressBg = $percent > 85 ? 'bg-warning' : 'bg-success';
                                                        @endphp
                                                        <div class="progress mt-1" style="width: 60px; height: 5px;">
                                                            <div class="progress-bar {{ $progressBg }}" role="progressbar" style="width: {{ $percent }}%"></div>
                                                        </div>
                                                    @endif
                                                </div>
                                            </td>
                                            <td class="text-end px-4">
                                                <div class="d-inline-flex gap-2">
                                                    <a href="{{ route('registrar.sections.showSchedule', $section->id) }}" class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center gap-1">
                                                        <i class="bi bi-calendar-week"></i> Schedule
                                                    </a>
                                                    <a href="{{ route('registrar.sections.showEditSection', $section->id) }}" class="btn btn-sm btn-outline-primary d-inline-flex align-items-center gap-1">
                                                        <i class="bi bi-pencil"></i> Edit
                                                    </a>
                                                    <form method="POST" action="{{ route('registrar.sections.deleteSection', $section->id) }}" class="d-inline" onsubmit="return confirm('Delete this section? This will delete all its links.')">
                                                        @csrf 
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger d-inline-flex align-items-center gap-1">
                                                            <i class="bi bi-trash"></i> Delete
                                                        </button>
                                                    </form>
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
