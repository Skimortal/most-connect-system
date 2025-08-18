import DataTable from 'datatables.net-bs5';
import 'datatables.net-responsive-bs5';

import 'datatables.net-bs5/css/dataTables.bootstrap5.css';
import 'datatables.net-responsive-bs5/css/responsive.bootstrap5.css';

function initDatatables() {
    document.querySelectorAll('table.mcDataTable').forEach((el) => {
        if (el.dataset.dtInit) return;
        new DataTable(el, {
            responsive: true,
            pageLength: 10,
            lengthMenu: [10, 25, 50, 100],
            language: {
                search: 'Suchen:',
                lengthMenu: '_MENU_ pro Seite',
                info: 'Zeige _START_–_END_ von _TOTAL_',
                zeroRecords: 'Keine Einträge gefunden',
                infoEmpty: 'Keine Einträge verfügbar'
            }
        });
        el.dataset.dtInit = '1';
    });
}

document.addEventListener('DOMContentLoaded', initDatatables);
document.addEventListener('turbo:render', initDatatables);
