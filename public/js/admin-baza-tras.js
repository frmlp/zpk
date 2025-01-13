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

    const columnsConfig = [
        { width: '25%' },
        { width: '20%' },
        { width: '10%' },
        { width: '10%' },
        { width: '15%' }, 
        null,
        null
    ];

    const columnDefsConfig =[
        { responsivePriority: 1, targets: 0 },
        { responsivePriority: 4, targets: 1 },
        { responsivePriority: 3, targets: 2 },
        { responsivePriority: 2, targets: 3 },
        { responsivePriority: 5, targets: 4 },
        { responsivePriority: 1, targets: 5 },
        { responsivePriority: 1, targets: 6 },
        { orderable: false, targets: [5, 6]}
    ];
    
    csrfAjaxSetup();

    getAdminPathData()
        .then(function(result) {
            // console.log("halo");
            paths = filterPathsWithPoints(result.data);
            
            const rows = paths.map(path => `
                <tr class="" data-id="${path.id}" id="${path.id}">
                    <td>${path.name}</td>
                    <td>${getPathAreaNames(path.points)}</td>
                    <td>${path.points.length}</td>
                    <td>${calculateRouteLength(path.points)}</td>
                    <td>${checkRouteType(path.points)}</td>
                    <td><button data-id="${path.id}" class="btn btn-warning btn-sm w-100 edit-btn">Edytuj</button></td>
                    <td><button data-id="${path.id}" class="btn btn-danger btn-sm w-100 delete-btn">Usuń</button></td>
                </tr>
            `).join('');

            populateTable(rows, columnsConfig, columnDefsConfig);
    }).catch((error) => console.log(error));

    getAdminPointsData()
        .then(function(result) {
            points = result.data;
            // resetDropdowns(points);
            initializeSortable();
            // initPointsPreview(points, markers, modalMap, "planer");
            console.log(points);
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

    $('#table').on('click', '.delete-btn', function(event) {
        
        event.preventDefault();

        let id = $(this).data('id');
        const deleteUrl = `/admin/paths/${id}`; // Endpoint do usunięcia
        const confirmMessage = 'Czy na pewno chcesz usunąć tę ścieżkę?';

        // Potwierdzenie akcji użytkownika
        if (!confirm(confirmMessage)) {
            return;
        }

        // Pobierz token przed wysłaniem żądania DELETE
        // getToken().then(function(response) {
        //     const token = response.token; // Pobierz token z odpowiedzi serwera

            // Wykonaj żądanie DELETE
            $.ajax({
                url: deleteUrl,
                type: 'DELETE',
                // data: {
                //     _token: token // Dołącz token CSRF
                // },
                success: function(response) {
                    // Obsługa sukcesu (np. odświeżenie listy)
                    alert('Usunięto pomyślnie');
                    location.reload(); // Odśwież stronę
                },
                error: function(xhr) {
                    // Obsługa błędu
                    const errorMessage = xhr.responseJSON?.message || 'Wystąpił błąd podczas usuwania.';
                    alert(errorMessage);
                }
            });
        // }).catch(function() {
        //     // Obsługa błędu pobierania tokenu
        //     alert('Nie udało się pobrać tokenu. Spróbuj ponownie.');
        // });

    });

    $('#table').on('click', '.edit-btn', function() {
        let id = $(this).data('id');
        // console.log(id);
        const path = paths.find(path => path.id === id);
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
        pathPoints.forEach(pointId => {
            $('<input>').attr({
                type: 'hidden',
                name: 'points[]',
                value: pointId
            }).appendTo('#pathForm');
        });

        const form = $(this);
        const actionUrl = form.attr('action'); // Pobierz URL akcji
        const formData = form.serialize(); // Serializuj dane formularza

        $.ajax({
            url: actionUrl,
            method: form.attr('method'), // Pobierz metodę (POST lub inne)
            data: formData,
            success: function (response, status, xhr) {
                console.log("xhr status: " + xhr.status);
                console.log(response);
                console.log(status);
                if (xhr.status === 200 || xhr.status === 201) {
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
    };

    // zamknij okna popup znaczników przy kliknięciu poza mapą
    $(document).on('click', function(event){
        if(!map.getContainer().contains(event.target)) {
            map.closePopup();
        }

        if(!modalMap.getContainer().contains(event.target)) {
            modalMap.closePopup();
        }
    });
});

