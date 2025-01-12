$(document).ready(function() {
    let map = initMap("map");
    let markers = initMarkers();
    let paths = [];
    let maps = [];
    
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

    getPathData()
        .then(function(result) {

            paths = filterPathsWithPoints(result.data);

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

            populateTable(rows, columnsConfig, columnDefsConfig);
    }).catch((error) => console.log(error));

    getMapUIData()
        .then(function(result) {
            maps = result;
            console.log(maps);
        }).catch((error) => console.log(error));
    
    $('#table tbody').on('click', 'tr', function() {
        let id = $(this).data('id');
        highlightTableRow(id);

        let points = paths.find(p => p.id == id).points;
        updateMap(points, markers, map);
    });

    $(document).on('click', '.download-btn', function() {

        const pathId = $(this).data('id'); // Pobranie ID mapy z przycisku

        $('#mapList').html(prepareHtmlForMapChoiceModal(maps, pathId)); // Wstawienie wygenerowanej listy do modala

        $('#mapModal').modal('show'); // Wyświetlenie modala
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

        // Wysłanie żądania GET do API w celu pobrania pliku mapy
        downloadMap(selectedMapId, points);
    });

   



});