@extends('layouts.registrar')
@section('title', $section ? 'Edit Section' : 'Add Section')
@section('content')

    @php
        $isEdit = $section !== null;
        $formAction = $isEdit
            ? route('registrar.sections.updateSection', $section->id)
            : route('registrar.sections.postCreateSection');
        $assigned = $isEdit ? $section->subjects->pluck('id')->all() : [];
    @endphp

    {{-- Page Header --}}
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-4 pb-2 border-bottom">
        <div class="d-flex align-items-center gap-3">
            <a href="{{ route('registrar.sections.showSections') }}" class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center gap-1">
                <i class="bi bi-arrow-left"></i> <span>Back</span>
            </a>
            <h4 class="fw-bold mb-0 text-dark">{{ $isEdit ? 'Edit Section' : 'Add Section' }}</h4>
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

    <form method="POST" action="{{ $formAction }}">
        @csrf
        @if ($isEdit) @method('PUT') @endif

        {{-- Section Details --}}
        <div class="card border-0 shadow-sm mb-4 rounded-3">
            <div class="card-header bg-white border-0 py-3 fw-bold text-dark d-flex align-items-center gap-2">
                <i class="bi bi-info-circle text-primary fs-5"></i>
                <span>Section Details</span>
            </div>
            <div class="card-body pt-0">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="section_name" class="form-label fw-semibold small text-muted">Section Name <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-collection"></i></span>
                            <input id="section_name" name="section_name" type="text" class="form-control @error('section_name') is-invalid @enderror"
                                   value="{{ old('section_name', $section->section_name ?? '') }}"
                                   placeholder="e.g. Kasipagan" required>
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <label for="strand_id" class="form-label fw-semibold small text-muted">Strand <span class="text-danger">*</span></label>
                        <select id="strand_id" name="strand_id" class="form-select @error('strand_id') is-invalid @enderror" required>
                            <option value="" disabled {{ old('strand_id', $section->strand_id ?? '') ? '' : 'selected' }}>Select Strand</option>
                            @foreach ($strands as $strand)
                                <option value="{{ $strand->id }}" {{ old('strand_id', $section->strand_id ?? '') == $strand->id ? 'selected' : '' }}>{{ $strand->strand_code }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="col-md-3">
                        <label for="grade_level" class="form-label fw-semibold small text-muted">Grade <span class="text-danger">*</span></label>
                        <select id="grade_level" name="grade_level" class="form-select @error('grade_level') is-invalid @enderror" required>
                            <option value="11" {{ old('grade_level', $section->grade_level ?? '') == '11' ? 'selected' : '' }}>Grade 11</option>
                            <option value="12" {{ old('grade_level', $section->grade_level ?? '') == '12' ? 'selected' : '' }}>Grade 12</option>
                        </select>
                    </div>
                    
                    <div class="col-md-3">
                        <label for="semester" class="form-label fw-semibold small text-muted">Semester <span class="text-danger">*</span></label>
                        <select id="semester" name="semester" class="form-select @error('semester') is-invalid @enderror" required>
                            <option value="1st" {{ old('semester', $section->semester ?? '') == '1st' ? 'selected' : '' }}>1st Semester</option>
                            <option value="2nd" {{ old('semester', $section->semester ?? '') == '2nd' ? 'selected' : '' }}>2nd Semester</option>
                        </select>
                    </div>
                    
                    <div class="col-md-3">
                        <label for="time_period" class="form-label fw-semibold small text-muted">Time Period <span class="text-danger">*</span></label>
                        <select id="time_period" name="time_period" class="form-select @error('time_period') is-invalid @enderror" required>
                            <option value="AM" {{ old('time_period', $section->time_period ?? '') == 'AM' ? 'selected' : '' }}>AM (Morning)</option>
                            <option value="PM" {{ old('time_period', $section->time_period ?? '') == 'PM' ? 'selected' : '' }}>PM (Afternoon)</option>
                        </select>
                    </div>
                    
                    <div class="col-md-3">
                        <label for="max_capacity" class="form-label fw-semibold small text-muted">Max Capacity <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-people"></i></span>
                            <input id="max_capacity" name="max_capacity" type="number" min="1" max="100" class="form-control @error('max_capacity') is-invalid @enderror"
                                   value="{{ old('max_capacity', $section->max_capacity ?? 40) }}" required>
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <label for="school_year_id" class="form-label fw-semibold small text-muted">School Year <span class="text-danger">*</span></label>
                        <select id="school_year_id" name="school_year_id" class="form-select @error('school_year_id') is-invalid @enderror" required>
                            @foreach ($schoolYears as $sy)
                                <option value="{{ $sy->id }}" {{ old('school_year_id', $section->school_year_id ?? '') == $sy->id ? 'selected' : '' }}>
                                    {{ $sy->year_label }}{{ $sy->is_active ? ' (active)' : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>

        {{-- Assigned Subjects --}}
        <div class="card border-0 shadow-sm mb-4 rounded-3">
            <div class="card-header bg-white border-0 py-3 fw-bold text-dark d-flex align-items-center gap-2">
                <i class="bi bi-book-fill text-primary fs-5"></i>
                <div>
                    <span>Assigned Subjects</span>
                    <span class="text-muted small fw-normal ms-2">(Auto-enrolled for students in this section)</span>
                </div>
            </div>
            <div class="card-body pt-0">
                <div class="row g-3">
                    @foreach ($allSubjects as $subject)
                        <div class="col-md-6 col-lg-4">
                            <label class="card h-100 border p-3 rounded-3 style-checkbox-card" style="cursor: pointer;">
                                <div class="d-flex align-items-start gap-2">
                                    <input class="form-check-input flex-shrink-0 mt-1" type="checkbox" name="subject_ids[]"
                                           value="{{ $subject->id }}" id="subj{{ $subject->id }}"
                                           {{ in_array($subject->id, old('subject_ids', $assigned)) ? 'checked' : '' }}>
                                    <div>
                                        <div class="fw-bold text-dark small">{{ $subject->subject_code }}</div>
                                        <div class="text-muted small" style="font-size: 0.75rem;">{{ $subject->subject_name }} ({{ $subject->units }} Units)</div>
                                    </div>
                                </div>
                            </label>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2">
            <a href="{{ route('registrar.sections.showSections') }}" class="btn btn-outline-secondary">Cancel</a>
            <button type="submit" class="btn btn-primary px-4 d-inline-flex align-items-center gap-1">
                <i class="bi bi-check-lg"></i> {{ $isEdit ? 'Update Section' : 'Create Section' }}
            </button>
        </div>
    </form>

@endsection
