$(document).ready(function() {
    let points = [];
    let tags = [];
    const map = initMap('map');
    let markers = initMarkers();
    let highlightedMarker = null;

    const columnsConfig = [
        { width: '10%' },
        { width: '25%' },
        { width: '10%' },
        { width: '10%' },
        { width: '10%' }, 
        null,
        null
    ];

    const columnDefsConfig =[
        { responsivePriority: 1, targets: 0 },
        { responsivePriority: 5, targets: 1 },
        { responsivePriority: 6, targets: 2 },
        { responsivePriority: 2, targets: 3 },
        { responsivePriority: 2, targets: 4 },
        { responsivePriority: 1, targets: 5 },
        { responsivePriority: 1, targets: 6 },
        { orderable: false, targets: [5, 6]}
    ];

    csrfAjaxSetup();

    getAdminPointsData()
        .then(function(result) {
            points = result.data;

            console.log(points);

            const rows = points.map(point => `
                <tr class="" data-id="${point.id}" id="${point.id}">
                    <td>${point.code}</td>
                    <td>${point.description}</td>
                    <td>${getAreaNames(point.areas)}</td>
                    <td>${point.easting}</td>
                    <td>${point.northing}</td>
                    <td><button data-id="${point.id}" class="btn btn-warning btn-sm w-100 edit-btn">Edytuj</button></td>
                    <td><button data-id="${point.id}" class="btn btn-danger btn-sm w-100 delete-btn">Usuń</button></td>
                </tr>
            `).join('');

            populateTable(rows, columnsConfig, columnDefsConfig);

            points.forEach(point => {
                point.popup = point.code.concat(" - ", point.description + 
                    `<div class="row justify-content-center">
                        <div class="col text-center">
                            <button type='button' data-id='${point.id}' class="btn btn-warning btn-sm m-1 w-100 edit-btn">Edytuj</button>
                            <button type='button' data-id='${point.id}' class="btn btn-danger btn-sm m-1 w-100 delete-btn">Usuń</button>
                        </div>
                    </div>`
            )});

            initPointsPreview2(points, markers, map);

            // initHighlightMarker(highlightedMarker, points, markers.getLayers(), map);
            markers.getLayers().forEach(marker => {
                marker.on('click', function() {
                    const pointId = marker.options.pointId;
                    const point = points.find(p => p.id === pointId);
            
                    // Powiększ marker po kliknięciu
                    highlightedMarker = highlightMarker(point, highlightedMarker, markers.getLayers(), map);
                    highlightTableRow(point.id);
                });
            });
            

        }).catch((xhr) => {
            const message = xhr.responseJSON?.message || 'Wystąpił błąd';
            alert(message);
        });
    
    getAdminTagsData()
        .then(function(result){
            tags = result.tags;
            console.log('getAdminTagsData()');
            console.log(tags);
        }).catch((xhr) => {
            const message = xhr.responseJSON?.message || 'Wystąpił błąd';
            alert(message);
        });
    
    $('#table tbody').on('click', 'tr', function() {
        let id = $(this).data('id');
        highlightTableRow(id);

        let point = points.find(p => p.id == id);
        highlightedMarker = highlightMarker(point, highlightedMarker, markers.getLayers(), map);
    });

    $('#newPointBtn').on('click', function() {
        $('#alertMessage').hide();
        $('#pointModalLabel').text('Nowa trasa');
        $('#pointForm').attr('action', '/admin/points');
        $('#pointForm').attr('method', 'POST'); // Metoda POST do tworzenia nowego punktu
        $('#pointForm').find('input[name="_method"]').remove(); 
        $('#pointForm').attr('mode', 'new');

        $('#pointForm')[0].reset(); // Wyczyść formularz
        $('#coordinateEPSG2180').prop('checked', true);
        $('input[name="coordinateType"]').trigger('change');
        resetTagDropdowns(tags);

        $('#pointModal').modal('show');
    })

    $(document).on('click', '.edit-btn', function() {
        $('#alertMessage').hide();

        let id = $(this).data('id');

        const point = points.find(point => point.id === id);
        console.log(point);
        $('#pointForm').attr('action', '/admin/points/' + point.id);
        $('#pointForm').attr('method', 'POST'); // Ustawienie metody POST, a Laravel obsłuży PUT przez ukryte pole _method
        $('#pointForm').append('<input type="hidden" name="_method" value="PUT">');
        $('#pointForm').attr('mode', 'edit');
        // wypełnienie formularza danymi

        $('#pointModalLabel').text('Edytuj Punkt');
        $('#pointCode').val(point.code);
        $('#pointVirtual').prop('checked', point.pointVirtual === 1);
        $('#pointDescription').val(point.description);
        $('#coordinateEPSG2180').prop('checked', true);
        $('input[name="coordinateType"]').trigger('change');
        $('#pointEasting').val(point.easting);
        $('#pointNorthing').val(point.northing);
        $('#pointNorthing').trigger('input');

        $('#areaId').val(point.area_id);
        $('#pointUrl').val(point.url);

        resetTagDropdowns(tags);
        point.tags.forEach(tag => {
            const lastDropdown = $('.form-select').last();
    
            if (lastDropdown.length) {
                lastDropdown.val(tag.id).trigger('change');
                lastDropdown.closest('.dropdown-group').find('.tag-remove-btn').show();
            }
        })

        $('#pointModal').modal('show');
    });

    $(document).on('change', '.dropdown', function() {
        var parentGroup = $(this).closest('.dropdown-group');
        parentGroup.find('.tag-remove-btn').show();
        // parentGroup.find('.handle').show();
    
        if ($('#dropdown-container .dropdown-group').last().find('select').val()) {
            $('#dropdown-container').append(createTagDropdown(tags));
        }

    });

    $(document).on('click', '.tag-remove-btn', function() {
        $(this).closest('.dropdown-group').remove();
    });

    $(document).on('click', '.delete-btn', function(event) {
        event.preventDefault();

        let id = $(this).data('id');
        const deleteUrl = `/admin/points/${id}`; // Endpoint do usunięcia
        const confirmMessage = 'Czy na pewno chcesz usunąć tę punkt kontrolny?';

        // Potwierdzenie akcji użytkownika
        if (!confirm(confirmMessage)) {
            return;
        }

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
                const message = xhr.responseJSON?.message || 'Wystąpił błąd podczas usuwania.';
                alert(message);
            }
        });

    })


    $('#pointForm').on('submit', function(event) {
        event.preventDefault();
        const method = $('#pointForm').attr('mode') === 'edit'? 'PUT' : 'POST';

        let pathTags = collectPoints('#dropdown-container select', tags).map(tag => tag.id);

        const data = {
            code: $('#pointCode').val(),
            description: $('#pointDescription').val(),
            easting: parseFloat($('#pointEasting').val()),
            northing: parseFloat($('#pointNorthing').val()),
            pointVirtual: $('#pointVirtual').is(':checked') ? 1 : 0, // Checkbox na liczbę
            url: $('#pointUrl').val(),
            // area_id: parseInt($('#areaId').val(), 10), // Select na liczbę
            tag_ids: pathTags,// Konwersja string na tablicę

        };


        $.ajax({
            url: $(this).attr('action'),
            method: method, // Pobierz metodę (POST lub inne)
            contentType: 'application/json',
            data: JSON.stringify(data),
            success: function (response, status, xhr) {
                // console.log("xhr status: " + xhr.status);
                // console.log(response);
                // console.log(status);
                // if (xhr.status === 200 || xhr.status === 201) {
                    location.reload(); // Odśwież stronę
                // } else {
                //     $('#error-message').text('Wystąpił nieoczekiwany błąd. Spróbuj ponownie.');
                // }
            },
            error: function (xhr) {
                // Wyświetl wiadomość o błędzie w zależności od odpowiedzi serwera
                const message = xhr.responseJSON?.message || 'Wystąpił błąd. Spróbuj ponownie.';
                $('#alertMessage').text(message).show();
            }
        });



    });

    $('input[name="coordinateType"]').on('change', function() {
        if ($('#coordinateWSG84').is(':checked')) {
        $('#wsg84Fields').show();
        $('#epsg2180Fields').hide();
    } else if ($('#coordinateEPSG2180').is(':checked')) {
        $('#wsg84Fields').hide();
        $('#epsg2180Fields').show();
    }
    });

    $('#pointEasting').on('input', function() {
        inputEPSG2180Field();
    });

    $('#pointNorthing').on('input', function() {
        inputEPSG2180Field();
    });

    $('#pointLatitude').on('input', function() {
        inputWSG84Field();
    });

    $('#pointLongitude').on('input', function() {
        inputWSG84Field();
    });

    function inputEPSG2180Field() {
        let easting = Number($('#pointEasting').val());
        let northing = Number($('#pointNorthing').val());
        let wsg84coords = proj4('EPSG:2180', 'WSG:84', [easting, northing]);
        console.log(wsg84coords);

        $('#pointLongitude').val(Number(wsg84coords[0].toFixed(5)));
        $('#pointLatitude').val(Number(wsg84coords[1].toFixed(5)));
        
    };

    function inputWSG84Field() {
        let longitude = Number($('#pointLongitude').val());
        let latitude = Number($('#pointLatitude').val());
        let epsg2180coords = proj4('WSG:84', 'EPSG:2180', [longitude, latitude]);
        console.log(epsg2180coords);
        $('#pointEasting').val(Number(epsg2180coords[0].toFixed(2)));
        $('#pointNorthing').val(Number(epsg2180coords[1].toFixed(2)));
    };

    // zamknij okna popup znaczników przy kliknięciu poza mapą
    $(document).on('click', function(event){
        if(!map.getContainer().contains(event.target)) {
            map.closePopup();
        }
    });


})
