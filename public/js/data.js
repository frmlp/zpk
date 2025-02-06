/**
 * Pobiera dane o ścieżkach z API.
 *
 * @returns {Promise} - Obiekt `Promise` reprezentujący odpowiedź z API w formacie JSON.
 */
function getPathData() {
    return $.ajax({
        url: '/api/paths',
        type: 'GET',
        dataType: 'json',
    });
}


/**
 * Funkcja pobiera dane o punktach z API
 *
 * @returns {Promise} - Obiekt `Promise` reprezentujący odpowiedź z API w formacie JSON.
 */
function getPointsData() {
    return $.ajax({
        url: '/api/points',
        type: 'GET',
        dataType: 'json',
    })
}


/**
 * Funkcja pobiera dane o tagach z API
 *
 * @returns {Promise} - Obiekt `Promise` reprezentujący odpowiedź z API w formacie JSON.
 */
function getTagsData() {
    return $.ajax({
        url: '/api/tags',
        type: 'GET',
        dataType: 'json',
    })
}


/**
 * Funkcja pobiera dane o obszarach z API
 *
 * @returns {Promise} - Obiekt `Promise` reprezentujący odpowiedź z API w formacie JSON.
 */
function getAreasData() {
    return $.ajax({
        url: '/api/areas',
        type: 'GET',
        dataType: 'json',
    })
}

/**
 * Funkcja pobiera dane o ścieżkach z API admina
 *
 * @returns {Promise} - Obiekt `Promise` reprezentujący odpowiedź z API w formacie JSON.
 */
function getAdminPathData() {
    return $.ajax({
        url: '/admin/paths',
        type: 'GET',
        dataType: 'json',
    });
}

/**
 * Funkcja pobiera dane o punktach z API admina
 *
 * @returns {Promise} - Obiekt `Promise` reprezentujący odpowiedź z API w formacie JSON.
 */
function getAdminPointsData() {
    return $.ajax({
        url: '/admin/points',
        type: 'GET',
        dataType: 'json',
    })
}


/**
 * Funkcja pobiera dane o ścieżkach z API admina
 *
 * @returns {Promise} - Obiekt `Promise` reprezentujący odpowiedź z API w formacie JSON.
 */
function getAdminTagsData() {
    return $.ajax({
        url: '/admin/tags',
        type: 'GET',
        dataType: 'json',
    })
}

/**
 * Pobiera dane o ścieżkach z generatora tras na podstawie przekazanych parametrów.
 * Wywołuje funkcję `showLoading` przed wysłaniem żądania i `hideLoading` po jego zakończeniu, aby obsłużyć animację ładowania.
 *
 * @param {Object} params - Parametry wyszukiwania tras:
 *   - `startPoint`: ID punktu początkowego.
 *   - `endPoint`: ID punktu końcowego.
 *   - `distanceRange`: Zakres długości trasy.
 *   - `pointsRange`: Zakres liczby punktów.
 *   - `selectedTags`: Tablica wybranych tagów.
 *   - `selectedAreas`: Tablica wybranych obszarów.
 *   - `virtualPoints`: Flaga lub tablica określająca punkty wirtualne.
 * @param {Function} showLoading - Funkcja uruchamiana przed wysłaniem żądania (wyświetlenie spinnera ładowania).
 * @param {Function} hideLoading - Funkcja uruchamiana po zakończeniu żądania (ukrycie spinnera ładowania).
 * @returns {Promise} - Obiekt `Promise` reprezentujący odpowiedź z API w formacie JSON.
 */
function getGeneratorData({startPoint, endPoint, distanceRange, pointsRange, selectedTags, selectedAreas, virtualPoints}, showLoading, hideLoading ) {
    
    showLoading();

    return $.ajax({
        url: '/api/generator',
        type: 'GET',
        dataType:'json',
        data: {
            start_point_id: startPoint,
            end_point_id: endPoint,
            number_of_points_range: pointsRange,
            distance_range: distanceRange,
            tags: selectedTags,
            areas: selectedAreas,
            virtualpoints: virtualPoints
        }
       
    }).always(function () {

        hideLoading();
    });
}

/**
 * Konfiguruje globalne ustawienia AJAX, dodając nagłówek `X-CSRF-TOKEN`.
 * Token CSRF jest pobierany z meta tagu w dokumencie HTML.
 *
 * Zapewnia ochronę przed atakami CSRF (Cross-Site Request Forgery) w żądaniach AJAX.
 */
function csrfAjaxSetup() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
        }
    })
}

/**
 * Pobiera dane potrzebne do prawidłowego wyświetlenia formularza wyboru mapy podkładowej.
 *
 * @returns {Promise} - Obiekt `Promise` reprezentujący odpowiedź z API w formacie JSON.
 */
function getMapUIData() {
    return $.ajax({
        url: '/api/map/ui-data',
        type: 'GET',
        dataType: 'json',
    })
}
