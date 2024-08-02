document.addEventListener('DOMContentLoaded', function() {
    $.ajax({
        url: 'http://localhost:8000/api/points',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            // $('#table-wrapper').hide();
            var jsonResponse = response;
            console.log(jsonResponse);
            populateDropdowns(jsonResponse.data);
            
            
        }
    })
});

$(document).ready(function() {
    var tableData;
    var table = $('#routesTable').DataTable({
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
        // populateTable();
    })

    $('#generate-btn').on('click', function(event) {
        getRouteData(event)
            .then(function(result) {
                tableData = result.data;
                populateTable(tableData, table);
                $('#form-wrapper').hide();
                $('#table-wrapper').show();
                console.log(tableData);
            }).catch(() => console.log("Error"));;

    });
});

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
    // points.sort((p1, p2) => {
    //     return p1.position - p2.position;
    // });

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
    // points.sort((p1, p2) => {
    //     return p1.position - p2.position;
    // });
    
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