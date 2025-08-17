function initPage() {
    console.log('custom.js geladen.');
    initDataTables();

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

function initDataTables() {
    // jQuery/DataTables schon geladen?
    if (!window.jQuery || !$.fn.DataTable) return;

    document.querySelectorAll('table.mcDataTable').forEach((el) => {
        // Doppel-Init verhindern
        if ($.fn.DataTable.isDataTable(el)) return;

        $(el).DataTable({
            pageLength: 25,
            order: [[0, 'asc']],
            columnDefs: [
                { targets: -1, orderable: false, searchable: false } // Aktionen-Spalte
            ],
            paging: true,
            searching: true,
            ordering: true,
            // dataSrc: "",
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.5/i18n/de-DE.json',
                emptyTable: 'Keine Daten gefunden',
            }
        });
    });
}

document.addEventListener('DOMContentLoaded', initPage);
document.addEventListener('turbo:load', initDataTables);
document.addEventListener('turbo:frame-load', initDataTables);

// Vor dem Cachen DOM zurückbauen, damit beim nächsten Visit sauber neu initialisiert wird
document.addEventListener('turbo:before-cache', () => {
    if (!window.jQuery || !$.fn.DataTable) return;

    document.querySelectorAll('table.mcDataTable').forEach((el) => {
        // nur wenn noch im DOM und wirklich DataTable
        if (!el.isConnected) return;
        if (!$.fn.DataTable.isDataTable(el)) return;

        try {
            // wichtig: true → Wrapper entfernen, Original-Table wiederherstellen
            $(el).DataTable().destroy(true);
            // evtl. Inline-Styles entfernen, falls gesetzt
            el.removeAttribute('style');
        } catch (e) {
            // Element wurde gerade abgehängt? Fehler ignorieren.
            // console.warn('DT destroy skipped:', e);
        }
    });
});
