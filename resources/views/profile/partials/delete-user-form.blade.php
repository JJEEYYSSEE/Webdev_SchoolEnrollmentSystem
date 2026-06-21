<section>
    <div class="d-flex align-items-center gap-2 mb-1">
        <div class="rounded-circle bg-danger bg-opacity-10 p-2 d-flex align-items-center justify-content-center text-danger" style="width: 36px; height: 36px;">
            <i class="bi bi-trash-fill fs-5"></i>
        </div>
        <h5 class="fw-bold mb-0 text-danger">Delete Account</h5>
    </div>
    <p class="text-muted small mb-4 ms-5">
        Once your account is deleted, all of its resources and data will be permanently deleted.
        Before deleting your account, please download any data or information that you wish to retain.
    </p>

    <div class="ms-md-5">
        <button type="button" class="btn btn-danger d-inline-flex align-items-center gap-1" data-bs-toggle="modal" data-bs-target="#deleteAccountModal">
            <i class="bi bi-exclamation-triangle-fill"></i> Delete Account
        </button>
    </div>

    <div class="modal fade" id="deleteAccountModal" tabindex="-1" aria-labelledby="deleteAccountModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content border-0 shadow-lg">
                <form method="post" action="{{ route('profile.destroy') }}">
                    @csrf
                    @method('delete')

                    <div class="modal-header bg-danger text-white border-0 py-3">
                        <h5 class="modal-title fw-bold d-flex align-items-center gap-2" id="deleteAccountModalLabel">
                            <i class="bi bi-exclamation-octagon-fill"></i> Delete Account
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body p-4 text-start">
                        <div class="alert alert-danger border-0 d-flex gap-2">
                            <i class="bi bi-exclamation-triangle-fill fs-5 text-danger"></i>
                            <div class="small">
                                <strong>Critical Action:</strong> Are you sure you want to delete your account? This action is permanent and cannot be undone.
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="delete_password" class="form-label fw-semibold small text-muted">Confirm Your Password</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                                <input id="delete_password" name="password" type="password"
                                       class="form-control @if($errors->userDeletion->has('password')) is-invalid @endif"
                                       placeholder="Enter password to confirm deletion" required>
                                @if ($errors->userDeletion->has('password'))
                                    <div class="invalid-feedback">{{ $errors->userDeletion->first('password') }}</div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer border-top-0 pt-0">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger d-inline-flex align-items-center gap-1">
                            <i class="bi bi-trash-fill"></i> Permanently Delete
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
