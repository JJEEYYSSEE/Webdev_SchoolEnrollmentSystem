@extends('layouts.student')
@section('title', 'Online Enrollment')
@section('content')

    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4 pb-2 border-bottom">
        <div>
            <h3 class="fw-bold mb-0 text-dark">Online Enrollment</h3>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('student.showDashboard') }}" class="text-decoration-none">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Enrollment Form</li>
                </ol>
            </nav>
        </div>
    </div>

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center gap-2 border-0 shadow-sm mb-4" role="alert">
            <i class="bi bi-exclamation-triangle-fill text-danger fs-5"></i>
            <div>{{ session('error') }}</div>
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

    {{-- Gate: enrollment closed --}}
    @if ($blocked === 'closed')
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-body text-center py-5">
                <div class="rounded-circle bg-light d-inline-flex align-items-center justify-content-center mb-3" style="width: 72px; height: 72px;">
                    <i class="bi bi-lock-fill text-muted fs-2"></i>
                </div>
                <h5 class="fw-bold mt-2 text-dark">Enrollment is currently closed</h5>
                <p class="text-muted small mx-auto mb-0" style="max-width: 460px;">
                    @if ($schoolYear)
                        Enrollment for S.Y. {{ $schoolYear->year_label }} has not been opened by the registrar yet. Please check back later.
                    @else
                        There is no active school year configured by the registrar at the moment.
                    @endif
                </p>
            </div>
        </div>

    {{-- Gate: already enrolled --}}
    @elseif ($blocked === 'enrolled')
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-body text-center py-5">
                <div class="rounded-circle bg-success bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-3" style="width: 72px; height: 72px;">
                    <i class="bi bi-check-circle-fill text-success fs-2"></i>
                </div>
                <h5 class="fw-bold mt-2 text-dark">You're already enrolled this semester</h5>
                <p class="text-muted small mx-auto mb-4" style="max-width: 460px;">
                    Your enrollment status is currently: <strong class="text-dark">{{ ucfirst($existing->status) }}</strong>. You can monitor your enrollment updates on the status page.
                </p>
                <a href="{{ route('student.showEnrollStatus') }}" class="btn btn-success d-inline-flex align-items-center gap-1">
                    <i class="bi bi-arrow-right"></i> View Status Page
                </a>
            </div>
        </div>

    {{-- Section picker --}}
    @else
        <div class="alert alert-info border-0 d-flex gap-3 mb-4 shadow-sm rounded-3">
            <i class="bi bi-info-circle-fill fs-4 text-info"></i>
            <div>
                <div class="fw-bold text-dark">Enrollment Settings</div>
                <div class="small text-muted">
                    Showing sections for <strong>{{ $student->strand?->strand_code }}</strong>,
                    <strong>Grade {{ $student->grade_level }}</strong>,
                    S.Y. {{ $schoolYear->year_label }} &middot; {{ $schoolYear->active_semester }} Semester.
                    Subjects are fixed per section — picking a section enrolls you in all its subjects.
                </div>
            </div>
        </div>

        @if (!empty($invalidRemarks))
            <div class="alert alert-warning d-flex align-items-start gap-2 border-0 shadow-sm mb-4">
                <i class="bi bi-exclamation-triangle-fill fs-5"></i>
                <div>
                    <strong>Your previous submission was returned for compliance.</strong>
                    <div class="small">{{ $invalidRemarks }}</div>
                    <div class="small text-muted mt-1">Fix the issue, then re-submit your enrollment below.</div>
                </div>
            </div>
        @endif

        @if ($sections->isEmpty())
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body text-center text-muted py-5">
                    <i class="bi bi-inbox fs-1 mb-2 d-block opacity-50"></i>
                    <span>No sections available for your strand and grade level yet. Please consult the registrar.</span>
                </div>
            </div>
        @else
            <form method="POST" action="{{ route('student.postEnrollForm') }}" enctype="multipart/form-data">
                @csrf
                <div class="row g-3">
                    @foreach ($sections as $section)
                        <div class="col-md-6">
                            <label class="card h-100 border rounded-3 p-3 card-hover" style="cursor: pointer;" for="section{{ $section->id }}">
                                <div class="card-body p-0">
                                    <div class="form-check d-flex align-items-start gap-2 mb-2">
                                             <input class="form-check-input mt-1" type="radio" name="section_id"
                                               value="{{ $section->id }}" id="section{{ $section->id }}"
                                               {{ old('section_id') == $section->id ? 'checked' : '' }} required>
                                        <div class="form-check-label flex-grow-1">
                                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                                <span class="fw-bold text-dark fs-5">{{ $section->section_name }}</span>
                                                @if ($section->isNearlyFull())
                                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle">
                                                        <i class="bi bi-exclamation-circle"></i> Almost full
                                                    </span>
                                                @endif
                                            </div>
                                            <span class="badge bg-light text-secondary border mt-1">{{ $section->time_period }} Schedule</span>
                                        </div>
                                    </div>

                                    <div class="text-muted small mb-3">
                                        Slots:
                                        <strong class="{{ $section->isNearlyFull() ? 'text-danger' : 'text-success' }}">{{ $section->remainingSlots() }} left</strong>
                                        of {{ $section->max_capacity }} &middot;
                                        Subjects: <strong>{{ $section->subjects->count() }} total</strong>
                                    </div>
                                    
                                    <details class="border-top pt-2">
                                        <summary class="small text-success fw-bold d-inline-flex align-items-center gap-1" style="cursor: pointer;">
                                            <i class="bi bi-eye"></i>
                                            <span>View Subjects</span>
                                        </summary>
                                        <ul class="small text-muted mt-2 mb-0 ps-3">
                                            @foreach ($section->subjects as $subject)
                                                <li class="mb-1">
                                                    <span class="fw-semibold text-dark">{{ $subject->subject_code }}</span> — {{ $subject->subject_name }}
                                                </li>
                                            @endforeach
                                        </ul>
                                    </details>
                                </div>
                            </label>
                        </div>
                    @endforeach
                </div>

                @if ($student->grade_level === '12')
                    <div class="card border-0 shadow-sm rounded-3 mt-4">
                        <div class="card-body">
                            <h6 class="fw-bold text-dark mb-1"><i class="bi bi-paperclip me-1"></i> Enrollment Requirements (Grade 12)</h6>
                            <p class="text-muted small mb-3">PDF, JPG, or PNG — max 5 MB each. Required to submit.</p>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="doc_sf9" class="form-label small fw-semibold">Grade 11 Report Card (SF9) <span class="text-danger">*</span></label>
                                    <input type="file" id="doc_sf9" name="documents[sf9]" accept=".pdf,.jpg,.jpeg,.png"
                                           class="form-control @error('documents.sf9') is-invalid @enderror" required>
                                    @error('documents.sf9') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="doc_photo" class="form-label small fw-semibold">2x2 ID Photo <span class="text-danger">*</span></label>
                                    <input type="file" id="doc_photo" name="documents[photo]" accept=".jpg,.jpeg,.png"
                                           class="form-control @error('documents.photo') is-invalid @enderror" required>
                                    @error('documents.photo') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                    <a href="{{ route('student.showDashboard') }}" class="btn btn-outline-secondary">Cancel</a>
                    <button type="submit" class="btn btn-success px-4 d-inline-flex align-items-center gap-1" data-loading-text="Submitting…">
                        <i class="bi bi-file-earmark-check"></i> Submit Enrollment
                    </button>
                </div>
            </form>
        @endif
    @endif

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const radios = document.querySelectorAll('input[name="section_id"]');
            function updateSelected() {
                document.querySelectorAll('label.card').forEach(l => l.classList.remove('selected'));
                const checked = document.querySelector('input[name="section_id"]:checked');
                if (checked) {
                    const lab = checked.closest('label.card');
                    if (lab) lab.classList.add('selected');
                }
            }
            radios.forEach(r => r.addEventListener('change', updateSelected));
            // initialize on page load
            updateSelected();
        });
    </script>

@endsection
