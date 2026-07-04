import './bootstrap';

import Alpine from 'alpinejs';

import AppTheme from './modules/theme.js';
import AppUI from './modules/ui.js';
import AppFullscreen from './modules/fullscreen.js';
import AppModals from './modules/modals.js';
import AppToasts from './modules/toasts.js';
import { renderIcons } from './modules/icons.js';

window.Alpine = Alpine;
Alpine.start();

// Session flash messages are serialized into window.__flash by the Blade
// layouts and rendered as toasts.
const showFlashToasts = () => {
    const flash = window.__flash || {};
    const titles = {
        success: 'Success',
        error: 'Error',
        warning: 'Warning',
        info: 'Notice',
    };

    Object.entries(titles).forEach(([variant, title]) => {
        if (flash[variant]) {
            AppToasts.show({
                title,
                message: flash[variant],
                variant: variant === 'error' ? 'danger' : variant,
            });
        }
    });
};

const initApp = () => {
    renderIcons();
    AppTheme.init();
    AppUI.syncSidebarDropdownHeights();
    AppUI.initResize();
    AppFullscreen.init();
    AppModals.init();
    AppToasts.init();
    showFlashToasts();

    const sidebar = document.getElementById('sidebar');
    const mainWrap = document.getElementById('mainWrap');

    if (window.innerWidth >= 992 && sidebar && mainWrap) {
        sidebar.style.transform = 'translateX(0)';
        mainWrap.style.marginLeft = 'var(--sidebar-width)';
    }

    document.querySelectorAll('.animate-in').forEach((element, index) => {
        element.style.animationDelay = `${0.05 + index * 0.06}s`;
    });

    document.addEventListener('click', (event) => {
        const target = event.target;

        if (!(target instanceof Element)) {
            return;
        }

        if (target.closest('#darkModeToggle') || target.closest('#offcanvasDarkMode')) {
            AppTheme.applyDarkMode(localStorage.getItem('winnerTAM-dark') !== 'true');
            return;
        }

        if (target.closest('#fullscreenToggle')) {
            AppFullscreen.toggle();
            return;
        }

        if (target.closest('#sidebarToggle')) {
            AppUI.toggleSidebar();
            return;
        }

        if (target.closest('#sidebarOverlay')) {
            AppUI.closeSidebarMobile();
            return;
        }

        if (target.closest('#settingsToggle')) {
            AppUI.openOffcanvas();
            return;
        }

        if (target.closest('#offcanvasClose') || target.closest('#offcanvasOverlay')) {
            AppUI.closeOffcanvas();
            return;
        }

        const modalTrigger = target.closest('[data-modal-open]');
        if (modalTrigger) {
            AppModals.open(modalTrigger.dataset.modalOpen, modalTrigger);
            return;
        }

        if (target.closest('[data-modal-close]') || target.closest('#modalOverlay')) {
            AppModals.close();
            return;
        }

        const toastClose = target.closest('[data-toast-close]');
        if (toastClose) {
            AppToasts.dismiss(toastClose.closest('.toast-card'));
            return;
        }

        const dropdownButton = target.closest('[data-dropdown]');
        if (dropdownButton) {
            event.stopPropagation();
            AppUI.toggleDropdown(dropdownButton.dataset.dropdown);
            return;
        }

        const sidebarDropdown = target.closest('[data-sidebar-dropdown]');
        if (sidebarDropdown) {
            AppUI.toggleSidebarDropdown(sidebarDropdown.dataset.sidebarDropdown);
            return;
        }

        if (target.closest('.sidebar-link') || target.closest('.sidebar-sub-link')) {
            AppUI.closeSidebarMobile();
            return;
        }

        const swatch = target.closest('.color-swatch');
        if (swatch) {
            AppTheme.setAccentColor(swatch.dataset.color);
            return;
        }

        const toggleSwitch = target.closest('.toggle-switch:not(#offcanvasDarkMode)');
        if (toggleSwitch) {
            toggleSwitch.classList.toggle('active');
            return;
        }

        if (!target.closest('.dropdown-wrap')) {
            AppUI.closeAllDropdowns();
        }
    });
};

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initApp, { once: true });
} else {
    initApp();
}
