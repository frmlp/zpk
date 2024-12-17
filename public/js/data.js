function getPathData() {
    return $.ajax({
        url: 'http://localhost:8000/api/paths',
        type: 'GET',
        dataType: 'json',
    });
}

function getPointsData() {
    return $.ajax({
        url: 'http://localhost:8000/api/points',
        type: 'GET',
        dataType: 'json',
    })
}

function getGeneratorData(startPoint, endPoint, distance_range, points_range) {
    // event.preventDefault(event);
    return $.ajax({
        url: 'http://localhost:8000/api/generator',
        type: 'GET',
        dataType:'json',
        data: {
            start_point_id: startPoint,
            end_point_id: endPoint,
            number_of_points_range: points_range,
            distance_range: distance_range
        }
       
    });
}