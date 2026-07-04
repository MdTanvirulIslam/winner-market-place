/** @type {import('tailwindcss').Config} */
export default {
  content: [
    './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
    './storage/framework/views/*.php',
    './resources/**/*.blade.php',
    './resources/**/*.js',
    './app/View/**/*.php',
  ],
  darkMode: ['selector', '[data-theme="dark"]'],
  theme: {
    extend: {
      colors: {
        accent: 'var(--accent)',
        'accent-hover': 'var(--accent-hover)',
        'accent-light': 'var(--accent-light)',
        'accent-subtle': 'var(--accent-subtle)',
        warning: 'var(--warning)',
        danger: 'var(--danger)',
        success: 'var(--success)',
        info: 'var(--info)',
        bg: 'var(--bg)',
        card: 'var(--bg-card)',
        'card-hover': 'var(--bg-card-hover)',
        sidebar: 'var(--bg-sidebar)',
        'sidebar-hover': 'var(--bg-sidebar-hover)',
        'sidebar-active': 'var(--bg-sidebar-active)',
        topbar: 'var(--bg-topbar)',
        dropdown: 'var(--bg-dropdown)',
        offcanvas: 'var(--bg-offcanvas)',
        input: 'var(--bg-input)',
        text: 'var(--text)',
        muted: 'var(--text-muted)',
        'sidebar-text': 'var(--text-sidebar)',
        'sidebar-active-text': 'var(--text-sidebar-active)',
        border: 'var(--border)',
      },
      fontFamily: {
        heading: ['var(--font-heading)'],
        body: ['var(--font-body)'],
      },
      borderRadius: {
        sm: 'var(--radius-sm)',
        DEFAULT: 'var(--radius)',
        lg: 'var(--radius-lg)',
      },
      boxShadow: {
        DEFAULT: 'var(--shadow)',
        lg: 'var(--shadow-lg)',
        dropdown: 'var(--shadow-dropdown)',
      },
      spacing: {
        sidebar: 'var(--sidebar-width)',
        topbar: 'var(--topbar-height)',
      },
      transitionDuration: {
        DEFAULT: '300ms',
      },
      transitionTimingFunction: {
        smooth: 'cubic-bezier(0.4, 0, 0.2, 1)',
      },
    },
  },
  plugins: [],
};
