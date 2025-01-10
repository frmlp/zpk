$(document).ready(function() {
    let map = initMap("map");
    let markers = initMarkers();
    let paths = [];
    let maps = [];
    // let table = initTable(true);
    getPathData()
        .then(function(result) {
            // console.log("halo");
            paths = filterPathsWithPoints(result.data);
            // console.log(paths);
            populateTable(paths, "baza-tras");
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