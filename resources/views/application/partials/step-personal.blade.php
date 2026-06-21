@php $a = $application; @endphp

<form method="POST" action="{{ route('application.save') }}">
    @csrf
    <input type="hidden" name="step" value="1">

    <h6 class="fw-bold text-primary mb-3"><i class="bi bi-person-fill me-1"></i> Personal Information</h6>

    <div class="row g-3 mb-3">
        <div class="col-md-8">
            <label class="form-label small fw-semibold">LRN <span class="text-muted fw-normal">(12 digits, optional)</span></label>
            <input type="text" name="lrn" maxlength="12" value="{{ old('lrn', $a->lrn) }}"
                   class="form-control @error('lrn') is-invalid @enderror" placeholder="Learner Reference Number">
            @error('lrn') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <div class="col-md-4">
            <label class="form-label small fw-semibold">Sex <span class="text-danger">*</span></label>
            <select name="sex" class="form-select @error('sex') is-invalid @enderror" required>
                <option value="" disabled {{ old('sex', $a->sex) ? '' : 'selected' }}>—</option>
                <option value="Male"   @selected(old('sex', $a->sex) === 'Male')>Male</option>
                <option value="Female" @selected(old('sex', $a->sex) === 'Female')>Female</option>
            </select>
            @error('sex') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="col-md-4">
            <label class="form-label small fw-semibold">First Name <span class="text-danger">*</span></label>
            <input type="text" name="first_name" value="{{ old('first_name', $a->first_name) }}"
                   class="form-control @error('first_name') is-invalid @enderror" required>
            @error('first_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <div class="col-md-4">
            <label class="form-label small fw-semibold">Middle Name</label>
            <input type="text" name="middle_name" value="{{ old('middle_name', $a->middle_name) }}" class="form-control">
        </div>
        <div class="col-md-3">
            <label class="form-label small fw-semibold">Last Name <span class="text-danger">*</span></label>
            <input type="text" name="last_name" value="{{ old('last_name', $a->last_name) }}"
                   class="form-control @error('last_name') is-invalid @enderror" required>
            @error('last_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <div class="col-md-1">
            <label class="form-label small fw-semibold">Ext.</label>
            <input type="text" name="extension_name" value="{{ old('extension_name', $a->extension_name) }}" class="form-control" placeholder="Jr.">
        </div>

        <div class="col-md-4">
            <label class="form-label small fw-semibold">Birthdate <span class="text-danger">*</span></label>
            <input type="date" name="birthdate" value="{{ old('birthdate', optional($a->birthdate)->format('Y-m-d')) }}"
                   class="form-control @error('birthdate') is-invalid @enderror" required>
            @error('birthdate') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <div class="col-md-4">
            <label class="form-label small fw-semibold">Place of Birth <span class="text-danger">*</span></label>
            <input type="text" name="place_of_birth" value="{{ old('place_of_birth', $a->place_of_birth) }}"
                   class="form-control @error('place_of_birth') is-invalid @enderror" required>
            @error('place_of_birth') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <div class="col-md-4">
            <label class="form-label small fw-semibold">Civil Status</label>
            <input type="text" name="civil_status" value="{{ old('civil_status', $a->civil_status) }}" class="form-control" placeholder="Single">
        </div>

        <div class="col-md-4">
            <label class="form-label small fw-semibold">Mother Tongue <span class="text-danger">*</span></label>
            <input type="text" name="mother_tongue" value="{{ old('mother_tongue', $a->mother_tongue) }}"
                   class="form-control @error('mother_tongue') is-invalid @enderror" required>
            @error('mother_tongue') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <div class="col-md-4">
            <label class="form-label small fw-semibold">Religion</label>
            <input type="text" name="religion" value="{{ old('religion', $a->religion) }}" class="form-control">
        </div>
        <div class="col-md-4">
            <label class="form-label small fw-semibold">Mobile No. <span class="text-danger">*</span></label>
            <input type="text" name="mobile" value="{{ old('mobile', $a->mobile) }}"
                   class="form-control @error('mobile') is-invalid @enderror" required>
            @error('mobile') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
    </div>

    <div class="row g-3 mb-3">
        <div class="col-md-4">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="is_ip" value="1" id="is_ip" @checked(old('is_ip', $a->is_ip))>
                <label class="form-check-label small" for="is_ip">Belongs to IP community</label>
            </div>
            <input type="text" name="ip_community" value="{{ old('ip_community', $a->ip_community) }}" class="form-control form-control-sm mt-1" placeholder="Specify community">
        </div>
        <div class="col-md-4">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="has_disability" value="1" id="has_disability" @checked(old('has_disability', $a->has_disability))>
                <label class="form-check-label small" for="has_disability">Has disability / special needs</label>
            </div>
            <input type="text" name="disability_type" value="{{ old('disability_type', $a->disability_type) }}" class="form-control form-control-sm mt-1" placeholder="Specify type">
        </div>
        <div class="col-md-4">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="is_4ps" value="1" id="is_4ps" @checked(old('is_4ps', $a->is_4ps))>
                <label class="form-check-label small" for="is_4ps">4Ps beneficiary</label>
            </div>
            <input type="text" name="household_id" value="{{ old('household_id', $a->household_id) }}" class="form-control form-control-sm mt-1" placeholder="Household ID No.">
        </div>
    </div>

    <h6 class="fw-bold text-primary mb-3 mt-4 pt-2 border-top"><i class="bi bi-geo-alt-fill me-1"></i> Current Address</h6>
    <div class="row g-3 mb-3">
        <div class="col-12">
            <label class="form-label small fw-semibold">House No. / Street <span class="text-danger">*</span></label>
            <input type="text" name="current_address" value="{{ old('current_address', $a->current_address) }}"
                   class="form-control @error('current_address') is-invalid @enderror" required>
            @error('current_address') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <div class="col-md-3">
            <label class="form-label small fw-semibold">Barangay <span class="text-danger">*</span></label>
            <input type="text" name="current_barangay" value="{{ old('current_barangay', $a->current_barangay) }}"
                   class="form-control @error('current_barangay') is-invalid @enderror" required>
            @error('current_barangay') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <div class="col-md-3">
            <label class="form-label small fw-semibold">City / Municipality <span class="text-danger">*</span></label>
            <input type="text" name="current_city" value="{{ old('current_city', $a->current_city) }}"
                   class="form-control @error('current_city') is-invalid @enderror" required>
            @error('current_city') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <div class="col-md-4">
            <label class="form-label small fw-semibold">Province <span class="text-danger">*</span></label>
            <input type="text" name="current_province" value="{{ old('current_province', $a->current_province) }}"
                   class="form-control @error('current_province') is-invalid @enderror" required>
            @error('current_province') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <div class="col-md-2">
            <label class="form-label small fw-semibold">Zip</label>
            <input type="text" name="current_zip" value="{{ old('current_zip', $a->current_zip) }}" class="form-control">
        </div>
        <div class="col-12">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="permanent_same" value="1" id="permanent_same" @checked(old('permanent_same', $a->permanent_same))>
                <label class="form-check-label small" for="permanent_same">Permanent address is the same as current</label>
            </div>
        </div>
        <div class="col-md-6">
            <label class="form-label small fw-semibold">Permanent Address <span class="text-muted fw-normal">(if different)</span></label>
            <input type="text" name="permanent_address" value="{{ old('permanent_address', $a->permanent_address) }}" class="form-control">
        </div>
        <div class="col-md-2">
            <label class="form-label small fw-semibold">Barangay</label>
            <input type="text" name="permanent_barangay" value="{{ old('permanent_barangay', $a->permanent_barangay) }}" class="form-control">
        </div>
        <div class="col-md-2">
            <label class="form-label small fw-semibold">City</label>
            <input type="text" name="permanent_city" value="{{ old('permanent_city', $a->permanent_city) }}" class="form-control">
        </div>
        <div class="col-md-2">
            <label class="form-label small fw-semibold">Province</label>
            <input type="text" name="permanent_province" value="{{ old('permanent_province', $a->permanent_province) }}" class="form-control">
        </div>
    </div>

    <h6 class="fw-bold text-primary mb-3 mt-4 pt-2 border-top"><i class="bi bi-people-fill me-1"></i> Parents / Guardian</h6>
    <div class="row g-3 mb-3">
        <div class="col-md-8">
            <label class="form-label small fw-semibold">Father's Full Name</label>
            <input type="text" name="father_name" value="{{ old('father_name', $a->father_name) }}" class="form-control">
        </div>
        <div class="col-md-4">
            <label class="form-label small fw-semibold">Father's Contact</label>
            <input type="text" name="father_contact" value="{{ old('father_contact', $a->father_contact) }}" class="form-control">
        </div>
        <div class="col-md-8">
            <label class="form-label small fw-semibold">Mother's Maiden Name</label>
            <input type="text" name="mother_name" value="{{ old('mother_name', $a->mother_name) }}" class="form-control">
        </div>
        <div class="col-md-4">
            <label class="form-label small fw-semibold">Mother's Contact</label>
            <input type="text" name="mother_contact" value="{{ old('mother_contact', $a->mother_contact) }}" class="form-control">
        </div>
        <div class="col-md-5">
            <label class="form-label small fw-semibold">Guardian's Name</label>
            <input type="text" name="guardian_name" value="{{ old('guardian_name', $a->guardian_name) }}" class="form-control">
        </div>
        <div class="col-md-3">
            <label class="form-label small fw-semibold">Relationship</label>
            <input type="text" name="guardian_relationship" value="{{ old('guardian_relationship', $a->guardian_relationship) }}" class="form-control">
        </div>
        <div class="col-md-4">
            <label class="form-label small fw-semibold">Guardian's Contact</label>
            <input type="text" name="guardian_contact" value="{{ old('guardian_contact', $a->guardian_contact) }}" class="form-control">
        </div>
    </div>

    <div class="d-flex justify-content-end pt-3 border-top">
        <button type="submit" class="btn btn-primary" data-loading-text="Saving…">
            Next <i class="bi bi-arrow-right ms-1"></i>
        </button>
    </div>
</form>
