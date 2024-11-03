document.addEventListener('DOMContentLoaded', function() {
    getPointsData()
        .then(function(result) {
            populateDropdowns(result.data);
        }).catch(() => console.log("Error"));
    
});

function getPointsData() {
    return $.ajax({
        url: 'http://localhost:8000/api/points',
        type: 'GET',
        dataType: 'json',
    })
}

$(document).ready(function() {
    var tableData;
    var table = initTable();

    //init Map
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

    var mapMarkers = L.layerGroup();
    var map = initMap();
    getPointsData()
        .then(function(result) {
            showPointsPreview(result.data, mapMarkers, map);
        }).catch(() => console.log("Error"));


    $('#change-parameters-btn').on('click', function(event) {
        $('#table-wrapper').hide();
        $('#form-wrapper').show();
    })

    $('#regenerate-btn').on('click', function(event) {
        console.log("regenerate");
        getRouteData(event)
            .then(function(result) {
                tableData = result.data;
                populateTable(tableData, table)
            }).catch(() => console.log("Error"));
    })

    $('#generate-btn').on('click', function(event) {
        getRouteData(event)
            .then(function(result) {
                tableData = result.data;
                populateTable(tableData, table);
                $('#form-wrapper').hide();
                $('#table-wrapper').show();
                console.log(tableData);
            }).catch(() => console.log("Error"));

    });

    $('#routesTable tbody').on('click', 'tr', function() {
        let id = $(this).find('button').attr('id');
        let points = tableData.find(r => r.id == id).points;
        highlightTableRow(id);
        updateMap(points, mapMarkers, map);

    });

});

function highlightTableRow(id) {
    // let tableRows = document.getElementById('routesTable').rows;
    $('#routesTable > tbody > tr').each(function(){
        let row = $(this);
        row.find('button').attr('id') != id ?
        row.removeClass("table-info"): 
        row.addClass("table-info");
    })
    // for(row of tableRows) {
    //     row.find('button').attr('id') != id ? 
    //         row.classList.remove("table-info"): 
    //         row.classList.add("table-info");
    // }
}

function updateMap(points, mapMarkers, map) {
    mapMarkers.clearLayers();

    // let points = jsonResponse.data.find(r => r.id == routeId).points;

    // points.sort((p1, p2) => {
    //     return p1.position - p2.position;
    // })

    if(points) {
        let epsg2180points = points.map(p => ({coords: [p.easting, p.northing], description: p.code.concat(" - ", p.description)}));
        let wsg84points = [];
        epsg2180points.forEach(function(item) {
            let wsg84coords = transformToWSG84(item.coords);
            wsg84points.push(wsg84coords);
            let marker = L.marker(wsg84coords);
            marker.bindPopup(item.description);
            mapMarkers.addLayer(marker);
        })

        let polyline = L.polyline(wsg84points, {color: 'red'});
        mapMarkers.addLayer(polyline);
        mapMarkers.addTo(map);

    }
};

function initMap() {
    let map = L.map('map').setView([54.52, 18.49], 13);
    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 17,
        attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
    }).addTo(map);

    return map;
}

function showPointsPreview(points, mapMarkers, map) {
    points.forEach(function(point) {
        let epsg2180coords = [point.easting, point.northing];
        let wsg84coords = transformToWSG84(epsg2180coords);
        let marker = L.marker(wsg84coords);
        marker.bindPopup(point.code.concat(" - ", point.description + 
            '<div class="row justify-content-center"><div class="col text-center"><button class="btn btn-primary btn-sm m-1 w-100">Ustaw start trasy</button></div></div><div class="row justify-content-center"><div class="col text-center"><button class="btn btn-primary btn-sm m-1 w-100">Ustaw koniec trasy</button></div></div>'
        ));
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

function initTable() {
    return $('#routesTable').DataTable({
        searching: false,
        info: false,
        "lengthMenu": [5, 10, 15],
        language: {
            lengthMenu: 'Wyświetl _MENU_ wpisów na stronę'
        },
        responsive: true,
        columnDefs: [
            {responsivePriority: 4, targets: 0},
            {responsivePriority: 5, targets: 1},
            {responsivePriority: 3, targets: 2},
            {responsivePriority: 2, targets: 3},
            {responsivePriority: 6, targets: 4},
            {responsivePriority: 1, targets: 5},
        ]
        
    });
}

function getRouteData(event) {
    event.preventDefault(event);
 
    let startPoint = $('.dropdown-start').val();
    let endPoint = $('.dropdown-end').val();
    let distance = $('input[name="distance"]:checked').val();
    let points = $('input[name="points"]:checked').val();

    return $.ajax({
        url: 'http://localhost:8000/api/generator',
        type: 'GET',
        dataType:'json',
        data: {
            start_point_id: startPoint,
            end_point_id: endPoint,
            number_of_points_range: points,
            distance_range: distance
        },
       
    })
}

function populateTable(tableData, table)
{
    console.log("populateTable()");
    table.clear().draw();
    tableData.forEach(function(route) {
        table.row.add([
            routeToString(route.points),
            "Obszar",
            route.points.length,
            calculateRouteLength(route.points),
            checkRouteType(route.points),
            `<button id="${route.id}" class="btn btn-primary w-100">Pobierz mapę</button>`
        ]).draw(false);
    })
}

function routeToString(points) {
    let path = "";
    let delimiter = " > ";

    points.forEach(function(point) {
        path += point.code + delimiter;
    });

    path = path.slice(0, - delimiter.length);

    return path;
}

function calculateRouteLength(points) {
    let totalLength = 0.0;

    for(let i = 1; i < points.length; i++) {
        //divide by 1000 to get values in kilometers
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

function checkRouteType(points) {
    let firstPoint = Math.min(points.map(p => p.position)).id;
    let lastPoint = Math.max(points.map(p => p.position)).id;

    if(firstPoint === lastPoint) {
        return "Pętla";
    } else {
        return "Otwarta";
    }
}

function populateDropdowns(points) {
    let dropdownList = `<option selected disabled>Wybierz punkt</option>`;
    points.forEach(function(point){
        dropdownList += `<option value="${point.id}">${point.code} - ${point.description}</option>`;
    })
    $('.dropdown-points').each(function() {
        $(this).html(dropdownList);
    })
}