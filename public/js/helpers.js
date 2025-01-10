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

function transformToWSG84(epsg2180coords) {
    let coords = proj4('EPSG:2180', 'WSG:84', epsg2180coords);
    var temp=coords[0];
    coords[0]=coords[1];
    coords[1]=temp;
    return coords;
}

function calculateRouteLength(points) {
    points.sort((p1, p2) => {
        return p1.position - p2.position;
    });

    let totalLength = 0.0;
    // console.log(points);

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
    let firstPoint;
    let lastPoint;

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

function populateDropdowns(points) {
    let dropdownList = `<option selected disabled>Wybierz punkt</option>`;
    points.forEach(function(point){
        dropdownList += `<option value="${point.id}">${point.code} - ${point.description}</option>`;
    })
    $('.dropdown-points').each(function() {
        $(this).html(dropdownList);
    })
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

function createTagDropdown(tags) {
    console.log("createTagDropdown()");
    console.log(tags);
    let optionList = `<option selected disabled>Wybierz tag</option>`;
    tags.forEach(function(tag) {
        optionList += `<option value="${tag.id}">${tag.name}</option>`
    })
    return  `<div class="input-group dropdown-group">
                <select class="form-select dropdown">`
                    + optionList +
                `</select>
                <button class="btn btn-danger tag-remove-btn" type="button" style="display:none;">Usuń</button>
            </div>`;
}

function resetDropdowns(points) {
    $('#dropdown-container').html(createDropdown(points));
}

function resetTagDropdowns(tags) {
    $('#dropdown-container').html(createTagDropdown(tags));
}

function collectPoints(markup, points){
    return $(markup).map(function() {
        let id = $(this).val();
        console.log("collectPoints() ->" + id)
        let point = points.find(p => p.id == id);
        return point;
    }).get();
}

function filterPathsWithPoints(paths) {
    return paths.filter(route => Array.isArray(route.points) && route.points.length > 0);
}

function checkPathArea(points) {
    let areas = [-1, 0, 0];
    
    points.forEach(point => {
        areas[point.area_id] = 1;
    });

    if(areas[1] == 1 && areas[2] == 0) return "Grabówek";
    if(areas[1] == 0 && areas[2] == 1) return "Chylonia";
    return "Chylonia i Grabówek";


}

function getAreaNames(areas) {
    const areasSet = new Set();

    areas.forEach(area => {
        areasSet.add(area.name);
    });

    return Array.from(areasSet).sort().join(" ");
}

function getPathAreaNames(points) {
    const areasSet = new Set();

    points.forEach(point => {
        point.areas.forEach(area => {
            areasSet.add(area.name);
        });
    });

    return Array.from(areasSet).sort().join(" ");
}

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