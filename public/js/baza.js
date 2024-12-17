$(document).ready(function() {
    let map = initMap("map");
    let markers = initMarkers();
    let paths;
    // let table = initTable(true);
    getPathData()
        .then(function(result) {
            // console.log("halo");
            paths = result.data;
            // console.log(jsonresponse);
            populateTable(paths, true);
    }).catch((error) => console.log(error));
    
    $('#table tbody').on('click', 'tr', function() {
        let id = $(this).data('id');
        highlightTableRow(id);

        let points = paths.find(p => p.id == id).points;
        updateMap(points, markers, map);
    });

    $(document).on('click', '.download-btn', function() {
        let id = $(this).data('id');
        let points = paths.find(r => r.id == id).points;
        downloadMap(points);
    })
});