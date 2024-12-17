$(document).ready(function() {
    let points = [];
    const map = initMap("map");
    const markers = initMarkers();
    let routeMarkers = initMarkers();
    let startMarker = null;
    let endMarker = null;

    getPointsData()
        .then(function(result) {
            points = result.data;
            resetDropdowns(points);
            initializeSortable();
            initPointsPreview(points, markers, map, "planer");
        });
    

    $(document).on('change', '.dropdown', function() {
        var parentGroup = $(this).closest('.dropdown-group');
        parentGroup.find('.remove-btn, .handle').show();
        // parentGroup.find('.handle').show();
    
        if ($('#dropdown-container .dropdown-group').last().find('select').val()) {
            $('#dropdown-container').append(createDropdown(points));
        }

        updatePath();
    });

    $(document).on('click', '.remove-btn', function() {
        $(this).closest('.dropdown-group').remove();
        updatePath();
    });

    $('#reset-btn').on('click', function() {
        resetDropdowns(points);
        updatePath();
        resetMarkers([startMarker, endMarker]);
        startMarker = null;
        endMarker = null;
    });

    $('#finish-btn').on('click', function() {
        let pathPoints = collectPoints('select', points);

        if(pathPoints.length > 1) {
            downloadMap(pathPoints);
        }
        
    });
        
    $(document).on('click', '#add-point-btn', function () {
        const buttonValue = $(this).val();
        const lastDropdown = $('.form-select').last();
    
        if (lastDropdown.length) {
            lastDropdown.val(buttonValue).trigger('change');
            lastDropdown.closest('.dropdown-group').find('.remove-btn').show();
        }
    });

    function updatePath() {
        let pathPoints = collectPoints('select', points);
    
        if(pathPoints.length > 0) {
            let startPointId = pathPoints[0].id;
            let endPointId = pathPoints[pathPoints.length - 1].id;
    
            startMarker = setStartPoint(startPointId, markers.getLayers(), startMarker, endMarker);
            endMarker = setEndPoint(endPointId, markers.getLayers(), startMarker, endMarker);
        }
    
        drawPath(pathPoints, routeMarkers, map);
    };

    function initializeSortable() {
        $("#dropdown-container").sortable({
            items: "> .dropdown-group:not(:last-child)", // Wyklucz ostatni element
            handle: ".handle", // Tylko lista rozwijalna jako uchwyt
            update: function (event, ui) {
                updatePath(); // Aktualizuj trasę po zmianie kolejności
            }
        }).sortable("refresh"); // Odśwież instancję
    }


});













