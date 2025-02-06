/**
 * Definicje ikon używanych jako markery na mapie Leaflet.
 * Każda ikona ma określony kolor, rozmiar oraz cienie.
 */
const defaultIcon = L.icon({
    iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-blue.png',
    shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
    iconSize: [25, 41],
    iconAnchor: [12, 41],
    popupAnchor: [1, -34],
    shadowSize: [41, 41],
});

const greenIcon = L.icon({
    iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-green.png',
    shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
    iconSize: [25, 41],
    iconAnchor: [12, 41],
    popupAnchor: [1, -34],
    shadowSize: [41, 41],
});

const redIcon = L.icon({
    iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png',
    shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
    iconSize: [25, 41],
    iconAnchor: [12, 41],
    popupAnchor: [1, -34],
    shadowSize: [41, 41],
});

const purpleIcon = L.icon({
    iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-violet.png',
    shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
    iconSize: [25, 41],
    iconAnchor: [12, 41],
    popupAnchor: [1, -34],
    shadowSize: [41, 41],
});

const yellowIcon = L.icon({
    iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-gold.png',
    shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
    iconSize: [25, 41],
    iconAnchor: [12, 41],
    popupAnchor: [1, -34],
    shadowSize: [41, 41],
});

/**
 * Początkowe ustawienia mapy:
 * - Współrzędne: [54.52, 18.49] 
 * - Poziom przybliżenia: 13
 */
const initCoordinates = [54.52, 18.49];
const initZoom = 13;

/**
 * Inicjalizuje mapę Leaflet w wybranym elemencie DOM.
 * 
 * @param {string} mapId - ID elementu DOM, w którym ma być osadzona mapa.
 * @returns {Object} - Obiekt mapy Leaflet.
 */
function initMap(mapId) {
    map = L.map(mapId).setView(initCoordinates, initZoom);

    L.tileLayer('https://{s}.tile.opentopomap.org/{z}/{x}/{y}.png', {
        maxZoom: 17,
        attribution: '&copy; <a href="https://opentopomap.org">OpenTopoMap</a> contributors'
    }).addTo(map);

    return map;
}

/**
 * Resetuje widok mapy do początkowych współrzędnych i poziomu przybliżenia.
 * 
 * @param {Object} map - Obiekt mapy Leaflet.
 */
function resetMapView(map) {
    map.setView(initCoordinates, initZoom);
}

/**
 * Inicjalizuje grupę warstw dla markerów Leaflet.
 * 
 * @returns {Object} - Grupa warstw (`L.layerGroup`) dla markerów.
 */
function initMarkers() {
    return L.layerGroup();
}

/**
 * Inicjalizuje podgląd punktów na mapie Leaflet.
 * 
 * @param {Array} points - Tablica punktów zawierająca współrzędne i informacje o punktach.
 * @param {Object} markers - Grupa warstw dla markerów.
 * @param {Object} map - Obiekt mapy Leaflet.
 */
function initPointsPreview2(points, markers, map) {
    markers.clearLayers();
    points.forEach(function(point) {
        let epsg2180coords = [point.easting, point.northing];
        let wsg84coords = transformToWSG84(epsg2180coords);
        let m = L.marker(wsg84coords, {pointId: point.id});
        point.pointVirtual? m.setIcon(yellowIcon) : m.setIcon(defaultIcon);
        m.bindPopup(point.popup);
        markers.addLayer(m);
    });
    markers.addTo(map);

}

/**
 * Rysuje trasę na mapie Leaflet, łącząc punkty linii.
 * 
 * @param {Array} points - Tablica punktów trasy.
 * @param {Object} markers - Grupa warstw dla markerów i linii.
 * @param {Object} map - Obiekt mapy Leaflet.
 */
function drawPath(points, markers, map) {
    markers.clearLayers();

    let wsg84points = points.map( p => {
        let epsg2180coords = [p.easting, p.northing];
        let wsg84coords = transformToWSG84(epsg2180coords);
        return wsg84coords;
    });

    const polyline = L.polyline(wsg84points, { color: 'magenta' });
    markers.addLayer(polyline);
    markers.addTo(map);
    
}

/**
 * Podświetla marker na mapie Leaflet, zmieniając jego ikonę na większą.
 * Jeśli istnieje poprzedni marker, jego wygląd jest resetowany do normalnego rozmiaru.
 * 
 * @param {Object} point - Obiekt punktu, który ma zostać podświetlony.
 * @param {Object} oldMarker - Obiekt poprzednio podświetlonego markera.
 * @param {Array} markers - Tablica wszystkich markerów na mapie.
 * @param {Object} map - Obiekt mapy Leaflet.
 * @returns {Object} - Nowo podświetlony marker.
 */
function highlightMarker(point, oldMarker, markers, map) {
    const factor = 1.5;

    if(oldMarker) {
        const currentIcon = oldMarker.getIcon();
        const normalIcon = L.icon({
            iconUrl: currentIcon.options.iconUrl,
            shadowUrl: currentIcon.options.shadowUrl,
            iconSize: currentIcon.options.iconSize.map(size => size / factor),
            iconAnchor: currentIcon.options.iconAnchor.map(anchor => anchor / factor),
            popupAnchor: currentIcon.options.popupAnchor.map(anchor => anchor / factor),
            shadowSize: currentIcon.options.shadowSize.map(anchor => anchor / factor)
        });

        oldMarker.setIcon(normalIcon);
    }

    let highlightedMarker = markers.find(marker => marker.options.pointId === parseInt(point.id));

    const currentIcon = highlightedMarker.getIcon();

    const highlightedIcon = L.icon({
        iconUrl: currentIcon.options.iconUrl,
        shadowUrl: currentIcon.options.shadowUrl,
        iconSize: currentIcon.options.iconSize.map(size => size * factor),
        iconAnchor: currentIcon.options.iconAnchor.map(anchor => anchor * factor),
        popupAnchor: currentIcon.options.popupAnchor.map(anchor => anchor * factor),
        shadowSize: currentIcon.options.shadowSize.map(anchor => anchor * factor)
    });

    highlightedMarker.setIcon(highlightedIcon);

    return highlightedMarker;    
}

/**
 * Inicjalizuje podświetlanie markerów na mapie Leaflet.
 * Przechodzi przez wszystkie markery na mapie, znajduje powiązane punkty
 * i podświetla je za pomocą funkcji `highlightMarker`.
 * 
 * @param {Object} highlightedMarker - Ostatnio podświetlony marker (lub `null` na początek).
 * @param {Array} points - Tablica punktów, zawierająca informacje o punktach (np. `id`).
 * @param {Array} markers - Tablica markerów na mapie Leaflet.
 * @param {Object} map - Obiekt mapy Leaflet.
 */
function initHighlightMarker(highlightedMarker, points, markers, map) {
    markers.forEach(marker => {
        const pointId = marker.options.pointId;
        const point = points.find(p => p.id === pointId);

        highlightedMarker = highlightMarker(point, highlightedMarker, markers, map);
    })
}

/**
 * Aktualizuje mapę Leaflet, rysując trasę oraz dodając markery w odpowiednich lokalizacjach.
 * Dostosowuje wygląd markerów w zależności od ich pozycji na trasie oraz rodzaju trasy (pętla lub otwarta).
 *
 * @param {Array} points - Tablica punktów trasy, zawierająca współrzędne (`easting`, `northing`), pozycję, i dodatkowe informacje.
 * @param {Object} markers - Grupa warstw (`L.layerGroup`) dla markerów i linii.
 * @param {Object} map - Obiekt mapy Leaflet.
 */
function updateMap(points, markers, map) {
    // Wyczyść istniejące markery i warstwy
    markers.clearLayers();

    // Jeśli brak punktów, zakończ funkcję
    if (!points || points.length === 0) return;

    // Posortuj punkty, jeśli zawierają pole `position`
    if('position' in points[0]) {
        points.sort((p1, p2) => {
            return p1.position - p2.position;
        })
    }

    // Sprawdź, czy trasa jest pętlą
    const isLoop = points[0].id === points[points.length - 1].id;

    // Przekształć punkty na współrzędne WSG:84 i dodaj markery na mapę
    const wsg84points = points.map((point, index) => {
        const wsg84coords = transformToWSG84([point.easting, point.northing]);
        const marker = L.marker(wsg84coords);
        marker.bindPopup(`${point.code} - ${point.description}`);

        // Ustaw ikonę markera w zależności od typu trasy i pozycji punktu
        if (isLoop) {
            if (index === 0 || index === points.length - 1) {
                marker.setIcon(purpleIcon);
            } else {
                point.pointVirtual? marker.setIcon(yellowIcon) : marker.setIcon(defaultIcon);
            }
        } else {
            if (index === 0) {
                marker.setIcon(greenIcon); 
            } else if (index === points.length - 1) {
                marker.setIcon(redIcon); 
            } else {
                point.pointVirtual? marker.setIcon(yellowIcon) : marker.setIcon(defaultIcon);
            }
        }

        // Dodaj marker do grupy warstw
        markers.addLayer(marker); 
        return wsg84coords; 
    });

    // Rysuj linię (trasę) między punktami
    const polyline = L.polyline(wsg84points, { color: 'magenta' });
    markers.addLayer(polyline);
    // Dodaj grupę markerów (z markerami i trasą) do mapy
    markers.addTo(map);
}

/**
 * Resetuje ikonę markera do jego domyślnej ikony.
 * Jeśli marker jest oznaczony jako wirtualny (`pointVirtual`), ustawia żółtą ikonę.
 * W przeciwnym przypadku przywraca domyślną niebieską ikonę.
 *
 * @param {Object} marker - Obiekt markera Leaflet, który ma zostać zresetowany.
 */
function resetMarker(marker) {
    if (marker) {  
        marker.options.pointVirtual? marker.setIcon(yellowIcon) : marker.setIcon(defaultIcon);
    }
}

/**
 * Ustawia nowy punkt początkowy na mapie, aktualizując jego ikonę i resetując poprzedni punkt początkowy.
 * Jeśli nowy punkt początkowy jest również punktem końcowym, dostosowuje ikonę, aby odzwierciedlić obie role.
 *
 * @param {number} newStartPointId - ID nowego punktu początkowego.
 * @param {Array} markers - Tablica markerów na mapie.
 * @param {Object|null} startMarker - Obecny punkt początkowy (lub `null`, jeśli brak).
 * @param {Object|null} endMarker - Obecny punkt końcowy (lub `null`, jeśli brak).
 * @returns {Object|null} - Nowo ustawiony punkt początkowy jako obiekt markera (lub `null`, jeśli nie znaleziono).
 */
function setStartPoint(newStartPointId, markers, startMarker, endMarker) {
    // Resetowanie wyglądu poprzedniego punktu końcowego
    if (startMarker) {
        
        if (startMarker === endMarker) {
            updateMarkerColor(startMarker, false, false);
        } else {
            resetMarker(startMarker);
        }
    }

    startMarker = markers.find(marker => marker.options.pointId === parseInt(newStartPointId));

    // Aktualizacja wyglądu nowego punktu początkowego
    if (startMarker && endMarker && startMarker.options.pointId === endMarker.options.pointId) {
        updateMarkerColor(startMarker, false, true);
    } else if (startMarker) {
        updateMarkerColor(startMarker, true, false);
    }
    return startMarker
}

/**
 * Ustawia nowy punkt końcowy na mapie, aktualizując jego ikonę i resetując poprzedni punkt końcowy.
 * Jeśli nowy punkt końcowy jest również punktem początkowym, dostosowuje ikonę, aby odzwierciedlić obie role.
 *
 * @param {number} newEndPointId - ID nowego punktu końcowego.
 * @param {Array} markers - Tablica markerów na mapie.
 * @param {Object|null} startMarker - Obecny punkt początkowy (lub `null`, jeśli brak).
 * @param {Object|null} endMarker - Obecny punkt końcowy (lub `null`, jeśli brak).
 * @returns {Object|null} - Nowo ustawiony punkt końcowy jako obiekt markera (lub `null`, jeśli nie znaleziono).
 */
function setEndPoint(newEndPointId, markers, startMarker, endMarker) {
    // Resetowanie wyglądu poprzedniego punktu końcowego
    if (endMarker) {
        if (endMarker === startMarker) {
            updateMarkerColor(endMarker, true, false);
        } else {
            resetMarker(endMarker);
        }
    }

    endMarker = markers.find(marker => marker.options.pointId === parseInt(newEndPointId));

    // Aktualizacja wyglądu nowego punktu końcowego
    if (startMarker && endMarker && startMarker.options.pointId === endMarker.options.pointId) {
        updateMarkerColor(endMarker, false, true);
    } else if (endMarker) {
        updateMarkerColor(endMarker, false, false);
    }

    return endMarker;
}

/**
 * Zmienia ikonę markera w zależności od jego roli na mapie.
 *
 * @param {Object} marker - Marker Leaflet, którego ikona ma zostać zmieniona.
 * @param {boolean} isStart - Określa, czy marker reprezentuje punkt początkowy.
 * @param {boolean} isBoth - Określa, czy marker reprezentuje punkt jednocześnie początkowy i końcowy.
 */
function updateMarkerColor(marker, isStart, isBoth) {
    if (marker) {
        if (isBoth) {
            marker.setIcon(purpleIcon);
        } else if (isStart) {
            marker.setIcon(greenIcon);
        } else {
            marker.setIcon(redIcon);
        }
    }
}

/**
 * Resetuje wszystkie markery w grupie do ich domyślnych ikon.
 * Jeśli marker ma właściwość `pointVirtual`, ustawia żółtą ikonę.
 * W przeciwnym przypadku ustawia standardową niebieską ikonę.
 *
 * @param {Array} markers - Tablica markerów Leaflet, które mają zostać zresetowane.
 */
function resetMarkers(markers) {
    markers.forEach(marker => {
        if(marker) marker.options.pointVirtual? marker.setIcon(yellowIcon) : marker.setIcon(defaultIcon);
    });
}

// Eksport modułów na potrzeby testów
// module.exports = {initMap, initMarkers, initPointsPreview2};