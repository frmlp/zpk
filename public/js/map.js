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

function initMap(mapId) {
    map = L.map(mapId).setView([54.52, 18.49], 13);
    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 17,
        attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
    }).addTo(map);
    return map;
}

function initMarkers() {
    return L.layerGroup();
}

function initPointsPreview(points, markers, map, view) {
    console.log(markers);
    markers.clearLayers();
    points.forEach(function(point) {
        let epsg2180coords = [point.easting, point.northing];
        let wsg84coords = transformToWSG84(epsg2180coords);
        let m = L.marker(wsg84coords, {pointId: point.id});
        if(view === "generator") {
            m.bindPopup(point.code.concat(" - ", point.description + 
                `<div class="row justify-content-center">
                    <div class="col text-center">
                        <button value='` + point.id + `' class="btn btn-success btn-sm m-1 w-100 route-start-btn">Ustaw start trasy</button>
                        <button value='` + point.id + `' class="btn btn-success btn-sm m-1 w-100 route-finish-btn">Ustaw koniec trasy</button>
                    </div>
                </div>`
            ));
        }
        else if(view === "planer") {
            m.bindPopup(point.code.concat(" - ", point.description) +
                `<button id='add-point-btn' value='`+ point.id +`' class='btn btn-success btn-sm m-1 w-100'>Dodaj do trasy</button>`
            );
        }
        else {
            m.bindPopup(point.code.concat(" - ", point.description));
        }
        // m.bindPopup(point.code.concat(" - ", point.description) + popupContent);
        markers.addLayer(m);
    });
    markers.addTo(map);
}

//planer
function drawPath(points, markers, map) {
    markers.clearLayers(); // Czyść warstwę trasy

    let wsg84points = points.map( p => {
        let epsg2180coords = [p.easting, p.northing];
        let wsg84coords = transformToWSG84(epsg2180coords);
        return wsg84coords;
    });

    const polyline = L.polyline(wsg84points, { color: 'red' });
    markers.addLayer(polyline);
    markers.addTo(map);
}

function updateMap(points, markers, map) {
    // console.log(markers);
    // Clear all existing layers
    markers.clearLayers();

    // Return early if no points are provided
    if (!points || points.length === 0) return;

    if('position' in points[0]) {
        points.sort((p1, p2) => {
            return p1.position - p2.position;
        })
    }

    // Check if the first and last points are the same
    const isLoop = points[0].id === points[points.length - 1].id;

    // Convert points to WSG84 and prepare markers
    const wsg84points = points.map((point, index) => {
        const wsg84coords = transformToWSG84([point.easting, point.northing]);
        const marker = L.marker(wsg84coords);
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

        markers.addLayer(marker); // Add marker to layer group
        return wsg84coords; // Return coordinates for polyline
    });

    // Draw polyline connecting all points
    const polyline = L.polyline(wsg84points, { color: 'red' });
    markers.addLayer(polyline);
    markers.addTo(map);
}



// HELPER FUNCTION TO MANAGE MARKER COLORING 
// function setRouteStart() {
//     $(document).on('click', '.route-start-btn', function() {
//         const selectedValue = $(this).val(); // Pobierz wartość z klikniętego przycisku
//         $('#start-point').val(selectedValue).change(); // Ustaw wartość w dropdownie
//     });
// }

// function setRouteFinish() {
//     $(document).on('click', '.route-finish-btn', function() {
//         const selectedValue = $(this).val(); // Pobierz wartość z klikniętego przycisku
//         $('#end-point').val(selectedValue).change(); // Ustaw wartość w dropdownie
//     });
// }



// Reset a marker to its default color
function resetMarker(marker) {
    if (marker) {
        marker.setIcon(defaultIcon);
    }
}

// Handle start point selection
function setStartPoint(newStartPointId, markers, startMarker, endMarker) {
    if (startMarker) {
        // If start and end were the same, reset to end (red)
        if (startMarker === endMarker) {
            updateMarkerColor(startMarker, false, false); // Red
        } else {
            resetMarker(startMarker); // Reset to default
        }
    }

    // Find the new start marker
    startMarker = markers.find(marker => marker.options.pointId === parseInt(newStartPointId));

    // Check if start and end are the same
    if (startMarker && endMarker && startMarker.options.pointId === endMarker.options.pointId) {
        updateMarkerColor(startMarker, false, true); // Purple
    } else if (startMarker) {
        updateMarkerColor(startMarker, true, false); // Green
    }
    return startMarker
}


// Handle end point selection
function setEndPoint(newEndPointId, markers, startMarker, endMarker) {
    if (endMarker) {
        // If start and end were the same, reset to start (green)
        if (endMarker === startMarker) {
            updateMarkerColor(endMarker, true, false); // Green
        } else {
            resetMarker(endMarker); // Reset to default
        }
    }

    // Find the new end marker
    endMarker = markers.find(marker => marker.options.pointId === parseInt(newEndPointId));

    // Check if start and end are the same
    if (startMarker && endMarker && startMarker.options.pointId === endMarker.options.pointId) {
        updateMarkerColor(endMarker, false, true); // Purple
    } else if (endMarker) {
        updateMarkerColor(endMarker, false, false); // Red
    }

    return endMarker;
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

function resetMarkers(markers) {
    markers.forEach(marker => {
        if (marker) marker.setIcon(defaultIcon);
    });
}