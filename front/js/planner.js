var jsonResponse;
var map;
var mapMarkers;
var mapRoute;
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
})

$('#finish-btn').on('click', function() {
    //updateRoute();
})

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
        let marker = L.marker(wsg84coords);
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

