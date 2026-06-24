@extends('layouts.registrar')
@section('title', 'Encode Grades')
@section('content')

    {{-- Page Header --}}
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-4 pb-2 border-bottom">
        <div class="d-flex align-items-center gap-3">
            <a href="{{ route('registrar.showEnrollment', $enrollment->id) }}" class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center gap-1">
                <i class="bi bi-arrow-left"></i> <span>Back</span>
            </a>
            <h4 class="fw-bold mb-0 text-dark">Encode Grades</h4>
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

    {{-- Student Info Header Card --}}
    <div class="card border-0 border-start border-4 border-primary shadow-sm mb-4 bg-white">
        <div class="card-body py-3">
            <h5 class="fw-bold mb-1 text-dark">{{ $enrollment->student->first_name }} {{ $enrollment->student->last_name }}</h5>
            <div class="d-flex align-items-center gap-2 text-muted small">
                <span>Student No: <strong>{{ $enrollment->student->student_number }}</strong></span>
                &middot;
                <span>Section: <strong class="text-secondary">{{ $enrollment->section->strand->strand_code ?? '' }} - {{ $enrollment->section->section_name }}</strong></span>
            </div>
        </div>
    </div>

    {{-- Grade Form --}}
    <form method="POST" action="{{ route('registrar.updateGrades', $enrollment->id) }}">
        @csrf 
        @method('PUT')

        <div class="card border-0 shadow-sm rounded-3 overflow-hidden mb-4">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th class="px-4">Code</th>
                            <th>Subject</th>
                            <th style="width: 180px;">Grade (60–100)</th>
                            <th style="width: 200px;" class="px-4">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($enrollment->enrollmentSubjects as $es)
                            <tr>
                                <td class="px-4 text-muted fw-bold">{{ $es->subject->subject_code }}</td>
                                <td>
                                    <div class="fw-semibold text-dark">{{ $es->subject->subject_name }}</div>
                                    <div class="small text-muted" style="font-size: 0.75rem;">Units: {{ $es->subject->units }}</div>
                                </td>
                                <td>
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text"><i class="bi bi-bookmark-star"></i></span>
                                        <input type="number" step="0.01" min="60" max="100"
                                               name="grades[{{ $es->id }}][grade]" value="{{ $es->grade }}"
                                               class="form-control" placeholder="—">
                                    </div>
                                </td>
                                <td class="px-4">
                                    <select name="grades[{{ $es->id }}][status]" class="form-select form-select-sm">
                                        @foreach (['enrolled', 'passed', 'failed', 'dropped'] as $st)
                                            <option value="{{ $st }}" {{ $es->status === $st ? 'selected' : '' }}>{{ ucfirst($st) }}</option>
                                        @endforeach
                                    </select>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2">
            <a href="{{ route('registrar.showEnrollment', $enrollment->id) }}" class="btn btn-outline-secondary">Cancel</a>
            <button type="submit" class="btn btn-primary d-inline-flex align-items-center gap-1">
                <i class="bi bi-floppy"></i> Save Grades
            </button>
        </div>
    </form>

@endsection
