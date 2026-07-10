import './bootstrap';
import '../css/app.css';

/* ── Bootstrap 5 JS (dropdowns, collapse, modals, tooltips) ─────────────── */
import * as bootstrap from 'bootstrap';
window.bootstrap = bootstrap;

/* ── OverlayScrollbars (AdminLTE sidebar) ───────────────────────────────── */
import { OverlayScrollbars } from 'overlayscrollbars';
window.OverlayScrollbars = OverlayScrollbars;

/* ── AdminLTE 4 (sidebar toggle, treeview, push-menu) ───────────────────── */
import 'admin-lte';

/* ── HTMX ───────────────────────────────────────────────────────────────── */
import htmx from 'htmx.org';
window.htmx = htmx;

/* ── Alpine (small toggles, retained from Breeze) ───────────────────────── */
import Alpine from 'alpinejs';
window.Alpine = Alpine;
Alpine.start();

/* ──────────────────────────────────────────────────────────────────────────
   HTMX ⇄ Laravel wiring
   ────────────────────────────────────────────────────────────────────────── */

// Attach CSRF token to every htmx request.
document.addEventListener('htmx:configRequest', (event) => {
    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    if (token) {
        event.detail.headers['X-CSRF-TOKEN'] = token;
    }
});

// Global slim progress bar on any htmx request.
const progress = () => document.getElementById('htmx-progress');
document.addEventListener('htmx:beforeRequest', () => {
    const bar = progress();
    if (!bar) return;
    bar.classList.add('active');
    bar.style.width = '35%';
});
document.addEventListener('htmx:beforeSwap', () => {
    const bar = progress();
    if (bar) bar.style.width = '80%';
});
document.addEventListener('htmx:afterRequest', () => {
    const bar = progress();
    if (!bar) return;
    bar.style.width = '100%';
    setTimeout(() => {
        bar.classList.remove('active');
        bar.style.width = '0';
    }, 300);
});

// ── Feather → Bootstrap Icons shim ──────────────────────────────────────────
// Legacy views use <i data-feather="name">. Convert them to <i class="bi ...">
// so we don't depend on the old feather library. Runs on load + after swaps.
const FEATHER_TO_BI = {
    'grid': 'grid-3x3-gap', 'arrow-left': 'arrow-left', 'arrow-right': 'arrow-right',
    'plus': 'plus-lg', 'credit-card': 'credit-card', 'trash-2': 'trash', 'trash': 'trash',
    'briefcase': 'briefcase', 'eye': 'eye', 'edit-2': 'pencil', 'edit': 'pencil',
    'users': 'people', 'user': 'person', 'user-x': 'person-x', 'lock': 'lock',
    'image': 'image', 'external-link': 'box-arrow-up-right', 'check': 'check-lg',
    'check-circle': 'check-circle', 'send': 'send', 'download': 'download',
    'upload': 'upload', 'mail': 'envelope', 'phone': 'telephone', 'map-pin': 'geo-alt',
    'file-text': 'file-earmark-text', 'file': 'file-earmark', 'trending-up': 'graph-up-arrow',
    'clock': 'clock', 'calendar': 'calendar3', 'settings': 'gear', 'log-out': 'box-arrow-right',
    'x': 'x-lg', 'alert-circle': 'exclamation-circle', 'alert-triangle': 'exclamation-triangle',
    'chevrons-up': 'chevron-up',
};

function applyFeatherShim(root = document) {
    root.querySelectorAll('[data-feather]').forEach((el) => {
        const name = el.getAttribute('data-feather');
        const bi = FEATHER_TO_BI[name] || 'square';
        el.classList.add('bi', 'bi-' + bi);
        // Map any explicit pixel width onto font-size so the glyph scales.
        const w = el.style.width;
        if (w) el.style.fontSize = w;
        el.style.width = '';
        el.style.height = '';
        el.removeAttribute('data-feather');
    });
}
document.addEventListener('DOMContentLoaded', () => applyFeatherShim());
document.addEventListener('htmx:afterSwap', (e) => applyFeatherShim(e.target));

// Re-init Bootstrap tooltips after every swap (and on load).
function initTooltips(root = document) {
    root.querySelectorAll('[data-bs-toggle="tooltip"]').forEach((el) => {
        if (!bootstrap.Tooltip.getInstance(el)) new bootstrap.Tooltip(el);
    });
}
document.addEventListener('DOMContentLoaded', () => initTooltips());
document.addEventListener('htmx:afterSwap', (e) => initTooltips(e.target));

// Flash a dismissible toast-ish alert requested by the server via HX-Trigger.
document.addEventListener('flash', (event) => {
    const { level = 'success', message = '' } = event.detail || {};
    if (!message) return;
    const holder = document.getElementById('flash-holder');
    if (!holder) return;
    const el = document.createElement('div');
    el.className = `alert alert-${level} alert-dismissible fade show shadow-sm`;
    el.setAttribute('role', 'alert');
    el.innerHTML = `${message}<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>`;
    holder.appendChild(el);
    setTimeout(() => el.classList.remove('show'), 4000);
});

// ── Shared HTMX modal ───────────────────────────────────────────────────────
// Any element with hx-target="#app-modal-content" loads a form into the shared
// modal; we open it once the content arrives, and close it on request.
function appModal() {
    const el = document.getElementById('app-modal');
    return el ? bootstrap.Modal.getOrCreateInstance(el) : null;
}
document.addEventListener('htmx:afterSwap', (e) => {
    if (e.target.id === 'app-modal-content') {
        appModal()?.show();
    }
});
// Server can request the modal be closed via HX-Trigger: closeModal
document.body.addEventListener('closeModal', () => appModal()?.hide());
// Clear stale content after the modal is dismissed so the next open is fresh.
document.addEventListener('DOMContentLoaded', () => {
    const el = document.getElementById('app-modal');
    el?.addEventListener('hidden.bs.modal', () => {
        const c = document.getElementById('app-modal-content');
        if (c) c.innerHTML = '';
    });
});

// Wire OverlayScrollbars onto the sidebar once the DOM is ready.
document.addEventListener('DOMContentLoaded', () => {
    const sidebar = document.querySelector('.app-sidebar .sidebar-wrapper')
        || document.querySelector('.app-sidebar');
    if (sidebar && window.OverlayScrollbars) {
        OverlayScrollbars(sidebar, {
            scrollbars: { theme: 'os-theme-light', autoHide: 'leave', clickScroll: true },
        });
    }
});
