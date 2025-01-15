function highlightTableRow(id) {
    const tableRows = $('tbody tr');

    for(row of tableRows) {
        row.id != id ? 
            row.classList.remove("table-success"): 
            row.classList.add("table-success");
    }
}

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