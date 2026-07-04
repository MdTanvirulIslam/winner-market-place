import { setIcon } from './icons.js';

const AppFullscreen = {
  toggle() {
    if (!document.fullscreenElement) {
      document.documentElement.requestFullscreen().catch(() => {});
      return;
    }

    document.exitFullscreen().catch(() => {});
  },

  updateIcon() {
    setIcon('#fullscreenToggleIcon', document.fullscreenElement ? 'minimize' : 'maximize');
  },

  init() {
    document.addEventListener('fullscreenchange', this.updateIcon);
    this.updateIcon();
  },
};

export default AppFullscreen;
