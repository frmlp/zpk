var jsonResponse = null;

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

var points = null;
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
            points = result.data;
            showPointsPreview(points, mapMarkers, map);
        }).catch(() => console.log("Error"));


    $('#change-parameters-btn').on('click', function(event) {
        $('#table-wrapper').hide();
        $('#form-wrapper').show();
        showPointsPreview(points, mapMarkers, map);
        $('#start-point').change();
        $('#end-point').change();
    })

    $('#regenerate-btn').on('click', function(event) {
        console.log("regenerate");
        getRouteData(event)
            .then(function(result) {
                tableData = result.data;
                populateTable(tableData, table);
                updateMap(null, mapMarkers, map);
            }).catch(() => console.log("Error"));
    })

    $('#generate-btn').on('click', function(event) {
        getRouteData(event)
            .then(function(result) {
                tableData = result.data;
                populateTable(tableData, table);
                updateMap(null, mapMarkers, map);
                $('#form-wrapper').hide();
                $('#table-wrapper').show();
                console.log(tableData);
            }).catch(() => console.log("Error"));

    });

    $('#routesTable tbody').on('click', 'tr', function() {
        let id = $(this).find('button').data('id');
        console.log(">>> id: " + id);
        let points = tableData.find(r => r.id == id).points;
        highlightTableRow(id);
        updateMap(points, mapMarkers, map);

    });

    setRouteStart();
    setRouteFinish();

    // Event listeners for dropdown changes
    $('#start-point').on('change', function () {
        const selectedPointId = $(this).val();
        setStartPoint(selectedPointId, mapMarkers.getLayers());
    });
    
    $('#end-point').on('change', function () {
        const selectedPointId = $(this).val();
        setEndPoint(selectedPointId, mapMarkers.getLayers());
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

    $(document).on('click', '.download-btn', function() {
        let id = $(this).data('id');
        console.log(id);
        downloadMap(id);
    })

});

function downloadMap(routeId) {
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
                modifyMap(uint8Array, routeId);
            };
            reader.readAsArrayBuffer(data);
        },
        error: function(error) {
            console.error('Error downloading map: ', error);
        }
    });

};

async function modifyMap(uint8Array, routeId) {
    
    let points = jsonResponse.data.find(r => r.id == routeId).points;

    points.sort((p1, p2) => {
        return p1.position - p2.position;
    });
    console.log("Punkty: ");
    console.log(points);
    console.log("ścieżka: ", routeId);

    const pdfDoc = await PDFLib.PDFDocument.load(uint8Array);
    const pages = pdfDoc.getPages();
    const firstPage = pages[0];

    const { width, height } = firstPage.getSize();
    console.log("height: ", height);
    console.log("width: ", width);

    let circleSize = 15;
    let thickness = 2;
    let color = PDFLib.rgb(1,0,1);
    

    for(let i = 1; i < points.length; i++) {

        // let circleSize = 15;
        // let thickness = 2;
        // let color = PDFLib.rgb(1,0,0);

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
    let x = 0.26768 * point.easting - 0.00125 * point.northing - 123844.43;
    let y = 0.00299 * point.easting + 0.26643 * point.northing - 197788.31;
    return {x: x, y: y};
}

function highlightTableRow(id) {
    // let tableRows = document.getElementById('routesTable').rows;
    $('#routesTable > tbody > tr').each(function(){
        let row = $(this);
        row.find('button').data('id') != id ?
        row.removeClass("table-success"): 
        row.addClass("table-success");
    })
    // for(row of tableRows) {
    //     row.find('button').attr('id') != id ? 
    //         row.classList.remove("table-info"): 
    //         row.classList.add("table-info");
    // }
}

function updateMap(points, mapMarkers, map) {
    // Clear all existing layers
    mapMarkers.clearLayers();

    // Return early if no points are provided
    if (!points || points.length === 0) return;

    // Check if the first and last points are the same
    const isLoop = points[0].id === points[points.length - 1].id;

    // Convert points to WSG84 and prepare markers
    const wsg84points = points.map((point, index) => {
        const wsg84coords = transformToWSG84([point.easting, point.northing]);
        const marker = L.marker(wsg84coords, { pointId: point.id });
        marker.bindPopup(`${point.code} - ${point.description}`);

        // Set marker icon based on position
        if (isLoop) {
            // If the route is a loop, first and last points get a purple icon
            if (index === 0 || index === points.length - 1) {
                marker.setIcon(purpleIcon);
            } else {
                marker.setIcon(defaultIcon);
            }
        } else {
            // For a non-loop route
            if (index === 0) {
                marker.setIcon(greenIcon); // Start point
            } else if (index === points.length - 1) {
                marker.setIcon(redIcon); // End point
            } else {
                marker.setIcon(defaultIcon); // Middle points
            }
        }

        mapMarkers.addLayer(marker); // Add marker to layer group
        return wsg84coords; // Return coordinates for polyline
    });

    // Draw polyline connecting all points
    const polyline = L.polyline(wsg84points, { color: 'red' });
    mapMarkers.addLayer(polyline);

    // Add all layers to the map
    mapMarkers.addTo(map);
}

function initMap() {
    let map = L.map('map').setView([54.52, 18.49], 13);
    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 17,
        attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
    }).addTo(map);

    return map;
}

function showPointsPreview(points, mapMarkers, map) {
    mapMarkers.clearLayers();
    points.forEach(function(point) {
        let epsg2180coords = [point.easting, point.northing];
        let wsg84coords = transformToWSG84(epsg2180coords);
        let marker = L.marker(wsg84coords, {pointId: point.id});
        marker.bindPopup(point.code.concat(" - ", point.description + 
            `<div class="row justify-content-center">
                <div class="col text-center">
                    <button value='` + point.id + `' class="btn btn-success btn-sm m-1 w-100 route-start-btn">Ustaw start trasy</button>
                    <button value='` + point.id + `' class="btn btn-success btn-sm m-1 w-100 route-finish-btn">Ustaw koniec trasy</button>
                </div>
            </div>`
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
            {responsivePriority: 3, targets: 1},
            {responsivePriority: 2, targets: 2},
            {responsivePriority: 5, targets: 3},
            {responsivePriority: 1, targets: 4},
            //{responsivePriority: 1, targets: 5},
        ]
        
    });
}

function getRouteData(event) {
    event.preventDefault(event);
 
    let startPoint = $('#start-point').val();
    let endPoint = $('#end-point').val();
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
        success: function(data) {
            jsonResponse = data;
        },
        error: function(error) {
            console.log(error);
        }
       
    });

}

function populateTable(tableData, table)
{
    console.log("populateTable()");
    table.clear().draw();
    tableData.forEach(function(route) {
        table.row.add([
            //routeToString(route.points),
            "Obszar",
            route.points.length,
            calculateRouteLength(route.points),
            checkRouteType(route.points),
            `<button data-id="${route.id}" class="btn btn-success w-100 download-btn">Pobierz mapę</button>`
        ]).draw(false);
    })
}

// function routeToString(points) {
//     let path = "";
//     let delimiter = " > ";

//     points.forEach(function(point) {
//         path += point.code + delimiter;
//     });

//     path = path.slice(0, - delimiter.length);

//     return path;
// }

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
    //console.log(points);
    let firstPoint = points[0];
    let lastPoint = points[points.length - 1];

    if(firstPoint.id === lastPoint.id) {
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

function setRouteStart() {
    $(document).on('click', '.route-start-btn', function() {
        const selectedValue = $(this).val(); // Pobierz wartość z klikniętego przycisku
        $('#start-point').val(selectedValue).change(); // Ustaw wartość w dropdownie
    });
}

function setRouteFinish() {
    $(document).on('click', '.route-finish-btn', function() {
        const selectedValue = $(this).val(); // Pobierz wartość z klikniętego przycisku
        $('#end-point').val(selectedValue).change(); // Ustaw wartość w dropdownie
    });
}

// Update marker color based on start or end point
function updateMarkerColor(marker, isStart) {
    if (marker) {
        marker.setIcon(isStart ? greenIcon : redIcon);
    }
}

// Reset a marker to its default color
function resetMarker(marker) {
    if (marker) {
        marker.setIcon(defaultIcon);
    }
}

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

// function resetMarker(marker) {
//     if (marker) {
//         marker.setIcon(defaultIcon); // Reset to default
//     }
// }