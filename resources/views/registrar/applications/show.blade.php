@extends('layouts.registrar')
@section('title', 'Review Application')
@section('content')

    @php
        $a   = $application;
        $ref = 'APP-'.now()->year.'-'.str_pad($a->id, 5, '0', STR_PAD_LEFT);
        $row = fn ($label, $value) => '<dt class="col-sm-4 small text-muted fw-normal">'.$label.'</dt><dd class="col-sm-8 small fw-semibold">'.e($value ?: '—').'</dd>';
        $badge = ['pending' => 'bg-warning-subtle text-warning-emphasis', 'invalid' => 'bg-warning-subtle text-warning-emphasis', 'qualified' => 'bg-success-subtle text-success-emphasis'];
    @endphp

    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h3 class="fw-bold mb-0 text-dark">{{ $a->fullName() }}</h3>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('registrar.showApplications') }}" class="text-decoration-none">Applications</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $ref }}</li>
                </ol>
            </nav>
        </div>
        <span class="badge {{ $badge[$a->status] ?? 'bg-secondary' }} fs-6 text-capitalize">{{ $a->status === 'invalid' ? 'Returned' : $a->status }}</span>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger py-2 small">{{ $errors->first() }}</div>
    @endif

    <div class="row g-4">
        {{-- Left: details --}}
        <div class="col-lg-7 d-flex flex-column gap-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="fw-bold text-primary mb-3"><i class="bi bi-person-fill me-1"></i> Personal Information</h6>
                    <dl class="row mb-0">
                        {!! $row('LRN', $a->lrn) !!}
                        {!! $row('Full Name', trim("$a->first_name $a->middle_name $a->last_name $a->extension_name")) !!}
                        {!! $row('Sex', $a->sex) !!}
                        {!! $row('Birthdate', optional($a->birthdate)->format('M d, Y')) !!}
                        {!! $row('Place of Birth', $a->place_of_birth) !!}
                        {!! $row('Mobile', $a->mobile) !!}
                        {!! $row('Email', $a->user->email) !!}
                        {!! $row('Address', "$a->current_address, $a->current_barangay, $a->current_city, $a->current_province") !!}
                        {!! $row('Father', $a->father_name.' '.($a->father_contact ? "($a->father_contact)" : '')) !!}
                        {!! $row('Mother', $a->mother_name.' '.($a->mother_contact ? "($a->mother_contact)" : '')) !!}
                        {!! $row('Guardian', $a->guardian_name) !!}
                    </dl>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="fw-bold text-primary mb-1"><i class="bi bi-mortarboard-fill me-1"></i> Educational Background</h6>
                    <p class="text-muted small mb-3">Cross-check these against the uploaded SF10 / report card.</p>
                    <dl class="row mb-0">
                        {!! $row('Junior High School', $a->jhs_name) !!}
                        {!! $row('JHS School ID', $a->jhs_school_id) !!}
                        {!! $row('Year Graduated', $a->jhs_year_graduated) !!}
                        {!! $row('General Average', $a->general_average) !!}
                        {!! $row('Elementary School', $a->elementary_name) !!}
                        {!! $row('Strand Applied', optional($a->strand)->strand_name) !!}
                        {!! $row('Grade Level', 'Grade '.$a->grade_level) !!}
                        {!! $row('Returning / Transferee', ($a->is_returning ? 'Returning ' : '').($a->is_transferee ? 'Transferee' : '') ?: 'No') !!}
                    </dl>
                </div>
            </div>
        </div>

        {{-- Right: documents + actions --}}
        <div class="col-lg-5 d-flex flex-column gap-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="fw-bold text-primary mb-3"><i class="bi bi-folder2-open me-1"></i> Uploaded Documents</h6>
                    @forelse ($a->documents as $doc)
                        <a href="{{ $doc->url() }}" target="_blank"
                           class="d-flex align-items-center justify-content-between border rounded-3 p-2 mb-2 text-decoration-none">
                            <span class="small text-dark"><i class="bi bi-file-earmark-text me-1 text-primary"></i>{{ $doc->original_name ?? $doc->type }}</span>
                            <i class="bi bi-box-arrow-up-right text-muted"></i>
                        </a>
                    @empty
                        <p class="text-muted small mb-0">No documents uploaded.</p>
                    @endforelse
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="fw-bold text-primary mb-3"><i class="bi bi-clipboard-check me-1"></i> Decision</h6>

                    @if ($a->isPending())
                        <form method="POST" action="{{ route('registrar.returnApplication', $a) }}" class="mb-3">
                            @csrf
                            <label class="form-label small fw-semibold">Return for compliance — reason</label>
                            <textarea name="remarks" rows="3" class="form-control mb-2 @error('remarks') is-invalid @enderror"
                                      placeholder="e.g. SF10 is unreadable, please re-upload a clear copy.">{{ old('remarks') }}</textarea>
                            @error('remarks') <div class="invalid-feedback d-block mb-2">{{ $message }}</div> @enderror
                            <button type="submit" class="btn btn-warning w-100" data-loading-text="Returning…">
                                <i class="bi bi-arrow-return-left me-1"></i> Return as Invalid
                            </button>
                        </form>

                        <button class="btn btn-success w-100" disabled title="Available in the next phase">
                            <i class="bi bi-patch-check me-1"></i> Qualify &amp; Issue School ID
                        </button>
                        <p class="text-muted small text-center mt-2 mb-0">Qualify / waitlist + School ID issuance comes in Phase 2.</p>
                    @elseif ($a->isInvalid())
                        <div class="alert alert-warning small mb-0">
                            <strong>Returned for compliance.</strong>
                            <div>{{ $a->remarks }}</div>
                            <div class="text-muted mt-1">Awaiting the applicant's re-submission.</div>
                        </div>
                    @else
                        <div class="alert alert-success small mb-0"><strong>Qualified.</strong> School ID issued.</div>
                    @endif
                </div>
            </div>
        </div>
    </div>

@endsection
