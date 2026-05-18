<?php

declare(strict_types=1);
?>
<style>
    #confirm-overlay {
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.45);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 9999;
    }

    #confirm-overlay.is-open {
        display: flex;
    }

    #confirm-dialog {
        background: #fff;
        border: 1px solid #ccc;
        padding: 16px;
        width: min(92vw, 420px);
        border-radius: 10px;
    }

    #confirm-dialog h3 {
        margin: 0 0 8px 0;
        color: #111;
    }

    #confirm-dialog p {
        margin: 0 0 14px 0;
        color: #2a2a2a;
    }

    #confirm-actions {
        display: flex;
        gap: 8px;
        justify-content: flex-end;
    }

    #confirm-cancel {
        background: #f3f3f3;
        border: 1px solid #ccc;
        color: #333;
        border-radius: 6px;
        padding: 6px 12px;
        cursor: pointer;
    }

    #confirm-ok {
        background: #fff;
        border: 1px solid #ccc;
        color: #111;
        border-radius: 6px;
        padding: 6px 12px;
        cursor: pointer;
    }
</style>

<div id="confirm-overlay" role="dialog" aria-modal="true" aria-labelledby="confirm-title">
    <div id="confirm-dialog">
        <h3 id="confirm-title">Please confirm</h3>
        <p id="confirm-message">Are you sure?</p>
        <div id="confirm-actions">
            <button type="button" id="confirm-cancel">Cancel</button>
            <button type="button" id="confirm-ok">Confirm</button>
        </div>
    </div>
</div>

<script>
    (function () {
        var overlay = document.getElementById('confirm-overlay');
        var message = document.getElementById('confirm-message');
        var cancelBtn = document.getElementById('confirm-cancel');
        var okBtn = document.getElementById('confirm-ok');
        var activeForm = null;

        if (!overlay || !message || !cancelBtn || !okBtn) {
            return;
        }

        function openModal(form) {
            activeForm = form;
            message.textContent = form.getAttribute('data-confirm') || 'Are you sure?';
            overlay.classList.add('is-open');
        }

        function closeModal() {
            overlay.classList.remove('is-open');
            activeForm = null;
        }

        document.addEventListener('submit', function (event) {
            var form = event.target;
            if (!(form instanceof HTMLFormElement)) {
                return;
            }

            if (!form.hasAttribute('data-confirm') || form.dataset.confirmed === '1') {
                return;
            }

            event.preventDefault();
            openModal(form);
        });

        cancelBtn.addEventListener('click', function () {
            closeModal();
        });

        okBtn.addEventListener('click', function () {
            if (!activeForm) {
                return;
            }

            activeForm.dataset.confirmed = '1';
            activeForm.submit();
        });

        overlay.addEventListener('click', function (event) {
            if (event.target === overlay) {
                closeModal();
            }
        });
    }());
</script>
