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
        
        firstPoint = Math.min(points.map(p => p.position)).id;
        lastPoint = Math.max(points.map(p => p.position)).id;
    }
    else {
        firstPoint = points[0].id;
        lastPoint = points[points.length - 1].id;
    }
    
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

function resetDropdowns(points) {
    $('#dropdown-container').html(createDropdown(points));
}

function collectPoints(markup, points){
    return $(markup).map(function() {
        let id = $(this).val();
        let point = points.find(p => p.id == id);
        return point;
    }).get();
}

