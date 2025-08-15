document.addEventListener('DOMContentLoaded', function () {
    console.log('custom.js geladen.');

    const table = document.querySelector('.mcDataTable');
    if (table) {
        new DataTable(table, {
            paging: true,
            searching: true,
            ordering: true,
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.5/i18n/de-DE.json'
            }
        });
    }

    document.addEventListener('click', function (e) {
        const el = e.target.closest('.confirmRemoveItem');
        if (!el) return;

        const msg = el.dataset.confirmMessage || 'Eintrag wirklich löschen?';
        if (!window.confirm(msg)) {
            e.preventDefault();
            e.stopImmediatePropagation();
        }
    }, true);
});
