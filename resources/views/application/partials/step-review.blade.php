@php
    $a = $application;
    $row = fn ($label, $value) => '<dt class="col-sm-5 small text-muted fw-normal">'.$label.'</dt><dd class="col-sm-7 small fw-semibold">'.e($value ?: '—').'</dd>';
@endphp

<h6 class="fw-bold text-primary mb-3"><i class="bi bi-clipboard-check-fill me-1"></i> Review Your Application</h6>

@error('documents')
    <div class="alert alert-danger py-2 small">{{ $message }}</div>
@enderror

<div class="accordion mb-3" id="reviewAccordion">

    <div class="accordion-item">
        <h2 class="accordion-header">
            <button class="accordion-button small fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#rvPersonal">
                Personal Information
            </button>
        </h2>
        <div id="rvPersonal" class="accordion-collapse collapse show" data-bs-parent="#reviewAccordion">
            <div class="accordion-body">
                <dl class="row mb-0">
                    {!! $row('LRN', $a->lrn) !!}
                    {!! $row('Name', trim("$a->first_name $a->middle_name $a->last_name $a->extension_name")) !!}
                    {!! $row('Sex', $a->sex) !!}
                    {!! $row('Birthdate', optional($a->birthdate)->format('M d, Y')) !!}
                    {!! $row('Place of Birth', $a->place_of_birth) !!}
                    {!! $row('Mother Tongue', $a->mother_tongue) !!}
                    {!! $row('Mobile', $a->mobile) !!}
                    {!! $row('Address', "$a->current_address, $a->current_barangay, $a->current_city, $a->current_province") !!}
                    {!! $row('Father', $a->father_name) !!}
                    {!! $row('Mother', $a->mother_name) !!}
                    {!! $row('Guardian', $a->guardian_name) !!}
                </dl>
            </div>
        </div>
    </div>

    <div class="accordion-item">
        <h2 class="accordion-header">
            <button class="accordion-button small fw-bold collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#rvEducation">
                Educational Background
            </button>
        </h2>
        <div id="rvEducation" class="accordion-collapse collapse" data-bs-parent="#reviewAccordion">
            <div class="accordion-body">
                <dl class="row mb-0">
                    {!! $row('Junior High School', $a->jhs_name) !!}
                    {!! $row('Year Graduated', $a->jhs_year_graduated) !!}
                    {!! $row('General Average', $a->general_average) !!}
                    {!! $row('Elementary School', $a->elementary_name) !!}
                    {!! $row('Strand', optional($a->strand)->strand_code) !!}
                    {!! $row('Grade Level', 'Grade '.$a->grade_level) !!}
                </dl>
            </div>
        </div>
    </div>

    <div class="accordion-item">
        <h2 class="accordion-header">
            <button class="accordion-button small fw-bold collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#rvDocs">
                Documents ({{ $a->documents->count() }})
            </button>
        </h2>
        <div id="rvDocs" class="accordion-collapse collapse" data-bs-parent="#reviewAccordion">
            <div class="accordion-body">
                <ul class="list-unstyled mb-0 small">
                    @forelse ($a->documents as $doc)
                        <li class="mb-1">
                            <i class="bi bi-file-earmark-check text-success me-1"></i>
                            <a href="{{ $doc->url() }}" target="_blank">{{ $doc->original_name ?? $doc->type }}</a>
                        </li>
                    @empty
                        <li class="text-muted">No documents uploaded.</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
</div>

<form method="POST" action="{{ route('application.submit') }}">
    @csrf

    <div class="form-check mb-3">
        <input class="form-check-input" type="checkbox" id="certify" required>
        <label class="form-check-label small" for="certify">
            I certify that all information provided is true and correct to the best of my knowledge.
        </label>
    </div>

    <div class="d-flex justify-content-between pt-3 border-top">
        <button type="submit" form="reviewBack" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Back
        </button>
        <button type="submit" class="btn btn-success" data-loading-text="Submitting…">
            <i class="bi bi-send-fill me-1"></i> Submit Application
        </button>
    </div>
</form>

{{-- Separate back form (review submit posts to application.submit) --}}
<form method="POST" action="{{ route('application.save') }}" id="reviewBack" class="d-none">
    @csrf
    <input type="hidden" name="step" value="4">
    <input type="hidden" name="direction" value="back">
</form>
