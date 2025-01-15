$(document).ready(function() {
    $('#alertMessage').hide();

    let points = [];
    let maps = [];
    const map = initMap("map");
    const markers = initMarkers();
    let pathMarkers = initMarkers();
    let startMarker = null;
    let endMarker = null;

    getPointsData()
        .then(function(result) {
            points = result.data;
            resetDropdowns(points);
            initializeSortable();
            initPointsPreview(points, markers, map, "planer");
        });
    
    getMapUIData()
        .then(function(result) {
            maps = result;
            console.log(maps);
        }).catch((error) => console.log(error));

    $(document).on('change', '.dropdown', function() {
        $('#alertMessage').hide();

        var parentGroup = $(this).closest('.dropdown-group');
        parentGroup.find('.remove-btn, .handle').show();
        // parentGroup.find('.handle').show();
    
        if ($('#dropdown-container .dropdown-group').last().find('select').val()) {
            $('#dropdown-container').append(createDropdown(points));
        }

        updatePath();
    });

    $(document).on('click', '.remove-btn', function() {
        $(this).closest('.dropdown-group').remove();
        updatePath();
    });

    $('#reset-btn').on('click', function() {
        resetDropdowns(points);
        updatePath();
        resetMarkers([startMarker, endMarker]);
        startMarker = null;
        endMarker = null;
    });



    $(document).on('click', '#finish-btn', function() {

        let pathPoints = collectPoints('select', points);
        if(pathPoints.length < 2) {
            const message = 'Trasa musi się składać z co najmniej dwóch punktów.';
            $('#alertMessage').text(message).show();
            return;
        }

        $('#mapList').html(prepareHtmlForMapChoiceModal(maps, 0)); // Wstawienie wygenerowanej listy do modala

        $('#mapModal').modal('show'); // Wyświetlenie modala
    });

    // Obsługa kliknięcia przycisku "Pobierz"
    $('#download-file').on('click', function () {
        const selectedMapId = $('input[name="mapRadio"]:checked').data('id');
        
        if (!selectedMapId) {
            alert('Wybierz mapę podkładową przed pobraniem.');
            return;
        }

        let pathPoints = collectPoints('select', points);

        if(pathPoints.length > 2) {

            pathPoints.forEach((point, index) => {
                point.position = index + 1;
            });

            downloadMap(selectedMapId, pathPoints);
        }

    });
        
    $(document).on('click', '#add-point-btn', function () {
        const buttonValue = $(this).val();
        const lastDropdown = $('.form-select').last();
    
        if (lastDropdown.length) {
            lastDropdown.val(buttonValue).trigger('change');
            lastDropdown.closest('.dropdown-group').find('.remove-btn').show();
        }
    });

    function updatePath() {
        let pathPoints = collectPoints('select', points);
        console.log("updatePath() pathPoints: " );
        console.log(pathPoints);
        if(pathPoints.length > 0) {
            let startPointId = pathPoints[0].id;
            let endPointId = pathPoints[pathPoints.length - 1].id;
    
            startMarker = setStartPoint(startPointId, markers.getLayers(), startMarker, endMarker);
            endMarker = setEndPoint(endPointId, markers.getLayers(), startMarker, endMarker);
        }
    
        drawPath(pathPoints, pathMarkers, map);
    };

    function initializeSortable() {
        $("#dropdown-container").sortable({
            items: "> .dropdown-group:not(:last-child)", // Wyklucz ostatni element
            handle: ".handle", // Tylko lista rozwijalna jako uchwyt
            update: function (event, ui) {
                updatePath(); // Aktualizuj trasę po zmianie kolejności
            }
        }).sortable("refresh"); // Odśwież instancję
    };

    // zamknij okna popup znaczników przy kliknięciu poza mapą
    $(document).on('click', function(event){
        if(!map.getContainer().contains(event.target)) {
            map.closePopup();
        }
    });


});













