var jsonResponse;
var map;
var mapMarkers;
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

document.addEventListener('DOMContentLoaded', function(){
    $.ajax({
        url: 'http://localhost:8000/api/paths',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            jsonResponse = response;
            console.log(jsonResponse);
            populateTable(jsonResponse.data);
            initMap();
        },
        error: function(error){
            console.log("Błąd: ", error);
        }
    })
});

$('#table tbody').on('click', 'tr', function() {
    let id = $(this).data('id');
    highlightTableRow(id);
    updateMap(id);
});

function highlightTableRow(id) {
    let tableRows = document.getElementById('table').rows;

    for(row of tableRows) {
        row.id != id ? 
            row.classList.remove("table-success"): 
            row.classList.add("table-success");
    }
}

function updateMap(routeId) {
    mapMarkers.clearLayers();

    let points = jsonResponse.data.find(r => r.id == routeId).points;

    points.sort((p1, p2) => {
        return p1.position - p2.position;
    })

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

$('#table tbody').on('click', 'button', function(event) {
    event.stopPropagation();
    let id = $(this). closest('tr').data('id');
    downloadMap(id);
});

function transformToWSG84(epsg2180coords) {
    let coords = proj4('EPSG:2180', 'WSG:84', epsg2180coords);
    var temp=coords[0];
    coords[0]=coords[1];
    coords[1]=temp;
    return coords;
}

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

function populateTable(data) {
    var tableBody = $('#table tbody');
    data.forEach(function(item){
        let row = `
            <tr class="" data-id="${item.id}" id="${item.id}">
                <td>${item.name}</td>
                <td>Obszar</td>
                <td>${item.points.length}</td>
                <td>${calculateRouteLength(item.points)}</td>
                <td>${checkRouteType(item.points)}</td>
                <td><button class="btn btn-success btn-sm w-100">Pobierz mapę</button></td>
            </tr>
        `;

        tableBody.append(row);
    });

    // Inicjalizacja DataTable
    $('#table').DataTable({
        searching: false,
        info: false,
        language: {
          lengthMenu: 'Wyświetl _MENU_ wpisów na stronę'
        }
      });
}

function calculateRouteLength(points) {
    points.sort((p1, p2) => {
        return p1.position - p2.position;
    });

    let totalLength = 0.0;
    console.log(points);

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
    points.sort((p1, p2) => {
        return p1.position - p2.position;
    });
    
    let firstPoint = Math.min(points.map(p => p.position)).id;
    let lastPoint = Math.max(points.map(p => p.position)).id;

    if(firstPoint === lastPoint) {
        return "Pętla";
    } else {
        return "Otwarta";
    }
}

function initMap() {
    map = L.map('map').setView([54.52, 18.49], 13);
    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 17,
        attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
    }).addTo(map);
      
    mapMarkers = L.layerGroup();
}
