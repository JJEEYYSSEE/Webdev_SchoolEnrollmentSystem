@php
    $a       = $application;
    $existing = $a->documents->keyBy('type');
@endphp

<form method="POST" action="{{ route('application.save') }}" enctype="multipart/form-data">
    @csrf
    <input type="hidden" name="step" value="3">

    <h6 class="fw-bold text-primary mb-1"><i class="bi bi-cloud-arrow-up-fill me-1"></i> Upload Documents</h6>
    <p class="text-muted small mb-3">PDF, JPG, or PNG — max 5 MB each. Re-uploading replaces the previous file.</p>

    @error('documents')
        <div class="alert alert-danger py-2 small"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</div>
    @enderror

    <div class="d-flex flex-column gap-3 mb-3">
        @foreach ($documentTypes as $type => $label)
            <div class="border rounded-3 p-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <label class="form-label fw-semibold mb-0">{{ $label }} <span class="text-danger">*</span></label>
                    @if ($existing->has($type))
                        <a href="{{ $existing[$type]->url() }}" target="_blank" class="badge bg-success-subtle text-success text-decoration-none">
                            <i class="bi bi-check-circle-fill me-1"></i>{{ $existing[$type]->original_name ?? 'Uploaded' }}
                        </a>
                    @else
                        <span class="badge bg-secondary-subtle text-secondary">Not uploaded</span>
                    @endif
                </div>
                <input type="file" name="documents[{{ $type }}]" accept=".pdf,.jpg,.jpeg,.png"
                       class="form-control @error('documents.'.$type) is-invalid @enderror">
                @error('documents.'.$type) <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
        @endforeach
    </div>

    <div class="d-flex justify-content-between pt-3 border-top">
        <button type="submit" name="direction" value="back" class="btn btn-outline-secondary" formnovalidate>
            <i class="bi bi-arrow-left me-1"></i> Back
        </button>
        <button type="submit" name="direction" value="next" class="btn btn-primary" data-loading-text="Uploading…">
            Next <i class="bi bi-arrow-right ms-1"></i>
        </button>
    </div>
</form>
