/**
 * Funkcja definiuje układy współrzędnych dla biblioteki Proj4.
 * 
 * Układy współrzędnych:
 * - `'WGS:84'`: Globalny układ współrzędnych WGS 84 (długość i szerokość geograficzna).
 *   - Parametry: 
 *     - `+proj=longlat`: Układ współrzędnych geograficznych.
 *     - `+datum=WGS84`: System odniesienia WGS 84.
 * - `'EPSG:2180'`: Lokalny układ współrzędnych PL-1992, używany w Polsce.
 *   - Parametry:
 *     - `+proj=tmerc`: Projekcja poprzeczna Merkatora (Transverse Mercator).
 *     - `+lat_0=0`: Szerokość geograficzna początkowa (0°).
 *     - `+lon_0=19`: Długość geograficzna centralna (19°E).
 *     - `+k=0.9993`: Współczynnik skali.
 *     - `+x_0=500000`: Fałszywe przesunięcie X.
 *     - `+y_0=-5300000`: Fałszywe przesunięcie Y.
 *     - `+datum=GRS80`: System odniesienia geodezyjnego GRS80.
 *     - `+units=m`: Jednostki w metrach.
 *     - `+no_defs`: Wyłącza domyślne definicje.
 */
proj4.defs([
    [
        'WSG:84',
        '+title=WGS 84 (long/lat) +proj=longlat +datum=WGS84 +no_defs'
    ],
    [
        'EPSG:2180',
        '+proj=tmerc +lat_0=0 +lon_0=19 +k=0.9993 +x_0=500000 +y_0=-5300000 +datum=GRS80 +units=m +no_defs'
    ]
]);

/**
 * Transformuje współrzędne z układu EPSG:2180 (PL-1992) do układu WGS:84.
 * 
 * Dodatkowo zamienia kolejność współrzędnych (x -> y, y -> x), aby odpowiadały układowi WGS:84, 
 * który wymaga kolejności [szerokość geograficzna, długość geograficzna] (stosowane w bibliotece Leaflet).
 *
 * @param {Array} epsg2180coords - Tablica współrzędnych w układzie EPSG:2180 ([easting, northing]).
 * @returns {Array} - Tablica współrzędnych w układzie WGS:84 ([latitude, longitude]).
 */
function transformToWSG84(epsg2180coords) {
    let coords = proj4('EPSG:2180', 'WSG:84', epsg2180coords);
    var temp=coords[0];
    coords[0]=coords[1];
    coords[1]=temp;
    return coords;
}

/**
 * Oblicza całkowitą długość trasy na podstawie listy punktów.
 * Współrzędne punktów są konwertowane na kilometry, a odległości między kolejnymi punktami
 * są sumowane przy użyciu wzoru na odległość euklidesową.
 *
 * @param {Array} points - Tablica punktów trasy. 
 * @returns {number} - Całkowita długość trasy w kilometrach, zaokrąglona do dwóch miejsc po przecinku.
 */
function calculateRouteLength(points) {
    // Sortowanie punktów według ich pozycji na trasie
    points.sort((p1, p2) => {
        return p1.position - p2.position;
    });

    let totalLength = 0.0;

    for(let i = 1; i < points.length; i++) {
        // Zamiana jednostki na kilometry
        let x_start = parseFloat(points[i-1].easting)/1000;
        let y_start = parseFloat(points[i-1].northing)/1000;
        let x_end = parseFloat(points[i].easting)/1000;
        let y_end = parseFloat(points[i].northing)/1000;

        x_delta_sq = Math.pow(x_start - x_end, 2);
        y_delta_sq = Math.pow(y_start - y_end, 2);

        totalLength += Math.sqrt(x_delta_sq + y_delta_sq);
    }

    return Number(totalLength.toFixed(2));
}

/**
 * Sprawdza typ trasy na podstawie listy punktów.
 * Jeśli pierwszy i ostatni punkt mają ten sam identyfikator (`id`), trasa jest określana jako "Pętla".
 * W przeciwnym wypadku trasa jest określana jako "Otwarta".
 *
 * @param {Array} points - Tablica punktów trasy
 * @returns {string} - Typ trasy: `"Pętla"` lub `"Otwarta"`.
 */
function checkRouteType(points) {
    let firstPoint;
    let lastPoint;

    // Sortowanie punktów według ich pozycji na trasie
    if('position' in points[0]) {
        points.sort((p1, p2) => {
            return p1.position - p2.position;
        });
    }

    firstPoint = points[0].id;
    lastPoint = points[points.length - 1].id;

    if(firstPoint === lastPoint) {
        return "Pętla";
    } else {
        return "Otwarta";
    }
}

/**
 * Wypełnia elementy dropdown (listy rozwijane) danymi punktów.
 * Dla każdego elementu z klasą `dropdown-points` generuje opcje na podstawie przekazanej listy punktów.
 *
 * @param {Array} points - Tablica punktów.
 */
function populateDropdowns(points) {
    let dropdownList = `<option selected disabled>Wybierz punkt</option>`;
    points.forEach(function(point){
        dropdownList += `<option value="${point.id}">${point.code} - ${point.description}</option>`;
    })
    $('.dropdown-points').each(function() {
        $(this).html(dropdownList);
    })
}

/**
 * Tworzy element dropdown w formie grupy wejściowej HTML na podstawie listy punktów.
 * Zwraca gotowy fragment HTML zawierający dropdown i przyciski pomocnicze.
 *
 * @param {Array} points - Tablica punktów. 
 * @returns {string} - Fragment HTML zawierający strukturę dropdowna z dodatkowymi elementami.
 */
function createDropdown(points) {
    // Generowanie opcji dla dropdowna
    let optionList = `<option selected disabled>Wybierz punkt</option>`;
    points.forEach(function(point) {
        optionList += `<option value="${point.id}">${point.code} - ${point.description}</option>`
    })
    // Tworzenie struktury HTML dla dropdowna z dodatkowymi elementami
    return  `<div class="input-group dropdown-group">
                <span class='input-group-text handle' role='button' style="display:none;"><i class="bi bi-chevron-bar-expand"></i></span>
                <select class="form-select dropdown">
                    ${optionList}
                </select>
                <button class="btn btn-danger remove-btn" type="button" style="display:none;">Usuń</button>
            </div>`;
}

/**
 * Tworzy element dropdown dla tagów w formie grupy wejściowej HTML.
 * Zwraca gotowy fragment HTML zawierający dropdown z tagami i przycisk "Usuń".
 *
 * @param {Array} tags - Tablica tagów.
 * @returns {string} - Fragment HTML zawierający dropdown z tagami i przyciskiem "Usuń".
 */
function createTagDropdown(tags) {
    // Generowanie opcji dla dropdowna
    let optionList = `<option selected disabled>Wybierz tag</option>`;
    tags.forEach(function(tag) {
        optionList += `<option value="${tag.id}">${tag.name}</option>`
    })
    // Tworzenie struktury HTML dla dropdowna z dodatkowymi elementami
    return  `<div class="input-group dropdown-group">
                <select class="form-select dropdown">
                    ${optionList}
                </select>
                <button class="btn btn-danger tag-remove-btn" type="button" style="display:none;">Usuń</button>
            </div>`;
}

/**
 * Resetuje dropdowny w kontenerze o ID `dropdown-container`.
 * Usuwa istniejącą zawartość i tworzy nowy dropdown na podstawie przekazanych punktów.
 *
 * @param {Array} points - Tablica punktów.
 */
function resetDropdowns(points) {
    $('#dropdown-container').html(createDropdown(points));
}

/**
 * Resetuje dropdowny z tagami w kontenerze o ID `dropdown-container`.
 * Usuwa istniejącą zawartość i tworzy nowy dropdown na podstawie przekazanych tagów.
 *
 * @param {Array} tags - Tablica tagów.
 */
function resetTagDropdowns(tags) {
    $('#dropdown-container').html(createTagDropdown(tags));
}

/**
 * Zbiera punkty na podstawie wartości wybranych w elementach określonych przez `markup`.
 * Wyszukuje obiekt punktu w tablicy `points` na podstawie `id` i zwraca tablicę znalezionych punktów.
 *
 * @param {string} markup - Selektor lub elementy HTML, z których mają zostać pobrane wartości punktów.
 * @param {Array} points - Tablica punktów. 
 * @returns {Array} - Tablica znalezionych punktów na podstawie wybranych wartości.
 */
function collectPoints(markup, points){
    return $(markup).map(function() {
        let id = $(this).val();

        let point = points.find(p => p.id == id);
        if(point){
            return {...point};
        }
        return null;
        
    }).get();
}

/**
 * Filtruje trasy, pozostawiając tylko te, które zawierają punkty.
 * Sprawdza, czy pole `points` w obiekcie trasy jest tablicą i czy zawiera co najmniej dwa elementy.
 *
 * @param {Array} paths - Tablica tras.
 * @returns {Array} - Tablica tras, które zawierają co najmniej dwa punkty.
 */
function filterPathsWithPoints(paths) {
    return paths.filter(route => Array.isArray(route.points) && route.points.length > 1);
}

/**
 * Zwraca nazwy obszarów, posortowane alfabetycznie i połączone w jeden ciąg znaków.
 * Usuwa duplikaty nazw.
 *
 * @param {Array} areas - Tablica obiektów obszarów.
 * @returns {string} - Posortowany alfabetycznie ciąg unikalnych nazw obszarów, oddzielonych spacjami.
 */
function getAreaNames(areas) {
    const areasSet = new Set();

    areas.forEach(area => {
        areasSet.add(area.name);
    });

    return Array.from(areasSet).sort().join(" ");
}

/**
 * Zwraca unikalne nazwy obszarów dla podanych punktów trasy.
 * Nazwy są sortowane alfabetycznie i połączone w jeden ciąg znaków.
 *
 * @param {Array} points - Tablica punktów trasy.
 * @returns {string} - Posortowany alfabetycznie ciąg unikalnych nazw obszarów, oddzielonych spacjami.
 */
function getPathAreaNames(points) {
    const areasSet = new Set();

    points.forEach(point => {
        point.areas.forEach(area => {
            areasSet.add(area.name);
        });
    });

    return Array.from(areasSet).sort().join(" ");
}

/**
 * Generuje kod HTML dla modala wyboru mapy.
 *
 * @param {Array} maps - Tablica obiektów map.
 * @param {number} pathId - ID ścieżki, do której odnosi się modal.
 * @returns {string} - Kod HTML zawierający strukturę modala z listą map.
 */
function prepareHtmlForMapChoiceModal(maps, pathId) {
    // Wygenerowanie listy map w modalu
    let listHtml = `<div id="modalContainer" class="row gx-3 gy-3 justify-content-center" data-id="${pathId}">`;

    maps.forEach((map) => {
        listHtml += `
            <div class="col-12 col-lg-6">
                <input type="radio" class="btn-check" name="mapRadio" id="map-${map.id}" data-id="${map.id}" autocomplete="off">
                <label class="btn btn-outline-success d-flex align-items-center w-100" for="map-${map.id}">
                    <img src="${map.icon_path}" alt="${map.name}" class="icon-img">
                    <span class="m-2 text-center flex-grow-1">${map.name}<span>
                </label>
            </div>
        `;
    });

    listHtml += '</div>';

    return listHtml;
}

//eksport modułów na potrzeby testów
// module.exports = {transformToWSG84, getAreaNames};