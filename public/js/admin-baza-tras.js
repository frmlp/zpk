$(document).ready(function() {
    let map = initMap("map");
    let modalMap = initMap("map-modal");
    let markers = initMarkers();
    let modalMarkers = initMarkers();
    let modalPathMarkers = initMarkers();
    let startMarker = null;
    let endMarker = null;
    let paths = [];
    let points = [];
    // let table = initTable(true);
    getPathData()
        .then(function(result) {
            // console.log("halo");
            paths = result.data;
            // console.log(jsonresponse);
            // console.log(window.location.pathname);
            populateTable(paths, "admin/baza-tras");
    }).catch((error) => console.log(error));

    getPointsData()
        .then(function(result) {
            points = result.data;
            // resetDropdowns(points);
            initializeSortable();
            // initPointsPreview(points, markers, modalMap, "planer");
            // console.log("pobrano punkty");
        });
    
    $('#table tbody').on('click', 'tr', function() {
        let id = $(this).data('id');
        highlightTableRow(id);

        let points = paths.find(p => p.id == id).points;
        updateMap(points, markers, map);
    });

    $('#newPathBtn').on('click', function() {
        $('#pathModalLabel').text('Nowa trasa');
        $('#pathForm').attr('action', '/admin/paths');
        $('#pathForm').attr('method', 'POST'); // Metoda POST do tworzenia nowego punktu
        $('#pathForm').find('input[name="_method"]').remove(); 

        $('#pathForm')[0].reset(); // Wyczyść formularz

        $('#pathModal').modal('show');

        
        setTimeout(() => {
            modalMap.invalidateSize(); // Poprawienie rozmiaru mapy w modal
        }, 250);
        
       

        resetDropdowns(points);
        resetMapView(modalMap);
        // let pathPoints = collectPoints('select', points);
        // console.log("updatePath() pathPoints: " );
        // console.log(pathPoints);
        // initializeSortable();
        updatePath();
        resetMarkers([startMarker, endMarker]);
        startMarker = null;
        endMarker = null;
        initPointsPreview(points, modalMarkers, modalMap, "planer");

        
    });

    $(document).on('change', '.dropdown', function() {
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

    $(document).on('click', '#add-point-btn', function () {
        const buttonValue = $(this).val();
        const lastDropdown = $('.form-select').last();
    
        if (lastDropdown.length) {
            lastDropdown.val(buttonValue).trigger('change');
            lastDropdown.closest('.dropdown-group').find('.remove-btn').show();
        }
    });

    $('#table').on('click', '.edit-btn', function() {
        let id = $(this).data('id');
        // console.log(id);
        path = paths.find(path => path.id === id);
        // console.log(path);
        $('#pathForm').attr('action', '/admin/paths/' + path.id);
        $('#pathForm').attr('method', 'POST'); // Ustawienie metody POST, a Laravel obsłuży PUT przez ukryte pole _method
        $('#pathForm').append('<input type="hidden" name="_method" value="PUT">');
        // wypełnienie formularza danymi

        $('#pathModalLabel').text('Edytuj Trasę');
        $('#pathName').val(path.name);
        // $('#pointDescription').val(pointData.description);
        // $('#pointEasting').val(pointData.easting);
        // $('#pointNorthing').val(pointData.northing);

        $('#pathModal').modal('show');

        setTimeout(() => {
            modalMap.invalidateSize(); // Poprawienie rozmiaru mapy w modal
        }, 250);

        resetDropdowns(points);
        resetMapView(modalMap);
        resetMarkers([startMarker, endMarker]);
        startMarker = null;
        endMarker = null;
        initPointsPreview(points, modalMarkers, modalMap, "planer");

        path.points.sort((p1, p2) => {
            return p1.position - p2.position;
        })

        path.points.forEach(point => {
            const lastDropdown = $('.form-select').last();
    
            if (lastDropdown.length) {
                lastDropdown.val(point.id).trigger('change');
                lastDropdown.closest('.dropdown-group').find('.remove-btn').show();
            }
        });


    });

    $('#pathForm').on('submit', function(event) {
        event.preventDefault();
        let pathPoints = collectPoints('#dropdown-container select', points).map(point => point.id);

        if(pathPoints.length < 2) {
            
            return;
        }

        // Usuń wcześniej dodane pole 'points', jeśli istnieje
        $('#pathForm input[name="points"]').remove();

        // Dodaj ukryte pole z tablicą 'points'
        $('<input>').attr({
            type: 'hidden',
            name: 'points',
            value: JSON.stringify(pathPoints) // Przekazujemy tablicę jako JSON
        }).appendTo('#pathForm');

        const form = $(this);
        const actionUrl = form.attr('action'); // Pobierz URL akcji
        const formData = form.serialize(); // Serializuj dane formularza

        $.ajax({
            url: actionUrl,
            method: form.attr('method'), // Pobierz metodę (POST lub inne)
            data: formData,
            success: function (response) {
                if (response.status === 200) {
                    location.reload(); // Odśwież stronę
                } else {
                    $('#error-message').text('Wystąpił nieoczekiwany błąd. Spróbuj ponownie.');
                }
            },
            error: function (xhr) {
                // Wyświetl wiadomość o błędzie w zależności od odpowiedzi serwera
                const errorMessage = xhr.responseJSON?.message || 'Wystąpił błąd. Spróbuj ponownie.';
                $('#error-message').text(errorMessage).show();
            }
        });
    })
    
    function updatePath() {
        let pathPoints = collectPoints('#dropdown-container select', points);
        console.log("updatePath() pathPoints: " );
        console.log(pathPoints);
        if(pathPoints.length > 0) {
            let startPointId = pathPoints[0].id;
            let endPointId = pathPoints[pathPoints.length - 1].id;
    
            startMarker = setStartPoint(startPointId, modalMarkers.getLayers(), startMarker, endMarker);
            endMarker = setEndPoint(endPointId, modalMarkers.getLayers(), startMarker, endMarker);
        }
    
        drawPath(pathPoints, modalPathMarkers, modalMap);
    };

    function initializeSortable() {
        $("#dropdown-container").sortable({
            items: "> .dropdown-group:not(:last-child)", // Wyklucz ostatni element
            handle: ".handle", // Tylko lista rozwijalna jako uchwyt
            update: function (event, ui) {
                updatePath(); // Aktualizuj trasę po zmianie kolejności
            }
        }).sortable("refresh"); // Odśwież instancję
    }
});

