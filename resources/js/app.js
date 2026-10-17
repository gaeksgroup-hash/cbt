import './bootstrap';

document.querySelectorAll('[data-dialog-open]').forEach((button) => {
    button.addEventListener('click', () => {
        const dialog = document.getElementById(button.dataset.dialogOpen);
        if (dialog instanceof HTMLDialogElement) {
            dialog.showModal();
        }
    });
});

document.querySelectorAll('[data-dialog-close]').forEach((button) => {
    button.addEventListener('click', () => {
        const dialog = button.closest('dialog');
        if (dialog instanceof HTMLDialogElement) {
            dialog.close();
        }
    });
});

document.querySelectorAll('dialog[data-auto-open]').forEach((dialog) => {
    if (dialog instanceof HTMLDialogElement) {
        dialog.showModal();
    }
});
