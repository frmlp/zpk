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

document.addEventListener('DOMContentLoaded', function() {
    $.ajax({
        url: 'http://localhost:8000/api/points',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            jsonResponse = response;
            console.log(jsonResponse);
            resetDropdowns(jsonResponse.data);
            initMap(jsonResponse.data);
        }
    })
})

$(document).on('change', '#dropdown', function() {
    var parentGroup = $(this).closest('#dropdown-group');
    parentGroup.find('#remove-btn').show();

    if($('#dropdown-container #dropdown-group:last-child select').val() !== null) {
        $('#dropdown-container').append(createDropdown(jsonResponse.data));
    }

    updateRoute();
})

$(document).on('click', '#remove-btn', function() {
    $(this).closest('#dropdown-group').remove();
    updateRoute();
})

$('#reset-btn').on('click', function() {
    resetDropdowns(jsonResponse.data);
    updateRoute();
})

$('#finish-btn').on('click', function() {
    downloadMap();
})

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



function updateRoute() {
    mapRoute.clearLayers();
    let wsg84points = [];
    $('select').each(function() {
        let id = $(this).val();
        if(id) {
            let point = jsonResponse.data.find(p => p.id == id);
            let epsg2180coords = [point.easting, point.northing];
            let wsg84coords = transformToWSG84(epsg2180coords);
            wsg84points.push(wsg84coords);
            //selectedPoints.push(value);
            // console.log(epsg2180coords);
            
        }
    });

    let polyline = L.polyline(wsg84points, {color: 'red'});
    mapRoute.addLayer(polyline);
    mapRoute.addTo(map);
    // console.log(selectedPoints);

}

function createDropdown(points) {
    let optionList = `<option selected disabled>Wybierz punkt</option>`;
    points.forEach(function(point) {
        optionList += `<option value="${point.id}">${point.code} - ${point.description}</option>`
    })
    return  `<div id="dropdown-group" class="input-group">
                <select id="dropdown" class="form-select">`
                    + optionList +
                `</select>
                <button id="remove-btn" class="btn btn-danger" type="button" style="display:none;">Usuń</button>
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
        let marker = L.marker(wsg84coords, {icon: yellowIcon});
        marker.bindPopup(point.code.concat(" - ", point.description));
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

