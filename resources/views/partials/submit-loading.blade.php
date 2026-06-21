{{-- Global double-submit guard: disables the submit button and shows a spinner
     once a form is validly submitted, preventing duplicate clicks (duplicate
     registrations, double-sent OTPs, etc.). Opt out per form with data-no-loading. --}}
<script>
    (function () {
        document.addEventListener('submit', function (e) {
            var form = e.target;
            if (!(form instanceof HTMLFormElement)) return;
            if (form.hasAttribute('data-no-loading')) return;

            // Native HTML5 validation blocks the submit event when invalid,
            // so reaching here means the form is valid and really submitting.
            var btn = form.querySelector('[type="submit"]');
            if (!btn || btn.dataset.loading === '1') return;

            btn.dataset.loading = '1';
            btn.disabled = true;

            var spinner = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>';
            btn.innerHTML = spinner + (btn.getAttribute('data-loading-text') || 'Please wait…');
        }, true);
    })();
</script>
