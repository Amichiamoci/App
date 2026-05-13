import { Controller } from '@hotwired/stimulus';
import { Modal } from 'bootstrap';

export default class extends Controller {
    static targets = ['offlineModal', 
                      'onlineModal', 
                      'resumeMessage', 
                      'gamePanel'];

    connect() {
        this.offlineModal = new Modal(this.offlineModalTarget);
        this.onlineModal = new Modal(this.onlineModalTarget);
        this.showingOfflinePrompt = false;
        this.showingOnlinePrompt = false;
        this.lastDesiredUrl = null;
        this.homeUrl = `${window.location.origin}/`;

        this.onlineListener = this.showOnlinePrompt.bind(this);
        this.offlineListener = this.showOfflinePrompt.bind(this);
        this.captureListener = this.captureDesiredUrl.bind(this);

        window.addEventListener('online', this.onlineListener);
        window.addEventListener('offline', this.offlineListener);
        document.addEventListener('click', this.captureListener);

        this.preloadAssets();

        if (!navigator.onLine) {
            this.showOfflinePrompt();
        }
    }

    disconnect() {
        window.removeEventListener('online', this.onlineListener);
        window.removeEventListener('offline', this.offlineListener);
        document.removeEventListener('click', this.captureListener);
    }

    preloadAssets() {
        this.imagePathsValue?.forEach((src) => {
            const image = new Image();
            image.src = src;
        });
    }

    captureDesiredUrl(event) {
        const link = event.target.closest('a[href]');
        if (!link) 
            return;

        const href = link.getAttribute('href');
        if (!href || href.startsWith('#') || href.startsWith('mailto:') || href.startsWith('tel:')) {
            return;
        }

        if (link.target && link.target !== '_self')
            return;

        try {
            const url = new URL(link.href, window.location.href);
            if (url.origin !== window.location.origin) 
                return;

            this.lastDesiredUrl = url.href;
        } catch (error) {
            // ignore invalid URLs
        }
    }

    showOfflinePrompt() {
        if (this.showingOfflinePrompt)
            return;

        this.showingOfflinePrompt = true;
        if (this.onlineModal && this.onlineModal._element?.classList.contains('show')) {
            this.onlineModal.hide();
        }

        this.offlineModal.show();
    }

    showOnlinePrompt() {
        this.showingOfflinePrompt = false;

        if (this.offlineModal && this.offlineModal._element?.classList.contains('show')) {
            this.offlineModal.hide();
        }

        if (this.showingOnlinePrompt) 
            return;

        this.showingOnlinePrompt = true;

        if (this.lastDesiredUrl) {
            this.resumeMessageTarget.textContent = 'Connessione ripristinata. Vuoi continuare a giocare o andare alla pagina desiderata?';
        } else {
            this.resumeMessageTarget.textContent = 'Connessione ripristinata. Vuoi continuare a giocare o tornare alla home?';
        }

        this.onlineModal.show();
        this.onlineModal._element.addEventListener('hidden.bs.modal', () => {
            this.showingOnlinePrompt = false;
        }, { once: true });
    }

    startGame() {
        this.offlineModal.hide();
        this.gamePanelTarget.classList.remove('d-none');
        document.body.classList.add('offline-game-open');
        
        // Trigger game start after a small delay to ensure DOM is ready
        requestAnimationFrame(() => {
            document.dispatchEvent(new CustomEvent('offline-game:start'));
        });
    }

    closeGame() {
        this.gamePanelTarget.classList.add('d-none');
        document.body.classList.remove('offline-game-open');
    }

    skipOffline() {
        this.offlineModal.hide();
    }

    continuePlaying() {
        this.onlineModal.hide();
    }

    goHome() {
        this.onlineModal.hide();
        window.location.href = this.lastDesiredUrl || this.homeUrl;
    }


}
