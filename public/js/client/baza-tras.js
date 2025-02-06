/**
 * Inicjalizacja widoku po załadowaniu dokumentu.
 * - Inicjalizuje mapę i markery Leaflet.
 * - Pobiera dane o ścieżkach i mapach z API.
 * - Konfiguruje tabelę z trasami, obsługę wyboru wierszy i interakcje z modalem.
 */
$(document).ready(function() {
    // Inicjalizacja mapy i markerów
    let map = initMap("map");
    let markers = initMarkers();
    let paths = [];
    let maps = [];
    
    // Konfiguracja kolumn i definicji kolumn dla DataTables
    const columnsConfig = [
        { width: '28%' }, 
        { width: '15%' }, 
        { width: '8%' }, 
        { width: '8%' }, 
        { width: '13%' },  
        null
    ];

    const columnDefsConfig = [
        { responsivePriority: 1, targets: 0 }, 
        { responsivePriority: 4, targets: 1 },
        { responsivePriority: 3, targets: 2 },
        { responsivePriority: 2, targets: 3 },
        { responsivePriority: 5, targets: 4 }, 
        { responsivePriority: 1, targets: 5 },
        { orderable: false, targets: 5}
    ];

    // Pobranie danych o ścieżkach z API
    getPathData()
        .then(function(result) {
            // Filtrowanie tras zawierających punkty
            paths = filterPathsWithPoints(result.data);

            // Generowanie wierszy tabeli
            const rows = paths.map(path => `
                <tr class="" data-id="${path.id}" id="${path.id}">
                    <td>${path.name}</td>
                    <td>${getPathAreaNames(path.points)}</td>
                    <td>${path.points.length}</td>
                    <td>${calculateRouteLength(path.points)}</td>
                    <td>${checkRouteType(path.points)}</td>
                    <td><button data-id="${path.id}" class="btn btn-success btn-sm w-100 download-btn">Pobierz mapę</button></td>
                </tr>
            `).join('');

            // Wypełnienie tabeli i inicjalizacja DataTables
            populateTable(rows, columnsConfig, columnDefsConfig);
    }).catch((xhr) => {
        const message = xhr.responseJSON?.message || 'Wystąpił błąd';
        alert(message);
    });

    // Pobranie danych o mapach z API
    getMapUIData()
        .then(function(result) {
            maps = result;
        }).catch((xhr) => {
            const message = xhr.responseJSON?.message || 'Wystąpił błąd';
            alert(message);
        });
    
    // Obsługa kliknięcia w wiersz tabeli (podświetlanie i aktualizacja mapy)
    $('#table tbody').on('click', 'tr', function() {
        let id = $(this).data('id');
        highlightTableRow(id);

        let points = paths.find(p => p.id == id).points;
        updateMap(points, markers, map);
    });

    // Obsługa kliknięcia przycisku "Pobierz mapę" w tabeli
    $(document).on('click', '.download-btn', function() {

        const pathId = $(this).data('id');

        $('#mapList').html(prepareHtmlForMapChoiceModal(maps, pathId));

        $('#mapModal').modal('show');
    });

    // Obsługa kliknięcia przycisku "Pobierz"
    $('#download-file').on('click', function () {
        const selectedMapId = $('input[name="mapRadio"]:checked').data('id');
        
        if (!selectedMapId) {
            alert('Wybierz mapę podkładową przed pobraniem.');
            return;
        }

        const pathId = $('#modalContainer').data('id');
        const points = paths.find(p => p.id == pathId).points;

        // Pobranie mapy na podstawie wybranego ID
        downloadMap(selectedMapId, points);
    });

    // Zamknięcie popupów na mapie po kliknięciu poza nią
    $(document).on('click', function(event){
        if(!map.getContainer().contains(event.target)) {
            map.closePopup();
        }
    });


   



});