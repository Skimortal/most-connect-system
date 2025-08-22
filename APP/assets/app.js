import './bootstrap.js';
/*
 * Welcome to your app's main JavaScript file!
 *
 * This file will be included onto the page via the importmap() Twig function,
 * which should already be in your base.html.twig.
 */
import './styles/app.css';

import 'adminator-admin-dashboard/src/assets/scripts/app.js';
import './dashboard.js';
import './datatables.js';

console.log('This log comes from assets/app.js - welcome to AssetMapper! 🎉');

function initApp() {
    initDeleteConfirm();
    initInvoiceDesignHandling();
    initInvoiceFilter();
}

function initDeleteConfirm() {
    document.addEventListener('click', function (e) {
        const el = e.target.closest('.confirmRemoveItem');
        if (!el) return;

        const msg = el.dataset.confirmMessage || 'Eintrag wirklich löschen?';
        if (!window.confirm(msg)) {
            e.preventDefault();
            e.stopImmediatePropagation();
        }
    }, true);
}

function initInvoiceDesignHandling() {
    document.addEventListener('change', function(e){
        if (!e.target.matches('.design-input')) return;
        const group = e.target.closest('.design-chooser');
        if (!group) return;
        group.querySelectorAll('.design-option').forEach(o => o.classList.remove('is-selected'));
        e.target.closest('.design-option')?.classList.add('is-selected');
    });

    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('.design-input:checked')
            .forEach(i => i.closest('.design-option')?.classList.add('is-selected'));
    });
}

function initInvoiceFilter() {
    document.addEventListener('DOMContentLoaded', function () {
        const el = document.getElementById('invoiceFilters');
        if (!el) return;

        // Restore from localStorage
        const open = localStorage.getItem('invoiceFiltersOpen');
        if (open === '1') new bootstrap.Collapse(el, {show: true});
        if (open === '0') new bootstrap.Collapse(el, {toggle: false});

        el.addEventListener('shown.bs.collapse', () => localStorage.setItem('invoiceFiltersOpen', '1'));
        el.addEventListener('hidden.bs.collapse', () => localStorage.setItem('invoiceFiltersOpen', '0'));
    });
}

document.addEventListener('DOMContentLoaded', initApp);
document.addEventListener('turbo:render', initApp);
