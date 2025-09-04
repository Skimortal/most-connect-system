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

document.addEventListener('DOMContentLoaded', initApp);
document.addEventListener('turbo:render', initApp);
