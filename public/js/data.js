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

function getAdminPathData() {
    return $.ajax({
        url: 'http://localhost:8000/admin/paths',
        type: 'GET',
        dataType: 'json',
    });
}

function getAdminPointsData() {
    return $.ajax({
        url: 'http://localhost:8000/admin/points',
        type: 'GET',
        dataType: 'json',
    })
}

function getAdminTagsData() {
    return $.ajax({
        url: 'http://localhost:8000/admin/tags',
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

function getTagData(){
    return $.ajax({
        url: 'http://localhost:8000/admin/tags',
        type: 'GET',
        dataType: 'json'
    })
}

function csrfAjaxSetup() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
        }
    })
}

function addTokenToForms(attributeArray) {
    $.ajax({
        url: 'http://localhost:8000/token',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            let token = response.token;
            attributeArray.forEach(attribute => {
                $(attribute).append('<input type="hidden" name="_token" value="' + response.token + '">');
            });
            // $('form').append('<input type="hidden" name="_token" value="' + response.token + '">');
            // $('logoutForm').append('<input type="hidden" name="_token" value="' + response.token + '">');
        },
        error: function(error){
            console.log("Błąd: ", error);
        }
    });
}

function getToken() {
    return $.ajax({
        url: 'http://localhost:8000/token',
        type: 'GET',
        dataType: 'json'
    });
}