/**
 * Podświetla wiersz tabeli o określonym identyfikatorze, usuwając podświetlenie z pozostałych wierszy.
 *
 * @param {string} id - Identyfikator wiersza tabeli, który ma zostać podświetlony.
 */
function highlightTableRow(id) {
    const tableRows = $('tbody tr');

    for(row of tableRows) {
        row.id != id ? 
            row.classList.remove("table-success"): 
            row.classList.add("table-success");
    }
}

/**
 * Inicjalizuje i wypełnia tabelę HTML za pomocą DataTables na podstawie przekazanych danych.
 *
 * @param {string} rows - HTML wierszy do wstawienia w sekcji `<tbody>` tabeli.
 * @param {Array} columnsConfig - Konfiguracja kolumn dla DataTables.
 * @param {Array} columnDefsConfig - Definicje kolumn dla DataTables.
 * @param {boolean} [hasPaging=true] - Określa, czy tabela ma być stronicowana.
 * @param {string} [emptyTableText="Brak danych w tabeli"] - Tekst wyświetlany, gdy tabela jest pusta.
 */
function populateTable(rows, columnsConfig, columnDefsConfig, hasPaging=true, emptyTableText="Brak danych w tabeli")
{
    const table = $('#table');

    if ($.fn.DataTable.isDataTable(table)) {
        table.DataTable().clear().destroy();
    }

    table.find('tbody').empty();

    $('#table tbody').html(rows);

    table.DataTable({
        searching: false,
        info: false,
        paging: hasPaging,
        lengthMenu: [5, 10, 25],
        language: {
            lengthMenu: 'Wyświetl _MENU_ wpisów na stronę',
            emptyTable: emptyTableText
        },
        responsive: true,
        columns: columnsConfig,
        columnDefs: columnDefsConfig
    });
}

// Export modułów na potrzeby testów
// module.exports = {populateTable};