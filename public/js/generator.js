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
    
    getPointsData()
        .then(function(result) {
            points = result.data;
            initPointsPreview(points, markers, map, "generator");
            populateDropdowns(points);
        })
        .catch(() => console.log("Error"));

    getTagsData()
        .then(function(result){
            tags = result.tags
            console.log(tags);

            tags.forEach(tag => {
                if(tag.points.length === 0) {
                    return;
                }

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

            
        }).catch((error) => console.log(error));

    getAreasData()
        .then(function(result){
            areas = result.data;
            
            const areasList = $('#areas-list');

            areas.forEach(area => {
                const checkbox = `
                    <div class="form-check d-inline-block me-3">
                        <input class="form-check-input" type="checkbox" id="area-${area.id}" value="${area.id}" checked>
                        <label class="form-check-label" for="area-${area.id}">${area.name}</label>
                    </div>
                `;
                areasList.append(checkbox);
            });
        })

    getMapUIData()
        .then(function(result) {
            maps = result;
            console.log(maps);
        }).catch((error) => console.log(error));

    $('#tag-btn').on('click', function () {
        $('#tag-modal').modal('show');
    });

    $('#generatorForm').on('submit', function(event) {
        event.preventDefault();

        const startPoint = $('#start-point').val();
        const endPoint = $('#end-point').val();
        const distanceRange = $('#distance').val();
        const pointsRange = $('#points').val();
        const selectedTags = [];
        const selectedAreas = [];
        const virtualPoints = $('#virtualpoints').is(':checked');

        // Pobranie zaznaczonych checkboxów
        $('#tag-list input:checked').each(function () {
            selectedTags.push(parseInt($(this).val()));
        });
        
        $('#areas-list input:checked').each(function () {
            selectedAreas.push(parseInt($(this).val()));
        });

        const firstAreaCheckbox = $('#areas-list input[type="checkbox"]').first()[0];

        if(selectedAreas.length < 1) {
            console.log("here");
            firstAreaCheckbox.setCustomValidity('Musisz zaznaczyć przynajmniej jeden obszar.');
            firstAreaCheckbox.reportValidity(); // Pokaż komunikat walidacji
            return; // Zatrzymaj dalsze przetwarzanie
        } else {
            firstAreaCheckbox.setCustomValidity(''); // Wyczyść komunikat walidacji, jeśli wszystko jest OK
        }
        

        getGeneratorData({startPoint, endPoint, distanceRange, pointsRange, selectedTags, selectedAreas, virtualPoints}, showLoading, hideLoading)
            .then(function(result) {
                paths = result.data;
                $('#form-wrapper').hide();
                $('#table-wrapper').show();

                const rows = paths.map(path => `
                    <tr class="" data-id="${path.id}" id="${path.id}">
                        
                        <td>${checkPathArea(path.points)}</td>
                        <td>${path.points.length}</td>
                        <td>${calculateRouteLength(path.points)}</td>
                        
                        <td><button data-id="${path.id}" class="btn btn-success btn-sm w-100 download-btn">Pobierz mapę</button></td>
                    </tr>
                `).join('');

                populateTable(rows, columnsConfig, columnDefsConfig, false, 'Nie udało się wygenerować trasy');
                updateMap(null, markers, map);
                
            }).catch((error) => {
                console.log(error)

                $('#form-wrapper').show();
            });

    });

    $('#re-generate-btn').click(function() {
        $('#generatorForm').submit();
    });

    $('#areas-list').on('change', 'input[type="checkbox"]', function () {
        console.log('zmiana checkbox');
        const firstAreaCheckbox = $('#areas-list input[type="checkbox"]').first()[0];
        
        // Jeśli przynajmniej jeden checkbox jest zaznaczony, wyczyść komunikat walidacji
        if ($('#areas-list input:checked').length > 0) {
            firstAreaCheckbox.setCustomValidity('');
        }
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

    // zamknij okna popup znaczników przy kliknięciu poza mapą
    $(document).on('click', function(event){
        if(!map.getContainer().contains(event.target)) {
            map.closePopup();
        }
    });

});

function showLoading() {
    $('#form-wrapper').hide();
    $('#table-wrapper').hide();
    $('#loading-spinner').show();
}

function hideLoading() {
    $('#loading-spinner').hide();
}