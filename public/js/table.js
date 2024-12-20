
function highlightTableRow(id) {
    const tableRows = $('tbody tr');

    for(row of tableRows) {
        row.id != id ? 
            row.classList.remove("table-success"): 
            row.classList.add("table-success");
    }
}


function populateTable(paths, tableUrl)
{
    const table = $('#table');
    let rows;
    let columnsConfig;
    let columnDefsConfig;

    // Sprawdź, czy tabela DataTables już istnieje i zniszcz ją
    if ($.fn.DataTable.isDataTable(table)) {
        table.DataTable().clear().destroy();
    }

    // Wyczyszczenie zawartości tabeli
    table.find('tbody').empty();

    if(tableUrl == "admin/baza-tras") {
        rows = paths.map(path => `
            <tr class="" data-id="${path.id}" id="${path.id}">
                <td>${path.name}</td>
                <td>${checkPathArea(path.points)}</td>
                <td>${path.points.length}</td>
                <td>${calculateRouteLength(path.points)}</td>
                <td>${checkRouteType(path.points)}</td>
                <td><button data-id="${path.id}" class="btn btn-warning btn-sm w-100 edit-btn">Edytuj</button></td>
                <td><button data-id="${path.id}" class="btn btn-danger btn-sm w-100 delete-btn">Usuń</button></td>
            </tr>
        `).join('');

        columnsConfig = [
            { width: '25%' },
            { width: '20%' },
            { width: '10%' },
            { width: '10%' },
            { width: '15%' }, 
            null,
            null
        ];

        columnDefsConfig =[
            { responsivePriority: 5, targets: 0 },
            { responsivePriority: 3, targets: 1 },
            { responsivePriority: 2, targets: 2 },
            { responsivePriority: 2, targets: 3 },
            { responsivePriority: 4, targets: 4 },
            { responsivePriority: 1, targets: 5 },
            { responsivePriority: 1, targets: 6 }
        ]
        
    }
    else if (tableUrl == "baza-tras") {
        rows = paths.map(path => `
            <tr class="" data-id="${path.id}" id="${path.id}">
                <td>${path.name}</td>
                <td>${checkPathArea(path.points)}</td>
                <td>${path.points.length}</td>
                <td>${calculateRouteLength(path.points)}</td>
                <td>${checkRouteType(path.points)}</td>
                <td><button data-id="${path.id}" class="btn btn-success btn-sm w-100 download-btn">Pobierz mapę</button></td>
            </tr>
        `).join('');

        columnsConfig = [
            { width: '25%' }, // Kolumna 1
            { width: '20%' }, // Kolumna 2
            { width: '10%' }, // Kolumna 3
            { width: '10%' }, // Kolumna 4
            { width: '15%' },  // Kolumna 5
            null// Kolumna 6
        ];

        columnDefsConfig = [
            { responsivePriority: 5, targets: 0 }, // Priorytet dla kolumny 1
            { responsivePriority: 3, targets: 1 },
            { responsivePriority: 2, targets: 2 },
            { responsivePriority: 2, targets: 3 },
            { responsivePriority: 4, targets: 4 },  // Priorytet dla kolumny 5
            { responsivePriority: 1, targets: 5 }  // Priorytet dla kolumny 5
        ];
    }
    else if (tableUrl == "generator") {
        rows = paths.map(path => `
            <tr class="" data-id="${path.id}" id="${path.id}">
                
                <td>${checkPathArea(path.points)}</td>
                <td>${path.points.length}</td>
                <td>${calculateRouteLength(path.points)}</td>
                <td>${checkRouteType(path.points)}</td>
                <td><button data-id="${path.id}" class="btn btn-success btn-sm w-100 download-btn">Pobierz mapę</button></td>
            </tr>
        `).join('');

        columnsConfig = [
            { width: '25%' }, // Kolumna 1 (indeks 1 w tabeli)
            { width: '15%' }, // Kolumna 2 (indeks 2 w tabeli)
            { width: '15%' }, // Kolumna 3 (indeks 3 w tabeli)
            { width: '20%' },  // Kolumna 4 (indeks 4 w tabeli)
            null  // Kolumna 5 (indeks 5 w tabeli)
        ];

        columnDefsConfig = [
            { responsivePriority: 3, targets: 0 }, // Priorytet dla kolumny 1 (indeks 1 w tabeli)
            { responsivePriority: 2, targets: 1 },
            { responsivePriority: 2, targets: 2 },
            { responsivePriority: 4, targets: 3 },  // Priorytet dla kolumny 4 (indeks 4 w tabeli)
            { responsivePriority: 1, targets: 4 },
        ];
    }

    $('#table tbody').html(rows);

    table.DataTable({
        searching: false,
        info: false,
        lengthMenu: [5, 10, 15],
        language: {
            lengthMenu: 'Wyświetl _MENU_ wpisów na stronę'
        },
        responsive: true,
        columns: columnsConfig,
        columnDefs: columnDefsConfig
    });
    
    
}

function populateTable2(rows, columnsConfig, columnDefsConfig)
{
    console.log("populateTable2()");
    const table = $('#table');

    if ($.fn.DataTable.isDataTable(table)) {
        table.DataTable().clear().destroy();
    }

    table.find('tbody').empty();

    $('#table tbody').html(rows);

    table.DataTable({
        searching: false,
        info: false,
        lengthMenu: [5, 10, 25],
        language: {
            lengthMenu: 'Wyświetl _MENU_ wpisów na stronę'
        },
        responsive: true,
        columns: columnsConfig,
        columnDefs: columnDefsConfig
    });
}