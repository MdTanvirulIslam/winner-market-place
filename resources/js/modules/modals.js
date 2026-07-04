const AppModals = {
  state: {
    openModal: null,
    closeTimer: null,
    lastTrigger: null,
  },

  clearCloseTimer() {
    if (this.state.closeTimer) {
      window.clearTimeout(this.state.closeTimer);
      this.state.closeTimer = null;
    }
  },

  lockScroll() {
    document.body.style.overflow = 'hidden';
  },

  unlockScroll() {
    document.body.style.overflow = '';
  },

  open(name, trigger = null) {
    const overlay = document.getElementById('modalOverlay');
    const modal = document.getElementById(`modal-${name}`);

    if (!overlay || !modal) {
      return;
    }

    this.clearCloseTimer();
    document.querySelectorAll('.modal-panel.show').forEach((panel) => {
      panel.classList.remove('show');
    });

    overlay.classList.remove('show');
    this.state.openModal = name;
    this.state.lastTrigger = trigger ?? document.activeElement;
    this.lockScroll();

    window.requestAnimationFrame(() => {
      overlay.classList.add('show');
      modal.classList.add('show');
      modal.setAttribute('tabindex', '-1');
      modal.focus({ preventScroll: true });
    });
  },

  close() {
    const hasVisibleModal = document.querySelector('.modal-panel.show') || document.getElementById('modalOverlay')?.classList.contains('show');

    if (!hasVisibleModal) {
      this.state.openModal = null;
      this.state.lastTrigger = null;
      this.unlockScroll();
      return;
    }

    document.querySelectorAll('.modal-panel.show').forEach((panel) => {
      panel.classList.remove('show');
    });

    document.getElementById('modalOverlay')?.classList.remove('show');
    const restoreTarget = this.state.lastTrigger;
    this.state.openModal = null;
    this.state.lastTrigger = null;
    this.clearCloseTimer();
    this.state.closeTimer = window.setTimeout(() => {
      this.unlockScroll();

      if (restoreTarget instanceof HTMLElement && document.contains(restoreTarget)) {
        restoreTarget.focus({ preventScroll: true });
      }

      this.state.closeTimer = null;
    }, 360);
  },

  init() {
    document.addEventListener('keydown', (event) => {
      if (event.key === 'Escape' && this.state.openModal) {
        this.close();
      }
    });
  },
};

export default AppModals;
