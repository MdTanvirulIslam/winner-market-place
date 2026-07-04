import { setIcon } from './icons.js';

const AppTheme = {
  accentMap: {
    teal: { accent: '#0d9488', hover: '#0f766e', light: '#14b8a6', subtle: 'rgba(13,148,136,0.1)', glow: 'rgba(13,148,136,0.25)' },
    blue: { accent: '#3b82f6', hover: '#2563eb', light: '#60a5fa', subtle: 'rgba(59,130,246,0.1)', glow: 'rgba(59,130,246,0.25)' },
    violet: { accent: '#8b5cf6', hover: '#7c3aed', light: '#a78bfa', subtle: 'rgba(139,92,246,0.1)', glow: 'rgba(139,92,246,0.25)' },
    pink: { accent: '#ec4899', hover: '#db2777', light: '#f472b6', subtle: 'rgba(236,72,153,0.1)', glow: 'rgba(236,72,153,0.25)' },
    amber: { accent: '#f59e0b', hover: '#d97706', light: '#fbbf24', subtle: 'rgba(245,158,11,0.1)', glow: 'rgba(245,158,11,0.25)' },
    red: { accent: '#ef4444', hover: '#dc2626', light: '#f87171', subtle: 'rgba(239,68,68,0.1)', glow: 'rgba(239,68,68,0.25)' },
  },

  applyDarkMode(on) {
    document.documentElement.setAttribute('data-theme', on ? 'dark' : 'light');
    setIcon('#darkModeToggleIcon', on ? 'sun' : 'moon');

    const offcanvasToggle = document.getElementById('offcanvasDarkMode');
    if (offcanvasToggle) {
      offcanvasToggle.classList.toggle('active', on);
    }

    localStorage.setItem('winnerTAM-dark', String(on));
  },

  setAccentColor(name) {
    const colors = this.accentMap[name];
    if (!colors) {
      return;
    }

    const root = document.documentElement.style;
    root.setProperty('--accent', colors.accent);
    root.setProperty('--accent-hover', colors.hover);
    root.setProperty('--accent-light', colors.light);
    root.setProperty('--accent-subtle', colors.subtle);
    root.setProperty('--accent-glow', colors.glow);

    document.querySelectorAll('.color-swatch').forEach((swatch) => {
      swatch.classList.toggle('active', swatch.dataset.color === name);
    });

    localStorage.setItem('winnerTAM-accent', name);
  },

  init() {
    const isDark = localStorage.getItem('winnerTAM-dark') === 'true';
    this.applyDarkMode(isDark);

    const savedAccent = localStorage.getItem('winnerTAM-accent');
    if (savedAccent && this.accentMap[savedAccent]) {
      this.setAccentColor(savedAccent);
    }
  },
};

export default AppTheme;
