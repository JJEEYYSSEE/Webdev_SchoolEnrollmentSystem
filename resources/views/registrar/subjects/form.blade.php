@extends('layouts.registrar')
@section('title', $subject ? 'Edit Subject' : 'Add Subject')
@section('content')

    @php
        $isEdit = $subject !== null;
        $formAction = $isEdit
            ? route('registrar.subjects.updateSubject', $subject->id)
            : route('registrar.subjects.postCreateSubject');
    @endphp

    {{-- Page Header --}}
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-4 pb-2 border-bottom">
        <div class="d-flex align-items-center gap-3">
            <a href="{{ route('registrar.subjects.showSubjects') }}" class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center gap-1">
                <i class="bi bi-arrow-left"></i> <span>Back</span>
            </a>
            <h4 class="fw-bold mb-0 text-dark">{{ $isEdit ? 'Edit Subject' : 'Add Subject' }}</h4>
        </div>
    </div>

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

    <div class="card border-0 shadow-sm rounded-3 bg-white">
        <div class="card-header bg-white border-0 py-3 fw-bold text-dark d-flex align-items-center gap-2">
            <i class="bi bi-info-circle text-primary fs-5"></i>
            <span>Subject Details</span>
        </div>
        <div class="card-body pt-0">
            <form method="POST" action="{{ $formAction }}">
                @csrf
                @if ($isEdit) @method('PUT') @endif

                <div class="row g-3">
                    <div class="col-md-3">
                        <label for="subject_code" class="form-label fw-semibold small text-muted">Subject Code <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-bookmark-fill"></i></span>
                            <input id="subject_code" name="subject_code" type="text" class="form-control @error('subject_code') is-invalid @enderror"
                                   value="{{ old('subject_code', $subject->subject_code ?? '') }}"
                                   placeholder="e.g. CORE-ORALCOM" required>
                        </div>
                    </div>
                    
                    <div class="col-md-7">
                        <label for="subject_name" class="form-label fw-semibold small text-muted">Subject Name <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-file-earmark-text-fill"></i></span>
                            <input id="subject_name" name="subject_name" type="text" class="form-control @error('subject_name') is-invalid @enderror"
                                   value="{{ old('subject_name', $subject->subject_name ?? '') }}"
                                   placeholder="e.g. Oral Communication" required>
                        </div>
                    </div>
                    
                    <div class="col-md-2">
                        <label for="units" class="form-label fw-semibold small text-muted">Units <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-hash"></i></span>
                            <input id="units" name="units" type="number" min="1" max="6" class="form-control @error('units') is-invalid @enderror"
                                   value="{{ old('units', $subject->units ?? '') }}" required>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                    <a href="{{ route('registrar.subjects.showSubjects') }}" class="btn btn-outline-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary px-4 d-inline-flex align-items-center gap-1">
                        <i class="bi bi-check-lg"></i> {{ $isEdit ? 'Update Subject' : 'Create Subject' }}
                    </button>
                </div>
            </form>
        </div>
    </div>

@endsection
