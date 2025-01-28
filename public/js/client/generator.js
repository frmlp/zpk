/**
 * Inicjalizacja widoku, konfiguracja mapy, obsługa punktów, tras, tagów i obszarów,
 * oraz dynamiczną interakcję użytkownika z interfejsem.
 */
$(document).ready(function() {
    let points = [];
    let paths = [];
    let maps = [];
    let tags=[];
    let areas = [];
    const map = initMap("map");
    const markers = L.layerGroup();
    let startMarker = null;
    let endMarker = null;

    // Konfiguracja kolumn i definicji kolumn dla tabeli DataTable
    const columnsConfig = [
        { width: '30%' },
        { width: '18%' },
        { width: '18%' },
        null
    ];

    const columnDefsConfig = [
        { responsivePriority: 4, targets: 0 },
        { responsivePriority: 3, targets: 1 },
        { responsivePriority: 2, targets: 2 }, 
        { responsivePriority: 1, targets: 3 },
        { orderable: false, targets: 3}
    ];
    
    // Pobranie danych punktów z API
    getPointsData()
        .then(function(result) {
            points = result.data;
            // Konfiguracja popupów dla każdego punktu
            points.forEach(point => {
                point.popup = point.code.concat(" - ", point.description + 
                    `<div class="row justify-content-center">
                        <div class="col text-center">
                            <button type='button' value='${point.id}' class="btn btn-success btn-sm m-1 w-100 route-start-btn">Ustaw start trasy</button>
                            <button type='button' value='${point.id}' class="btn btn-success btn-sm m-1 w-100 route-finish-btn">Ustaw koniec trasy</button>
                        </div>
                    </div>`
            )})
            initPointsPreview2(points, markers, map);
            populateDropdowns(points);
        }).catch((xhr) => {
            const message = xhr.responseJSON?.message || 'Wystąpił błąd';
            alert(message);
        });

    // Pobranie danych tagów z API
    getTagsData()
        .then(function(result){
            tags = result.tags

            tags.forEach(tag => {
                if(tag.points.length === 0) {
                    return;
                }

                // Dodanie checkboxów dla każdego tagu
                const checkbox = `
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" value="${tag.id}" id="tag-${tag.id}">
                    <label class="form-check-label row" for="tag-${tag.id}">
                      <div class="col">${tag.name}</div><div class="col">- liczba punktów: ${tag.points.length}</div>
                    </label>
                  </div>
                `;
                $('#tag-list').append(checkbox);
            });

        }).catch((xhr) => {
            const message = xhr.responseJSON?.message || 'Wystąpił błąd';
            alert(message);
        });

    // Pobranie danych obszarów z API
    getAreasData()
        .then(function(result){
            areas = result.data;
            
            const areasList = $('#areas-list');

            areas.forEach(area => {
                // Dodanie checkboxów dla każdego obszaru
                const checkbox = `
                    <div class="form-check d-inline-block me-3">
                        <input class="form-check-input" type="checkbox" id="area-${area.id}" value="${area.id}" checked>
                        <label class="form-check-label" for="area-${area.id}">${area.name}</label>
                    </div>
                `;
                areasList.append(checkbox);
            });
        }).catch((xhr) => {
            const message = xhr.responseJSON?.message || 'Wystąpił błąd';
            alert(message);
        });

    // Pobranie danych map z API
    getMapUIData()
        .then(function(result) {
            maps = result;
        }).catch((xhr) => {
            const message = xhr.responseJSON?.message || 'Wystąpił błąd';
            alert(message);
        });

    // Wyświetlenie modala wyboru tagów
    $('#tag-btn').on('click', function () {
        $('#tag-modal').modal('show');
    });

    // Obsługa formularza generatora tras
    $('#generatorForm').on('submit', function(event) {

        event.preventDefault();

        const startPoint = $('#start-point').val();
        const endPoint = $('#end-point').val();
        const distanceRange = $('#distance').val();
        const pointsRange = $('#points').val();
        const selectedTags = [];
        const selectedAreas = [];
        const virtualPoints = $('#virtualpoints').is(':checked')? 1 : 0;

        // Pobranie zaznaczonych checkboxów
        $('#tag-list input:checked').each(function () {
            selectedTags.push(parseInt($(this).val()));
        });
        
        // Pobranie zaznaczonych obszarów
        $('#areas-list input:checked').each(function () {
            selectedAreas.push(parseInt($(this).val()));
        });

        const firstAreaCheckbox = $('#areas-list input[type="checkbox"]').first()[0];

        // Walidacja zaznaczenia przynajmniej jednego obszaru
        if(selectedAreas.length < 1) {
            firstAreaCheckbox.setCustomValidity('Musisz zaznaczyć przynajmniej jeden obszar.');
            firstAreaCheckbox.reportValidity();
            return;
        } else {
            firstAreaCheckbox.setCustomValidity('');
        }
        
        // Pobranie danych tras z generatora
        getGeneratorData({startPoint, endPoint, distanceRange, pointsRange, selectedTags, selectedAreas, virtualPoints}, showLoading, hideLoading)
            .then(function(result) {
                paths = result.data;
                $('#form-wrapper').hide();
                const rows = paths.map(path => `
                    <tr class="" data-id="${path.id}" id="${path.id}">
                        
                        <td>${getPathAreaNames(path.points)}</td>
                        <td>${path.points.length}</td>
                        <td>${calculateRouteLength(path.points)}</td>
                        
                        <td><button data-id="${path.id}" class="btn btn-success btn-sm w-100 download-btn">Pobierz mapę</button></td>
                    </tr>
                `).join('');

                updateMap(null, markers, map);

                if(paths.length < 1) {
                    $('#alertMessage').show();
                    return;
                }

                populateTable(rows, columnsConfig, columnDefsConfig, false);
                $('#table-wrapper').show();
                
            }).catch((xhr) => {
                $('#form-wrapper').show();

                const message = xhr.responseJSON?.message || 'Wystąpił błąd';
                alert(message);
            });
    });

    // Obsługa przycisku "Wygeneruj nowe trasy"
    $('.re-generate-btn').click(function() {
        $('#alertMessage').hide();
        $('#generatorForm').submit();
    });

    // obsługa zdarzenia zmiany stanu checkboxów Obszarów
    $('#areas-list').on('change', 'input[type="checkbox"]', function () {
        const firstAreaCheckbox = $('#areas-list input[type="checkbox"]').first()[0];
        
        // Jeśli przynajmniej jeden checkbox jest zaznaczony, wyczyść komunikat walidacji
        if ($('#areas-list input:checked').length > 0) {
            firstAreaCheckbox.setCustomValidity('');
        }
    });

    // Obsługuje kliknięcie przycisku "Zmień parametry".
    // Resetuje widoczność formularza i tabeli, przywraca podgląd punktów na mapie.
    $('.change-parameters-btn').on('click', function(event) {
        $('#alertMessage').hide();
        $('#table-wrapper').hide();
        $('#form-wrapper').show();
        initPointsPreview2(points, markers, map);
        $('#start-point').change();
        $('#end-point').change();
    });

    // Obsługuje kliknięcie w wiersz tabeli z trasami.
    // Podświetla wybrany wiersz oraz rysuje trasę na mapie na podstawie wybranej trasy.
    $('#table tbody').on('click', 'tr', function() {
        let id = $(this).data('id');
        highlightTableRow(id);
        let points = paths.find(r => r.id == id).points;
        updateMap(points, markers, map);

    });


    // Obsługuje zmianę punktu początkowego w dropdownie.
    // Ustawia nowy punkt początkowy na mapie.
    $('#start-point').on('change', function () {
        const selectedPointId = $(this).val();
        startMarker = setStartPoint(selectedPointId, markers.getLayers(), startMarker, endMarker);
    });
    
    // Obsługuje zmianę punktu końcowego w dropdownie.
    // Ustawia nowy punkt końcowy na mapie.
    $('#end-point').on('change', function () {
        const selectedPointId = $(this).val();
        endMarker = setEndPoint(selectedPointId, markers.getLayers(), startMarker, endMarker);
    });
    
    // Obsługuje kliknięcie przycisku "Ustaw start trasy" w popupie markera.
    // Ustawia wartość dropdowna punktu początkowego i wywołuje jego zmianę.
    $(document).on('click', '.route-start-btn', function () {
        const pointId = $(this).val();
        $('#start-point').val(pointId).change();
    });
    
    // Obsługuje kliknięcie przycisku "Ustaw koniec trasy" w popupie markera.
    // Ustawia wartość dropdowna punktu końcowego i wywołuje jego zmianę.
    $(document).on('click', '.route-finish-btn', function () {
        const pointId = $(this).val();
        $('#end-point').val(pointId).change();
    });

    // Obsługuje kliknięcie przycisku "Pobierz mapę" w tabeli.
    // Wyświetla modal z listą map do wyboru dla pobrania.
    $(document).on('click', '.download-btn', function() {

        const pathId = $(this).data('id'); // Pobranie ID mapy z przycisku

        $('#mapList').html(prepareHtmlForMapChoiceModal(maps, pathId)); // Wstawienie wygenerowanej listy do modala

        $('#mapModal').modal('show'); // Wyświetlenie modala
    });

    // Obsługuje kliknięcie przycisku "Pobierz" w modalu wyboru mapy.
    // Weryfikuje wybór mapy i wysyła żądanie pobrania pliku z wybraną trasą.
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

    // zamknij okna popup znaczników przy kliknięciu poza mapą
    $(document).on('click', function(event){
        if(!map.getContainer().contains(event.target)) {
            map.closePopup();
        }
    });

});

// Funkcja wyświetla spinner ładowania i ukrywa elementy formularza, tabeli i komunikaty.
function showLoading() {
    $('#form-wrapper').hide();
    $('#table-wrapper').hide();
    $('#alertMessage').hide();
    $('#loading-spinner').show();
}

// Funkcja ukrywa spinner ładowania.
function hideLoading() {
    $('#loading-spinner').hide();
}