document.addEventListener('DOMContentLoaded', function () {
    console.log('custom.js geladen.');

    const table = document.querySelector('.mcDataTable');
    if (table) {
        new DataTable(table, {
            pageLength: 25,
            order: [[0, 'asc']],
            // columnDefs: [
            //     { targets: -1, orderable: false, searchable: false } // Aktionen-Spalte
            // ],
            paging: true,
            searching: true,
            ordering: true,
            dataSrc: "",
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.5/i18n/de-DE.json',
                emptyTable: 'Keine Daten gefunden',
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
