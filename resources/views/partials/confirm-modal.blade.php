<!-- Global Custom Confirmation Modal -->
<div class="modal fade" id="globalConfirmModal" tabindex="-1" aria-labelledby="globalConfirmModalLabel" aria-hidden="true" style="z-index: 1065;">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 420px; margin: 1.75rem auto;">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden" style="background: #ffffff; border-radius: 1rem;">
            <div class="modal-body p-4 text-center">
                <!-- Close Button -->
                <button type="button" class="btn-close position-absolute top-0 end-0 m-3 fs-7" id="confirmModalCloseBtn" aria-label="Close"></button>

                <!-- Dynamic Icon Container -->
                <div class="mb-3 d-inline-flex align-items-center justify-content-center rounded-circle p-3 shadow-sm mx-auto" id="confirmModalIconBox" style="width: 68px; height: 68px;">
                    <div id="confirmModalIconSvg">
                        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/><line x1="10" x2="10" y1="11" y2="17"/><line x1="14" x2="14" y1="11" y2="17"/></svg>
                    </div>
                </div>

                <h5 class="fw-bold text-dark mb-2 tracking-tight" id="confirmModalTitle" style="font-size: 1.15rem;">Move to Trash?</h5>
                <p class="text-muted fs-7 mb-4 px-2 lh-base" id="confirmModalMessage" style="font-size: 0.875rem;">Are you sure you want to move this item to trash?</p>

                <div class="d-flex gap-2 justify-content-center">
                    <button type="button" class="btn btn-light border rounded-3 px-4 py-2 fw-medium text-secondary fs-7" id="confirmModalCancelBtn">
                        Cancel
                    </button>
                    <button type="button" class="btn btn-danger rounded-3 px-4 py-2 fw-semibold shadow-sm d-inline-flex align-items-center gap-2 fs-7" id="confirmModalActionBtn">
                        <span id="confirmModalActionText">Yes, Move to Trash</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
(function() {
    var modalEl = document.getElementById('globalConfirmModal');
    var nativeConfirm = window.confirm;
    var currentResolve = null;

    var SVG_ICONS = {
        danger: '<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/><line x1="10" x2="10" y1="11" y2="17"/><line x1="14" x2="14" y1="11" y2="17"/></svg>',
        warning: '<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/><line x1="12" x2="12" y1="9" y2="13"/><line x1="12" x2="12.01" y1="17" y2="17"/></svg>',
        restore: '<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/></svg>'
    };

    function showModal() {
        if (!modalEl) return;
        var backdrop = document.getElementById('globalConfirmBackdrop');
        if (!backdrop) {
            backdrop = document.createElement('div');
            backdrop.id = 'globalConfirmBackdrop';
            backdrop.className = 'modal-backdrop fade show';
            backdrop.style.zIndex = '1060';
            document.body.appendChild(backdrop);
        }
        modalEl.style.display = 'block';
        modalEl.offsetHeight; // force reflow
        modalEl.classList.add('show');
        document.body.classList.add('modal-open');
        document.body.style.overflow = 'hidden';
    }

    function hideModal() {
        if (!modalEl) return;
        modalEl.classList.remove('show');
        setTimeout(function() {
            modalEl.style.display = 'none';
            var backdrop = document.getElementById('globalConfirmBackdrop');
            if (backdrop && backdrop.parentNode) {
                backdrop.parentNode.removeChild(backdrop);
            }
            document.body.classList.remove('modal-open');
            document.body.style.overflow = '';
        }, 150);
    }

    window.openConfirmModal = function (options) {
        options = options || {};
        return new Promise(function(resolve) {
            currentResolve = resolve;

            var titleEl = document.getElementById('confirmModalTitle');
            var msgEl = document.getElementById('confirmModalMessage');
            var iconBox = document.getElementById('confirmModalIconBox');
            var iconSvg = document.getElementById('confirmModalIconSvg');
            var actionBtn = document.getElementById('confirmModalActionBtn');
            var actionText = document.getElementById('confirmModalActionText');
            var cancelBtn = document.getElementById('confirmModalCancelBtn');

            var message = options.message || 'Are you sure you want to proceed?';
            var type = options.type;

            if (!type) {
                var msgLower = message.toLowerCase();
                if (msgLower.includes('restore')) {
                    type = 'restore';
                } else if (msgLower.includes('warning')) {
                    type = 'warning';
                } else {
                    type = 'danger';
                }
            }

            var defaultTitle = 'Confirm Action';
            var defaultBtn = 'Yes, Proceed';

            if (type === 'danger') {
                if (message.toLowerCase().includes('trash')) {
                    defaultTitle = 'Move to Trash?';
                    defaultBtn = 'Yes, Move to Trash';
                } else if (message.toLowerCase().includes('permanent')) {
                    defaultTitle = 'Permanently Delete?';
                    defaultBtn = 'Yes, Delete Permanently';
                } else {
                    defaultTitle = 'Delete Item?';
                    defaultBtn = 'Yes, Delete';
                }

                if (iconBox) iconBox.className = 'mb-3 d-inline-flex align-items-center justify-content-center rounded-circle p-3 bg-danger bg-opacity-10 text-danger shadow-sm mx-auto';
                if (actionBtn) actionBtn.className = 'btn btn-danger rounded-3 px-4 py-2 fw-semibold shadow-sm d-inline-flex align-items-center gap-2 fs-7';
                if (iconSvg) iconSvg.innerHTML = SVG_ICONS.danger;
            } else if (type === 'restore') {
                defaultTitle = 'Restore Item?';
                defaultBtn = 'Yes, Restore';

                if (iconBox) iconBox.className = 'mb-3 d-inline-flex align-items-center justify-content-center rounded-circle p-3 bg-success bg-opacity-10 text-success shadow-sm mx-auto';
                if (actionBtn) actionBtn.className = 'btn btn-success rounded-3 px-4 py-2 fw-semibold shadow-sm d-inline-flex align-items-center gap-2 fs-7';
                if (iconSvg) iconSvg.innerHTML = SVG_ICONS.restore;
            } else if (type === 'warning') {
                defaultTitle = 'Warning';
                defaultBtn = 'Continue';

                if (iconBox) iconBox.className = 'mb-3 d-inline-flex align-items-center justify-content-center rounded-circle p-3 bg-warning bg-opacity-10 text-warning shadow-sm mx-auto';
                if (actionBtn) actionBtn.className = 'btn btn-warning text-dark rounded-3 px-4 py-2 fw-semibold shadow-sm d-inline-flex align-items-center gap-2 fs-7';
                if (iconSvg) iconSvg.innerHTML = SVG_ICONS.warning;
            }

            if (titleEl) titleEl.textContent = options.title || defaultTitle;
            if (msgEl) msgEl.textContent = message;
            if (actionText) actionText.textContent = options.btnText || defaultBtn;
            if (cancelBtn) cancelBtn.textContent = options.cancelText || 'Cancel';

            showModal();
        });
    };

    function finishModal(result) {
        hideModal();
        if (currentResolve) {
            var res = currentResolve;
            currentResolve = null;
            res(result);
        }
    }

    var actionBtn = document.getElementById('confirmModalActionBtn');
    var cancelBtn = document.getElementById('confirmModalCancelBtn');
    var closeBtn  = document.getElementById('confirmModalCloseBtn');

    if (actionBtn) actionBtn.onclick = function() { finishModal(true); };
    if (cancelBtn) cancelBtn.onclick = function() { finishModal(false); };
    if (closeBtn)  closeBtn.onclick  = function() { finishModal(false); };

    // Global override of window.confirm to block browser native popup everywhere
    window.confirm = function (message) {
        var activeEl = document.activeElement;
        var activeForm = activeEl ? activeEl.closest('form') : null;

        if (activeForm && activeForm._customModalConfirmed) {
            return true;
        }

        window.openConfirmModal({
            message: message || 'Are you sure you want to proceed?'
        }).then(function (confirmed) {
            if (confirmed) {
                if (activeForm) {
                    activeForm._customModalConfirmed = true;
                    activeForm.submit();
                } else if (activeEl && activeEl.tagName === 'A' && activeEl.href) {
                    window.location.href = activeEl.href;
                }
            }
        });

        return false;
    };

    // Handle forms with data-confirm
    document.addEventListener('submit', function (e) {
        var form = e.target;
        if (!form || form._customModalConfirmed) return;

        var confirmMsg = form.getAttribute('data-confirm');
        if (confirmMsg) {
            e.preventDefault();
            e.stopPropagation();

            var title = form.getAttribute('data-confirm-title');
            var btnText = form.getAttribute('data-confirm-btn');
            var type = form.getAttribute('data-confirm-type');

            window.openConfirmModal({
                title: title,
                message: confirmMsg,
                btnText: btnText,
                type: type
            }).then(function (confirmed) {
                if (confirmed) {
                    form._customModalConfirmed = true;
                    form.submit();
                }
            });
        }
    }, true);

    // Handle clicks on buttons/links with data-confirm
    document.addEventListener('click', function (e) {
        var btn = e.target.closest('[data-confirm]');
        if (!btn) return;

        var form = btn.closest('form');
        if (form && form._customModalConfirmed) return;

        e.preventDefault();
        e.stopPropagation();

        if (form) {
            var checkboxes = form.querySelectorAll('input[name="selected_ids[]"]:checked');
            if (form.querySelector('input[name="selected_ids[]"]') && checkboxes.length === 0) {
                window.openConfirmModal({
                    title: 'No Items Selected',
                    message: 'Please select at least one record before performing this action.',
                    type: 'warning',
                    btnText: 'OK'
                });
                return;
            }
        }

        var confirmMsg = btn.getAttribute('data-confirm');
        var title = btn.getAttribute('data-confirm-title');
        var btnText = btn.getAttribute('data-confirm-btn');
        var type = btn.getAttribute('data-confirm-type');

        window.openConfirmModal({
            title: title,
            message: confirmMsg,
            btnText: btnText,
            type: type
        }).then(function (confirmed) {
            if (confirmed) {
                if (form) {
                    form._customModalConfirmed = true;
                    form.submit();
                } else if (btn.tagName === 'A' && btn.href) {
                    window.location.href = btn.href;
                }
            }
        });
    }, true);
})();
</script>
