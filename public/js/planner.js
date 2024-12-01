var jsonResponse;
var map;
var mapMarkers;
var mapRoute;
var yellowIcon = new L.Icon({
    iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-gold.png',
    shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
    iconSize: [20, 33],
    iconAnchor: [10, 33],
    popupAnchor: [1, -27],
    shadowSize: [33, 33]
})
// Definicje układów współrzędnych
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

var startMarker = null;
var endMarker = null;
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

document.addEventListener('DOMContentLoaded', function() {
    $.ajax({
        url: 'http://localhost:8000/api/points',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            jsonResponse = response;
            console.log(jsonResponse);
            resetDropdowns(jsonResponse.data);
            initializeSortable();
            initMap(jsonResponse.data);
        },
        error: function(error) {
            console.error("Error: ", error);
        }
    })
})

$(document).on('click', '.remove-btn', function() {
    $(this).closest('.dropdown-group').remove();
    updateRoute();
})

$('#reset-btn').on('click', function() {
    resetDropdowns(jsonResponse.data);
    updateRoute();
})

$('#finish-btn').on('click', function() {
    downloadMap();
})

$(document).on('change', '.dropdown', function() {
    var parentGroup = $(this).closest('.dropdown-group');
    parentGroup.find('.remove-btn').show();
    parentGroup.find('.handle').show();

    if($('#dropdown-container .dropdown-group:last-child select').val() !== null) {
        $('#dropdown-container').append(createDropdown(jsonResponse.data));
    }

    initializeSortable();
    updateRoute();
})

$(document).on('click', '#add-point-btn', function () {

    // Pobierz wartość atrybutu 'value' przycisku
    const buttonValue = $(this).val();

    // Znajdź wszystkie listy rozwijalne
    const dropdowns = $('.form-select');

    // Sprawdź, czy istnieje przynajmniej jedna lista rozwijalna
    if (dropdowns.length > 0) {
        // Pobierz ostatnią listę rozwijalną
        const lastDropdown = dropdowns.last();

        // Ustaw jej wartość na wartość przycisku
        lastDropdown.val(buttonValue);

        lastDropdown.trigger('change');

        // Znajdź rodzica o id 'dropdown-group'
        const parentGroup = lastDropdown.closest('.dropdown-group');

        // Znajdź przycisk 'Usuń' w tej grupie i pokaż go
        parentGroup.find('.remove-btn').show();

        
    }
});

function initializeSortable() {
    $("#dropdown-container").sortable({
        items: "> .dropdown-group:not(:last-child)", // Wyklucz ostatni element
        handle: ".handle", // Tylko lista rozwijalna jako uchwyt
        update: function (event, ui) {
            updateRoute(); // Aktualizuj trasę po zmianie kolejności
        }
    }).sortable("refresh"); // Odśwież instancję
}

function downloadMap() {
    $.ajax({
        url: 'http://localhost:8000/api/map-download',
        method: 'GET',
        xhrFields: {
            responseType: 'blob'
        },
        success: function(data) {
            const reader = new FileReader();
            reader.onload = function() {
                const uint8Array = new Uint8Array(reader.result);
                modifyMap(uint8Array);
            };
            reader.readAsArrayBuffer(data);
        },
        error: function(error) {
            console.error('Error downloading map: ', error);
        }
    });
}

async function modifyMap(uint8Array) {
    let points = [];
    $('select').each(function() {
        let id = $(this).val();
        if(id) {
            let point = jsonResponse.data.find(p => p.id == id);
            points.push(point);
             
        }
    });

    console.log("Punkty: ");
    console.log(points);
    // console.log("ścieżka: ", routeId);

    const pdfDoc = await PDFLib.PDFDocument.load(uint8Array);
    const pages = pdfDoc.getPages();
    const firstPage = pages[0];

    const { width, height } = firstPage.getSize();
    console.log("height: ", height);
    console.log("width: ", width);

    for(let i = 1; i < points.length; i++) {

        let circleSize = 15;
        let thickness = 2;
        let color = PDFLib.rgb(1,0,0);

        let start = getPDFCoords(points[i-1]);
        let end = getPDFCoords(points[i]);

        let deltaX = start.x-end.x;
        let deltaY = start.y-end.y;
        let distance = Math.sqrt(deltaX*deltaX+deltaY*deltaY);
        
        let XLineCorrection = deltaX*circleSize/distance;
        let YLineCorrection = deltaY*circleSize/distance;

        firstPage.drawCircle({
            x: start.x,
            y: start.y,
            size: circleSize,
            borderWidth: thickness,
            borderColor: color
        })

        firstPage.drawLine({
            start:{x: start.x - XLineCorrection, y: start.y - YLineCorrection},
            end: {x: end.x + XLineCorrection, y: end.y + YLineCorrection},
            thickness: thickness,
            color: color
        })

        firstPage.drawCircle({
            x: end.x,
            y: end.y,
            size: circleSize,
            borderWidth: thickness,
            borderColor: color
        })
    }

    const pdfBytes = await pdfDoc.save();
    const blob = new Blob([pdfBytes], { type: 'application/pdf' });
    const url = URL.createObjectURL(blob);

    // Tworzenie linku do pobrania
    const link = document.createElement('a');
    link.href = url;
    link.download = `mapa.pdf`;
    link.click();

}

function getPDFCoords(point) {
    let x = 0.282746162 * point.easting - 0.0213779564 * point.northing - 115546.575;
    let y = 0.0216790587 * point.easting + 0.283038932 * point.northing - 218790.281;
    return {x: x, y: y};
}

// function updateRoute() {
//     mapRoute.clearLayers();
//     let wsg84points = [];
//     $('select').each(function() {
//         let id = $(this).val();
//         if(id) {
//             let point = jsonResponse.data.find(p => p.id == id);
//             let epsg2180coords = [point.easting, point.northing];
//             let wsg84coords = transformToWSG84(epsg2180coords);
//             wsg84points.push(wsg84coords);
//             //selectedPoints.push(value);
//             // console.log(epsg2180coords);
            
//         }
//     });

//     let polyline = L.polyline(wsg84points, {color: 'red'});
//     mapRoute.addLayer(polyline);
//     mapRoute.addTo(map);
//     // console.log(selectedPoints);

// }

// Handle start point selection
function setStartPoint(pointId, markers) {
    if (startMarker) {
        // If start and end were the same, reset to end (red)
        if (startMarker === endMarker) {
            updateMarkerColor(startMarker, false, false); // Red
        } else {
            resetMarker(startMarker); // Reset to default
        }
    }

    // Find the new start marker
    startMarker = markers.find(marker => marker.options.pointId === parseInt(pointId));

    // Check if start and end are the same
    if (startMarker && endMarker && startMarker.options.pointId === endMarker.options.pointId) {
        updateMarkerColor(startMarker, false, true); // Purple
    } else if (startMarker) {
        updateMarkerColor(startMarker, true, false); // Green
    }
}

// Handle end point selection
function setEndPoint(pointId, markers) {
    if (endMarker) {
        // If start and end were the same, reset to start (green)
        if (endMarker === startMarker) {
            updateMarkerColor(endMarker, true, false); // Green
        } else {
            resetMarker(endMarker); // Reset to default
        }
    }

    // Find the new end marker
    endMarker = markers.find(marker => marker.options.pointId === parseInt(pointId));

    // Check if start and end are the same
    if (startMarker && endMarker && startMarker.options.pointId === endMarker.options.pointId) {
        updateMarkerColor(endMarker, false, true); // Purple
    } else if (endMarker) {
        updateMarkerColor(endMarker, false, false); // Red
    }
}

function updateMarkerColor(marker, isStart, isBoth) {
    if (marker) {
        if (isBoth) {
            marker.setIcon(purpleIcon); // Purple if both start and end
        } else if (isStart) {
            marker.setIcon(greenIcon); // Green for start
        } else {
            marker.setIcon(redIcon); // Red for end
        }
    }
}

function resetMarker(marker) {
    if (marker) {
        marker.setIcon(defaultIcon); // Reset to default
    }
}

function updateRoute() {
    console.log("updateRoute()");
    //console.log(mapRoute);
    mapRoute.clearLayers(); // Czyść warstwę trasy
    let wsg84points = [];
    let markersToReset = new Set(); // Przechowuje markery do przywrócenia do koloru domyślnego

    // Przetwórz dropdowny, aby znaleźć punkty
    $('select').each(function () {
        let id = $(this).val();
        if (id) {
            let point = jsonResponse.data.find(p => p.id == id);
            if (point) {
                let epsg2180coords = [point.easting, point.northing];
                let wsg84coords = transformToWSG84(epsg2180coords);
                wsg84points.push({ id, coords: wsg84coords });
            }
        }
    });



    if(wsg84points.length > 0) {
        setStartPoint(wsg84points[0].id, mapMarkers.getLayers());
        setEndPoint(wsg84points[wsg84points.length - 1].id, mapMarkers.getLayers());
    }

    // Rysuj trasę
    const polyline = L.polyline(wsg84points.map(p => p.coords), { color: 'red' });
    mapRoute.addLayer(polyline);
    mapRoute.addTo(map);
}

function createDropdown(points) {
    let optionList = `<option selected disabled>Wybierz punkt</option>`;
    points.forEach(function(point) {
        optionList += `<option value="${point.id}">${point.code} - ${point.description}</option>`
    })
    return  `<div class="input-group dropdown-group">
                <span class='input-group-text handle' role='button' style="display:none;"><i class="bi bi-chevron-bar-expand"></i></span>
                <select class="form-select dropdown">`
                    + optionList +
                `</select>
                <button class="btn btn-danger remove-btn" type="button" style="display:none;">Usuń</button>
            </div>`;
    
}

function resetDropdowns(points) {
    $('#dropdown-container').html(createDropdown(points));
}

function initMap(points) {
    map = L.map('map').setView([54.52, 18.49], 13);
    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 17,
        attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
    }).addTo(map);
      
    mapMarkers = L.layerGroup();
    mapRoute = L.layerGroup();

    points.forEach(function(point) {
        let epsg2180coords = [point.easting, point.northing];
        let wsg84coords = transformToWSG84(epsg2180coords);
        let marker = L.marker(wsg84coords, {pointId: point.id});
        marker.bindPopup(point.code.concat(" - ", point.description) +
            `<button id='add-point-btn' value='`+ point.id +`' class='btn btn-success btn-sm m-1 w-100'>Dodaj do trasy</button>`
    );
        mapMarkers.addLayer(marker);
    });
    mapMarkers.addTo(map);

}

function transformToWSG84(epsg2180coords) {
    let coords = proj4('EPSG:2180', 'WSG:84', epsg2180coords);
    var temp=coords[0];
    coords[0]=coords[1];
    coords[1]=temp;
    return coords;
}

