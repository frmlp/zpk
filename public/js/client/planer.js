/**
 * Funkcja inicjalizuje widok, konfigurując interakcje z mapą, punktami, trasami i interfejsem użytkownika.
 */
$(document).ready(function() {
    let points = [];
    let maps = [];
    const map = initMap("map");
    const markers = initMarkers();
    let pathMarkers = initMarkers();
    let startMarker = null;
    let endMarker = null;

    // Pobranie danych punktów z API
    getPointsData()
        .then(function(result) {
            points = result.data;
            points.forEach(point => {
                point.popup = point.code.concat(" - ", point.description) +
                `<button type='button' id='add-point-btn' value='${point.id}' class='btn btn-success btn-sm m-1 w-100'>Dodaj do trasy</button>`
            });

            resetDropdowns(points);
            initializeSortable();
            initPointsPreview2(points, markers, map);
        });
    
    // Pobranie danych map z API
    getMapUIData()
        .then(function(result) {
            maps = result;
        }).catch((xhr) => {
            const message = xhr.responseJSON?.message || 'Wystąpił błąd';
            alert(message);
        });

    // Obsługa zmiany wartości dropdownów w kontenerze.
    $(document).on('change', '.dropdown', function() {

        var parentGroup = $(this).closest('.dropdown-group');
        parentGroup.find('.remove-btn, .handle').show();
    
        if ($('#dropdown-container .dropdown-group').last().find('select').val()) {
            $('#dropdown-container').append(createDropdown(points));
        }

        updatePath();
    });

    // Obsługa kliknięcia przycisku usuwania dropdowna.
    $(document).on('click', '.remove-btn', function() {
        $(this).closest('.dropdown-group').remove();
        updatePath();
    });

    // Obsługa przycisku resetowania.
    $('#reset-btn').on('click', function() {
        $('#alertMessage').hide();
        resetDropdowns(points);
        updatePath();
        resetMarkers([startMarker, endMarker]);
        startMarker = null;
        endMarker = null;
    });

    // Obsługa przycisku zakończenia trasy.
    // Sprawdza, czy wybrano przynajmniej dwa punkty trasy.
    // Wyświetla modal z listą map do wyboru dla pobrania.
    $(document).on('click', '#finish-btn', function() {

        let pathPoints = collectPoints('select', points);
        if(pathPoints.length < 2) {
            const message = 'Trasa musi się składać z co najmniej dwóch punktów.';
            $('#alertMessage').text(message).show();
            return;
        }

        $('#mapList').html(prepareHtmlForMapChoiceModal(maps, 0));

        $('#mapModal').modal('show');
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

            for(let i = 0; i < pathPoints.length; i++) {                
                pathPoints[i].position = i + 1;
            }

            downloadMap(selectedMapId, pathPoints);
        }

    });
    
    // Obsługa przycisku "Dodaj do trasy" w popupie markera.
    $(document).on('click', '#add-point-btn', function () {
        const buttonValue = $(this).val();
        const lastDropdown = $('.form-select').last();
    
        if (lastDropdown.length) {
            lastDropdown.val(buttonValue).trigger('change');
            lastDropdown.closest('.dropdown-group').find('.remove-btn').show();
        }
    });

    
     // Funkcja aktualizuje trasę na mapie na podstawie wybranych punktów,
     // ustawia znaczniki punktu początkowego i końcowego,
     // rysuje trasę na mapie.
    function updatePath() {
        let pathPoints = collectPoints('select', points);

        if(pathPoints.length > 0) {
            let startPointId = pathPoints[0].id;
            let endPointId = pathPoints[pathPoints.length - 1].id;
    
            startMarker = setStartPoint(startPointId, markers.getLayers(), startMarker, endMarker);
            endMarker = setEndPoint(endPointId, markers.getLayers(), startMarker, endMarker);
        }
    
        drawPath(pathPoints, pathMarkers, map);
    };

    // Funkcja inicjalizuje możliwość sortowania dropdownów w kontenerze.
    function initializeSortable() {
        $("#dropdown-container").sortable({
            items: "> .dropdown-group:not(:last-child)",
            handle: ".handle",
            update: function (event, ui) {
                updatePath();
            }
        }).sortable("refresh");
    };

    // Zamknij okna popup znaczników przy kliknięciu poza mapą
    $(document).on('click', function(event){
        if(!map.getContainer().contains(event.target)) {
            map.closePopup();
        }
    });


});

