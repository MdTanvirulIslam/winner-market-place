import { renderIcons } from './icons.js';

const KNOWN_POSITIONS = new Set(['top-right', 'top-left', 'bottom-right', 'bottom-left']);
const KNOWN_VARIANTS = new Set(['success', 'info', 'warning', 'danger', 'error']);

const AppToasts = {
  state: {
    counter: 0,
    timers: new Map(),
  },

  normalizePosition(position) {
    return KNOWN_POSITIONS.has(position) ? position : 'top-right';
  },

  normalizeVariant(variant) {
    if (variant === 'error') {
      return 'danger';
    }

    return KNOWN_VARIANTS.has(variant) ? variant : 'success';
  },

  escapeHtml(value) {
    return String(value)
      .replaceAll('&', '&amp;')
      .replaceAll('<', '&lt;')
      .replaceAll('>', '&gt;')
      .replaceAll('"', '&quot;')
      .replaceAll("'", '&#39;');
  },

  iconForVariant(variant) {
    return {
      success: 'badge-check',
      info: 'circle-alert',
      warning: 'bell',
      danger: 'triangle-alert',
    }[variant] ?? 'badge-check';
  },

  viewport(position) {
    const normalized = this.normalizePosition(position);
    let viewport = document.querySelector(`.toast-viewport[data-position="${normalized}"]`);

    if (viewport instanceof HTMLElement) {
      return viewport;
    }

    viewport = document.createElement('div');
    viewport.className = `toast-viewport ${normalized}`;
    viewport.dataset.position = normalized;
    document.body.appendChild(viewport);
    return viewport;
  },

  dismiss(target) {
    const toast = typeof target === 'string' ? document.getElementById(target) : target;

    if (!(toast instanceof HTMLElement) || toast.classList.contains('is-dismissing')) {
      return;
    }

    const toastId = toast.dataset.toastId;
    const timer = toastId ? this.state.timers.get(toastId) : null;

    if (timer) {
      window.clearTimeout(timer);
      this.state.timers.delete(toastId);
    }

    toast.classList.add('is-dismissing');

    window.setTimeout(() => {
      toast.remove();
    }, 220);
  },

  show({
    title,
    message = '',
    variant = 'success',
    position = 'top-right',
    duration = 3200,
    meta = '',
  }) {
    const normalizedVariant = this.normalizeVariant(variant);
    const normalizedPosition = this.normalizePosition(position);
    const viewport = this.viewport(normalizedPosition);
    const toastId = `toast-${++this.state.counter}`;
    const safeTitle = this.escapeHtml(title || 'Notification');
    const safeMessage = this.escapeHtml(message);
    const safeMeta = this.escapeHtml(meta);
    const autoClose = Number.isFinite(Number(duration)) ? Math.max(1200, Number(duration)) : 3200;

    const toast = document.createElement('div');
    toast.className = `toast-card ${normalizedVariant}`;
    toast.dataset.toastId = toastId;
    toast.setAttribute('role', normalizedVariant === 'danger' ? 'alert' : 'status');
    toast.setAttribute('aria-live', normalizedVariant === 'danger' ? 'assertive' : 'polite');
    toast.innerHTML = `
      <button class="toast-close" data-toast-close aria-label="Dismiss notification"><span class="icon" data-icon="x"></span></button>
      <div class="toast-head">
        <div class="toast-icon"><span class="icon" data-icon="${this.iconForVariant(normalizedVariant)}"></span></div>
        <div class="toast-content">
          <div class="toast-title">${safeTitle}</div>
          ${safeMessage ? `<div class="toast-text">${safeMessage}</div>` : ''}
          ${safeMeta ? `<div class="toast-meta">${safeMeta}</div>` : ''}
        </div>
      </div>
      <div class="toast-progress"><span class="toast-progress-bar" style="animation-duration:${autoClose}ms;"></span></div>
    `;

    viewport.appendChild(toast);
    renderIcons(toast);

    window.requestAnimationFrame(() => {
      toast.classList.add('show');
    });

    const timer = window.setTimeout(() => {
      this.dismiss(toast);
    }, autoClose);

    this.state.timers.set(toastId, timer);
    return toast;
  },

  triggerFromElement(trigger) {
    if (!(trigger instanceof HTMLElement)) {
      return;
    }

    this.show({
      title: trigger.dataset.toastTitle,
      message: trigger.dataset.toastMessage,
      variant: trigger.dataset.toastVariant,
      position: trigger.dataset.toastPosition,
      duration: trigger.dataset.toastDuration,
      meta: trigger.dataset.toastMeta,
    });
  },

  initSessionMarkup() {
    document.querySelectorAll('[data-session-toast]').forEach((node) => {
      if (!(node instanceof HTMLElement)) {
        return;
      }

      this.show({
        title: node.dataset.toastTitle,
        message: node.dataset.toastMessage,
        variant: node.dataset.toastVariant,
        position: node.dataset.toastPosition,
        duration: node.dataset.toastDuration,
        meta: node.dataset.toastMeta,
      });

      node.remove();
    });
  },

  initFromLocation() {
    const params = new URLSearchParams(window.location.search);

    if (!params.has('toast')) {
      return;
    }

    this.show({
      variant: params.get('toast'),
      title: params.get('toast_title') ?? 'Session message',
      message: params.get('toast_message') ?? '',
      position: params.get('toast_position') ?? 'top-right',
      duration: params.get('toast_duration') ?? 3200,
      meta: params.get('toast_meta') ?? 'Redirect feedback',
    });

    ['toast', 'toast_title', 'toast_message', 'toast_position', 'toast_duration', 'toast_meta'].forEach((key) => {
      params.delete(key);
    });

    const nextSearch = params.toString();
    const nextUrl = `${window.location.pathname}${nextSearch ? `?${nextSearch}` : ''}${window.location.hash}`;
    window.history.replaceState({}, '', nextUrl);
  },

  init() {
    this.initSessionMarkup();
    this.initFromLocation();
  },
};

export default AppToasts;
