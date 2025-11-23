export class ModalManager {

    openModal(modalId = 'info', title = '', subtitle = '', callback = null) {
        const modal = document.getElementById(`bum-modal-${modalId}`);
        if (!modal) return;
        modal.classList.add('show');

        if (title) {
            const titleElement = document.getElementById(`title-${modalId}`);
            if (titleElement) titleElement.textContent = title;
        }

        if (subtitle) {
            const subtitleElement = document.getElementById(`subtitle-${modalId}`);
            if (subtitleElement) subtitleElement.textContent = subtitle;
        }

        // Configurar callback del botón OK
        document.getElementById(`btn-ok-${modalId}`)
            .onclick = callback || (() => this.closeModal(`bum-modal-${modalId}`));


        const btnClose = document.getElementById(`bum-closeModalBtn-${modalId}`);

        switch (modalId) {
            case ModalManager.ModalType.SUCCESS:
                btnClose.onclick = callback || (() => this.closeModal(`bum-modal-${modalId}`));
                break;
            case ModalManager.ModalType.ERROR:
                btnClose.onclick = () => this.closeModal(`bum-modal-${modalId}`);
                break;

            case ModalManager.ModalType.WARNING:
            case ModalManager.ModalType.INFO:
                const btnCancel = document.getElementById(`btn-cancel-${modalId}`);
                if (btnCancel) {
                    btnCancel.onclick = () => this.closeModal(`bum-modal-${modalId}`);
                }
                btnClose.onclick = () => this.closeModal(`bum-modal-${modalId}`);
                break;
        }

    }

    closeModal(modalId) {
        if (!modalId) return;
        if (!modalId.startsWith('bum-modal-')) {
            modalId = `bum-modal-${modalId}`;
        }
        const modal = document.getElementById(modalId);
        if (modal) modal.classList.remove('show');
    }

    openInfo(title, subtitle, callback) {
        this.openModal(ModalManager.ModalType.INFO, title, subtitle, callback);
    }

    openError(title, subtitle, callback) {
        this.openModal(ModalManager.ModalType.ERROR, title, subtitle, callback);
    }

    openSuccess(title, subtitle, callback) {
        this.openModal(ModalManager.ModalType.SUCCESS, title, subtitle, callback);
    }

    openWarning(title, subtitle, callback) {
        this.openModal(ModalManager.ModalType.WARNING, title, subtitle, callback);
    }

    closeModalPop() {
        const openModal = document.querySelector('.bum-modal.show');
        if (openModal) {
            this.closeModal(openModal.id);
        }
    }

// Modal Types
    static
    get ModalType() {
        return {
            INFO: 'info',
            ERROR: 'error',
            SUCCESS: 'success',
            WARNING: 'warning'
        };
    }
}