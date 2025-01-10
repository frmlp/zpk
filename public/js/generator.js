$(document).ready(function() {
    let points = [];
    let paths = [];
    let maps = [];
    const map = initMap("map");
    const markers = L.layerGroup();
    let startMarker = null;
    let endMarker = null;
    
    getPointsData()
        .then(function(result) {
            points = result.data;
            initPointsPreview(points, markers, map, "generator");
            populateDropdowns(points);
        })
        .catch(() => console.log("Error"));

    getMapUIData()
        .then(function(result) {
            maps = result;
            console.log(maps);
        }).catch((error) => console.log(error));

    $('.generate-btn').on('click', function(event) {
        event.preventDefault(event);
        let startPoint = $('#start-point').val();
        let endPoint = $('#end-point').val();
        let distance = $('input[name="distance"]:checked').val();
        let points = $('input[name="points"]:checked').val();
        getGeneratorData(startPoint, endPoint, distance, points)
            .then(function(result) {
                paths = result.data;
                $('#form-wrapper').hide();
                $('#table-wrapper').show();
                populateTable(paths, "generator");
                updateMap(null, markers, map);
                
            }).catch((error) => console.log(error));

    });

    $('#change-parameters-btn').on('click', function(event) {
        // console.log(points);
        $('#table-wrapper').hide();
        $('#form-wrapper').show();
        initPointsPreview(points, markers, map, "generator");
        $('#start-point').change();
        $('#end-point').change();
    });

    $('#table tbody').on('click', 'tr', function() {
        let id = $(this).data('id');
        highlightTableRow(id);
        let points = paths.find(r => r.id == id).points;
        updateMap(points, markers, map);

    });


    // Event listeners for dropdown changes
    $('#start-point').on('change', function () {
        const selectedPointId = $(this).val();
        startMarker = setStartPoint(selectedPointId, markers.getLayers(), startMarker, endMarker);
    });
    
    $('#end-point').on('change', function () {
        const selectedPointId = $(this).val();
        endMarker = setEndPoint(selectedPointId, markers.getLayers(), startMarker, endMarker);
    });

    // Event listeners for button clicks in marker popups
    
    $(document).on('click', '.route-start-btn', function () {
        const pointId = $(this).val();
        $('#start-point').val(pointId).change();
    });
    
    $(document).on('click', '.route-finish-btn', function () {
        const pointId = $(this).val();
        $('#end-point').val(pointId).change();
    });

    // $(document).on('click', '.download-btn', function() {
    //     let id = $(this).data('id');
    //     let points = paths.find(r => r.id == id).points;
    //     downloadMap(points);
    // });

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
        let pathPoints = paths.find(p => p.id == pathId).points;

        pathPoints.forEach((point, index) => {
            point.position = index + 1;
        });

        // Wysłanie żądania GET do API w celu pobrania pliku mapy
        downloadMap(selectedMapId, pathPoints);
    });

});