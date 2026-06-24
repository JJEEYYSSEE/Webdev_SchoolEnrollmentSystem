@extends('layouts.registrar')
@section('title', 'Subjects')
@section('content')

    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4 pb-2 border-bottom">
        <div>
            <h3 class="fw-bold mb-0 text-dark">Subjects</h3>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('registrar.showDashboard') }}" class="text-decoration-none">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Subjects</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('registrar.subjects.showCreateSubject') }}" class="btn btn-primary d-inline-flex align-items-center gap-1">
            <i class="bi bi-plus-lg"></i> Add Subject
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

    @if ($subjects->isEmpty())
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-body text-center text-muted py-5">
                <i class="bi bi-inbox fs-1 mb-2 d-block opacity-50"></i>
                <span>No subjects found. Add a subject to get started.</span>
            </div>
        </div>
    @else
        @foreach ($grouped as $prefix => $group)
            <div class="mb-4">
                <h5 class="fw-bold text-dark mt-4 mb-3 d-inline-flex align-items-center gap-2">
                    <i class="bi bi-folder2-open text-primary fs-5"></i>
                    <span>{{ $prefix }} Subjects</span>
                </h5>
                
                <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th class="px-4">Code</th>
                                    <th>Subject Name</th>
                                    <th class="text-center">Units</th>
                                    <th class="text-end px-4">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($group as $subject)
                                    <tr>
                                        <td class="px-4 fw-bold text-dark">{{ $subject->subject_code }}</td>
                                        <td class="fw-semibold text-secondary-emphasis">{{ $subject->subject_name }}</td>
                                        <td class="text-center fw-bold text-muted">{{ $subject->units }}</td>
                                        <td class="text-end px-4">
                                            <div class="d-inline-flex gap-2">
                                                <a href="{{ route('registrar.subjects.showEditSubject', $subject->id) }}" class="btn btn-sm btn-outline-primary d-inline-flex align-items-center gap-1">
                                                    <i class="bi bi-pencil"></i> Edit
                                                </a>
                                                <form method="POST" action="{{ route('registrar.subjects.deleteSubject', $subject->id) }}" class="d-inline" data-confirm="Delete this subject? This cannot be undone." data-confirm-title="Delete Subject" data-confirm-ok="Delete" data-confirm-danger>
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
            </div>
        @endforeach
    @endif

@endsection
