@php $a = $application; @endphp

<form method="POST" action="{{ route('application.save') }}">
    @csrf
    <input type="hidden" name="step" value="2">

    <h6 class="fw-bold text-primary mb-1"><i class="bi bi-mortarboard-fill me-1"></i> Educational Background</h6>
    <p class="text-muted small mb-3">These details are checked against your uploaded SF10 / report card for consistency.</p>

    <div class="row g-3 mb-3">
        <div class="col-md-8">
            <label class="form-label small fw-semibold">Junior High School Completed <span class="text-danger">*</span></label>
            <input type="text" name="jhs_name" value="{{ old('jhs_name', $a->jhs_name) }}"
                   class="form-control @error('jhs_name') is-invalid @enderror" required>
            @error('jhs_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <div class="col-md-4">
            <label class="form-label small fw-semibold">JHS School ID</label>
            <input type="text" name="jhs_school_id" value="{{ old('jhs_school_id', $a->jhs_school_id) }}" class="form-control">
        </div>

        <div class="col-md-4">
            <label class="form-label small fw-semibold">Year Graduated (Grade 10) <span class="text-danger">*</span></label>
            <input type="text" name="jhs_year_graduated" value="{{ old('jhs_year_graduated', $a->jhs_year_graduated) }}"
                   class="form-control @error('jhs_year_graduated') is-invalid @enderror" placeholder="e.g. 2025" required>
            @error('jhs_year_graduated') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <div class="col-md-4">
            <label class="form-label small fw-semibold">General Average (Grade 10) <span class="text-danger">*</span></label>
            <input type="number" step="0.01" min="0" max="100" name="general_average" value="{{ old('general_average', $a->general_average) }}"
                   class="form-control @error('general_average') is-invalid @enderror" required>
            @error('general_average') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="col-md-8">
            <label class="form-label small fw-semibold">Elementary School <span class="text-danger">*</span></label>
            <input type="text" name="elementary_name" value="{{ old('elementary_name', $a->elementary_name) }}"
                   class="form-control @error('elementary_name') is-invalid @enderror" required>
            @error('elementary_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <div class="col-md-4">
            <label class="form-label small fw-semibold">Year Graduated</label>
            <input type="text" name="elementary_year_graduated" value="{{ old('elementary_year_graduated', $a->elementary_year_graduated) }}" class="form-control" placeholder="e.g. 2021">
        </div>
    </div>

    <div class="row g-3 mb-3">
        <div class="col-md-6">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="is_returning" value="1" id="is_returning" @checked(old('is_returning', $a->is_returning))>
                <label class="form-check-label small" for="is_returning">Returning learner (balik-aral)</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="is_transferee" value="1" id="is_transferee" @checked(old('is_transferee', $a->is_transferee))>
                <label class="form-check-label small" for="is_transferee">Transferee</label>
            </div>
        </div>
        <div class="col-md-6">
            <label class="form-label small fw-semibold">Previous School <span class="text-muted fw-normal">(if transferee)</span></label>
            <input type="text" name="previous_school" value="{{ old('previous_school', $a->previous_school) }}" class="form-control">
        </div>
    </div>

    <h6 class="fw-bold text-primary mb-3 mt-4 pt-2 border-top"><i class="bi bi-bookmark-star-fill me-1"></i> Track / Strand</h6>
    <div class="row g-3 mb-3">
        <div class="col-md-8">
            <label class="form-label small fw-semibold">Strand applying for <span class="text-danger">*</span></label>
            <select name="strand_id" class="form-select @error('strand_id') is-invalid @enderror" required>
                <option value="" disabled {{ old('strand_id', $a->strand_id) ? '' : 'selected' }}>— Select strand —</option>
                @foreach ($strands as $strand)
                    <option value="{{ $strand->id }}" @selected(old('strand_id', $a->strand_id) == $strand->id)>
                        {{ $strand->strand_code }} — {{ $strand->strand_name }}
                    </option>
                @endforeach
            </select>
            @error('strand_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <div class="col-md-4">
            <label class="form-label small fw-semibold">Grade Level</label>
            <input type="text" class="form-control" value="Grade 11" disabled>
        </div>
    </div>

    <div class="d-flex justify-content-between pt-3 border-top">
        <button type="submit" name="direction" value="back" class="btn btn-outline-secondary" formnovalidate>
            <i class="bi bi-arrow-left me-1"></i> Back
        </button>
        <button type="submit" name="direction" value="next" class="btn btn-primary" data-loading-text="Saving…">
            Next <i class="bi bi-arrow-right ms-1"></i>
        </button>
    </div>
</form>
