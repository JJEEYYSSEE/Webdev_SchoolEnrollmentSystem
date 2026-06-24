{{--
    Reusable themed confirm dialog — replaces native confirm().
    Trigger by adding to a <form> OR a submit button:
        data-confirm="Your message"
        data-confirm-title="..."   (optional, default "Please Confirm")
        data-confirm-ok="..."      (optional, default "Confirm")
        data-confirm-danger        (optional, red OK button for destructive actions)
    Colors follow the active portal theme (navy on registrar, green on student).
    Must be included BEFORE partials.submit-loading so it intercepts first.
--}}
<div id="appConfirm" class="app-confirm" aria-hidden="true">
    <div class="app-confirm__backdrop" data-confirm-dismiss></div>
    <div class="app-confirm__dialog" role="dialog" aria-modal="true" aria-labelledby="appConfirmTitle">
        <div class="app-confirm__header">
            <i class="bi bi-question-circle-fill"></i>
            <span id="appConfirmTitle">Please Confirm</span>
        </div>
        <div class="app-confirm__body" id="appConfirmMessage"></div>
        <div class="app-confirm__footer">
            <button type="button" class="btn btn-light px-3" data-confirm-dismiss>Cancel</button>
            <button type="button" class="btn app-confirm__ok px-3" id="appConfirmOk">Confirm</button>
        </div>
    </div>
</div>

<script>
(function () {
    var modal = document.getElementById('appConfirm');
    if (!modal || window.__appConfirmInit) return;
    window.__appConfirmInit = true;

    var msgEl   = document.getElementById('appConfirmMessage');
    var titleEl = document.getElementById('appConfirmTitle');
    var okBtn   = document.getElementById('appConfirmOk');
    var pending = null;

    function open(opts) {
        msgEl.textContent   = opts.message || 'Are you sure?';
        titleEl.textContent = opts.title || 'Please Confirm';
        okBtn.textContent   = opts.confirmText || 'Confirm';
        okBtn.classList.toggle('is-danger', !!opts.danger);
        pending = opts.onOk || null;
        modal.classList.add('is-open');
        modal.setAttribute('aria-hidden', 'false');
        okBtn.focus();
    }
    function close() {
        modal.classList.remove('is-open');
        modal.setAttribute('aria-hidden', 'true');
        pending = null;
    }

    okBtn.addEventListener('click', function () {
        var cb = pending; close(); if (cb) cb();
    });
    modal.querySelectorAll('[data-confirm-dismiss]').forEach(function (el) {
        el.addEventListener('click', close);
    });
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && modal.classList.contains('is-open')) close();
    });

    // Intercept submits from forms/buttons carrying data-confirm.
    document.addEventListener('submit', function (e) {
        var form = e.target;
        if (!(form instanceof HTMLFormElement)) return;
        if (form.dataset.appConfirmed === '1') { form.dataset.appConfirmed = ''; return; }

        var submitter = e.submitter;
        var src = (submitter && submitter.hasAttribute('data-confirm')) ? submitter
                : (form.hasAttribute('data-confirm') ? form : null);
        if (! src) return;

        e.preventDefault();
        e.stopImmediatePropagation(); // stop the submit-loading guard on this cancelled pass

        open({
            message: src.getAttribute('data-confirm'),
            title: src.getAttribute('data-confirm-title'),
            confirmText: src.getAttribute('data-confirm-ok'),
            danger: src.hasAttribute('data-confirm-danger'),
            onOk: function () {
                form.dataset.appConfirmed = '1';
                if (typeof form.requestSubmit === 'function') form.requestSubmit(submitter || undefined);
                else form.submit();
            },
        });
    }, true);
})();
</script>
