/**
 * Funkcja inicjalizuje widok, konfigurując mapę, markery, listy punktów, ścieżek oraz zdarzenia użytkownika.
 */
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

    // Konfiguracja kolumn i definicji kolumn dla tabeli DataTable
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
    
    // Konfiguracja CSRF tokenów dla zapytań AJAX nieobsługiwanych jako formularz
    csrfAjaxSetup();

    // Pobranie danych ścieżek i ich wyświetlenie w tabeli.
    getAdminPathData()
        .then(function(result) {
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
    }).catch((xhr) => {
        const message = xhr.responseJSON?.message || 'Wystąpił błąd';
        alert(message);
    });

    // Pobranie danych punktów i przygotowanie ich do użycia w widoku.
    getAdminPointsData()
        .then(function(result) {
            points = result.data;
            points.forEach(point => {
                point.popup = point.code.concat(" - ", point.description) +
                    `<button type='button' id='add-point-btn' value='${point.id}' class='btn btn-success btn-sm m-1 w-100'>Dodaj do trasy</button>`
            });

            // Inicjalizacja sortowania dla dropdownów modala
            initializeSortable();

        }).catch((xhr) => {
            const message = xhr.responseJSON?.message || 'Wystąpił błąd';
            alert(message);
        });
    
    // Obsługa kliknięcia w wiersz tabeli.
    // Wyświetla trasę na mapie na podstawie wybranego wiersza.
    $('#table tbody').on('click', 'tr', function() {
        let id = $(this).data('id');
        highlightTableRow(id);

        let points = paths.find(p => p.id == id).points;
        updateMap(points, markers, map);
    });

    // Obsługa przycisku "Nowa trasa".
    $('#newPathBtn').on('click', function() {
        $('#alertMessage').hide();
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

        updatePath();
        resetMarkers([startMarker, endMarker]);
        startMarker = null;
        endMarker = null;
        
        
        initPointsPreview2(points, modalMarkers, modalMap);

        
    });

    // Obsługa zmiany wartości w dropdownach w kontenerze.
    $(document).on('change', '.dropdown', function() {
        var parentGroup = $(this).closest('.dropdown-group');
        parentGroup.find('.remove-btn, .handle').show();
    
        if ($('#dropdown-container .dropdown-group').last().find('select').val()) {
            $('#dropdown-container').append(createDropdown(points));
        }

        updatePath();
    });

    // Obsługa przycisku "Usuń" w dropdownach
    $(document).on('click', '.remove-btn', function() {
        $(this).closest('.dropdown-group').remove();
        updatePath();
    });

    // Obsługa przycisku "Dodaj do trasy" w popupie punktu
    $(document).on('click', '#add-point-btn', function () {
        const buttonValue = $(this).val();
        const lastDropdown = $('.form-select').last();
    
        if (lastDropdown.length) {
            lastDropdown.val(buttonValue).trigger('change');
            lastDropdown.closest('.dropdown-group').find('.remove-btn').show();
        }
    });

    // Obsługa przycisku "Usuń" w tabeli.
    $('#table').on('click', '.delete-btn', function(event) {
        
        event.preventDefault();

        let id = $(this).data('id');
        const deleteUrl = `/admin/paths/${id}`;
        const confirmMessage = 'Czy na pewno chcesz usunąć tę ścieżkę?';

        // Potwierdzenie akcji użytkownika
        if (!confirm(confirmMessage)) {
            return;
        }

 
        $.ajax({
            url: deleteUrl,
            type: 'DELETE',

            success: function(response) {
                alert('Usunięto pomyślnie');
                location.reload();
            },
            error: function(xhr) {
                const errorMessage = xhr.responseJSON?.message || 'Wystąpił błąd podczas usuwania.';
                alert(errorMessage);
            }
        });

    });

    // Obsługa przycisku "Edytuj".
    $('#table').on('click', '.edit-btn', function() {
        $('#alertMessage').hide();

        let id = $(this).data('id');
        const path = paths.find(path => path.id === id);

        $('#pathForm').attr('action', '/admin/paths/' + path.id);
        $('#pathForm').attr('method', 'POST'); 
        $('#pathForm').append('<input type="hidden" name="_method" value="PUT">');


        $('#pathModalLabel').text('Edytuj Trasę');
        $('#pathName').val(path.name);

        $('#pathModal').modal('show');

        setTimeout(() => {
            modalMap.invalidateSize();
        }, 250);

        resetDropdowns(points);
        resetMapView(modalMap);
        resetMarkers([startMarker, endMarker]);
        startMarker = null;
        endMarker = null;
        initPointsPreview2(points, modalMarkers, modalMap);

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

    // Obsługuje zapis trasy z modalowego formularza.
    $('#pathForm').on('submit', function(event) {
        event.preventDefault();
        let pathPoints = collectPoints('#dropdown-container select', points).map(point => point.id);

        if(pathPoints.length < 2) {
            const message = 'Trasa musi się składać z co najmniej dwóch punktów.';
            $('#alertMessage').text(message).show();
            return;
        }

        // Usuń wcześniej dodane pole 'points', jeśli istnieje
        $('#pathForm input[name="points"]').remove();

        // Dodaj ukryte pole z tablicą 'points'
        pathPoints.forEach(pointId => {
            $('<input>').attr({
                type: 'hidden',
                class: 'pointArray',
                name: 'points[]',
                value: pointId
            }).appendTo('#pathForm');
        });

        const form = $(this);
        const actionUrl = form.attr('action'); 
        const formData = form.serialize();

        $.ajax({
            url: actionUrl,
            method: form.attr('method'),
            data: formData,
            success: function (response, status, xhr) {
                location.reload();
                
            },
            error: function (xhr) {
                // Wyświetl wiadomość o błędzie w zależności od odpowiedzi serwera
                const message = xhr.responseJSON?.message || 'Wystąpił błąd. Spróbuj ponownie.';
                $('#alertMessage').text(message).show();
                $('#pathForm .pointArray').remove(); // wyczyść poprzednie wpisy formularza
            }
        });
    })
    
    // Funkcja aktualizuje trasę wyświetlaną na mapie modalnej na podstawie punktów wybranych w dropdownach.
    // Ustawia markery startowy i końcowy na mapie modalnej.
    // Rysuje trasę łączącą punkty.
    function updatePath() {
        let pathPoints = collectPoints('#dropdown-container select', points);
        if(pathPoints.length > 0) {
            let startPointId = pathPoints[0].id;
            let endPointId = pathPoints[pathPoints.length - 1].id;
    
            startMarker = setStartPoint(startPointId, modalMarkers.getLayers(), startMarker, endMarker);
            endMarker = setEndPoint(endPointId, modalMarkers.getLayers(), startMarker, endMarker);
        }
    
        drawPath(pathPoints, modalPathMarkers, modalMap);
    };

    // Funkcja inicjalizuje możliwość sortowania elementów w kontenerze `#dropdown-container`.
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

