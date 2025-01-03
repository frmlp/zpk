<!DOCTYPE html>
<html lang="pl">
    <head>
        <meta charset="UTF-8">
        <meta name="csrf-token" content="{{csrf_token()}}">

        <title>ZPK</title>

        <!-- BOOTSTRAP -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
        <!-- BOXIXONS -->
        <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
        <!-- DATATABLES -->
        <link href="https://cdn.datatables.net/2.1.8/css/dataTables.dataTables.min.css" rel="stylesheet">
        <link href="https://cdn.datatables.net/responsive/3.0.3/css/responsive.dataTables.min.css" rel="stylesheet">
        <!-- LEAFLET -->
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
        <!-- PDF-LIB -->
        <script src="https://unpkg.com/pdf-lib"></script>
        <!-- PROJ4JS -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/proj4js/2.11.0/proj4.min.js"></script>

        <link rel="stylesheet" href="/../css/style.css" />
        
    </head>
    <body>
        <nav class="navbar navbar-expand-md navbar-dark bg-primary">
            <div class="container-fluid">
                <span class="navbar-brand" style="font-size: 1.5rem;"><i class="bi-compass" ></i> ZPK</span>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
                    <div class="navbar-nav">
                        <!-- <a class="nav-link" aria-current="page" href="#"><i class="bi-person-gear"></i> Profil</a> -->
                        <a class="nav-link" href="/admin/ustawienia"><i class="bi-gear"></i> Ustawienia</a>
                        <a class="nav-link" href="/admin/baza-tras"><i class="bi-table"></i> Baza tras</a>
                        <a class="nav-link " href="/admin/zpk"><i class="bi-geo"></i> Punkty kontrolne</a>
                        <!-- <a class="nav-link" href="#"><i class="bi-tree"></i> Punkty wirtualne</a> -->
                        <a class="nav-link active" href="/admin/tagi"><i class="bi-tags"></i> Tagi</a>
                    </div>
                    <div class="navbar-nav ms-auto">
                        <form id="logoutForm" method="POST" action="/logout">@csrf<button class="nav-link" id="logout-btn" type="submit"><i class="bi-person-circle"></i> Wyloguj</button></form>
                        
                    </div>
                </div>
            </div>
        </nav>

        <div class="content">
            <div class="container-md">
                <div class="bg-light">
                    <div id="table-wrapper"></div>
                        <button id="newTagBtn" class="btn btn-success w-100">Dodaj nowy tag</button>
                        <table class="table table-hover" id="table">
                            <thead>
                                <tr>
                                    <!-- <th>ID</th> -->
                                    
                                    <th>Nazwa</th>
                                    <th></th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
        
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
        

<!-- Point Modal -->
<div class="modal fade" id="tagModal" tabindex="-1" aria-labelledby="pointModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="tagModalLabel">Tag</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="tagForm">
                    <!-- Pole Kod -->
                    <div class="mb-3">
                        <label for="name" class="form-label">Nazwa</label>
                        <input type="text" class="form-control" id="name" name="name">
                    </div>
                   
                    <!-- Przyciski -->
                     <div class="row">
                        <div class="col">
                            <button type="submit" class="btn btn-primary w-100" id="saveChanges">Zapisz</button>
                        </div>
                        <div class="col">
                            <button type="button" class="btn btn-outline-secondary w-100" data-bs-dismiss="modal">Anuluj</button>
                        </div>
                        
                        
                     </div>
                    
                </form>
            </div>
        </div>
    </div>
</div>




        <!-- JQUERY -->
        <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
        <!-- BOOTSTRAP -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
        <!-- DATATABLES -->
        <script src="https://cdn.datatables.net/2.1.8/js/dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/responsive/3.0.3/js/dataTables.responsive.min.js"></script>
        <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script> -->
        <!-- <script src="https://cdn.datatables.net/2.1.2/js/dataTables.bootstrap5.js"></script> -->
        <script src="../js/map.js" async></script>
        <script src="../js/map-pdf.js" async></script>
        <script src="../js/helpers.js" async></script>
        <script src="../js/table.js" async></script>
        <script src="../js/data.js" async></script>
        <script src="../js/admin-tagi.js" async></script>

        <!-- <script>
            $(document).ready(function() {
                let table = initTable();
                //let map =initMap();

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

                getPointsData()
                    .then(function(result) {
                        console.log(result.data);
                        populateTable(table, result.data);
                    }).catch((error) => console.error("Error: ", error));
                handleEditButtonClick();
                handleNewButtonClick();
                handleCoordinateToggle();
                handleCoordinateSynchonization();

                let token = "";
                

                $.ajax({
                    url: 'http://localhost:8000/token',
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        console.log(response.token);
                        token = response.token;
                        $('form').append('<input type="hidden" name="_token" value="' + response.token + '">');
                        $('logoutForm').append('<input type="hidden" name="_token" value="' + response.token + '">');
                    },
                    error: function(error){
                        console.log("Błąd: ", error);
                    }
                });

                // $('#logout-btn').on('click', function(event){
                //     event.preventDefault();
                //     console.log('logout button clicked');
                //     console.log(token);
                //     $.ajax({
                //         url: 'http://localhost:8000/logout',
                //         type: 'POST',
                //         data: {
                //             _token: token,
                //         },
                //         success: function(response) {
                //             // alert('Wylogowano');
                //             console.log(response);
                //         },
                //         error: function(xhr, status, error) {
                //             console.log('error');
                //         }
                //     })
                // });

                

                // toggleCoordinateFields();
                
            });

            function getPointsData() 
            {
                return $.ajax({
                    url: 'http://localhost:8000/api/points',
                    type: 'GET',
                    dataType: 'json',
                })
            }

            function initTable() {
                return $('#table').DataTable({
                    searching: false,
                    info: false,
                    lengthMenu: [5, 10, 15],
                    language: {
                        lengthMenu: 'Wyświetl _MENU_ wpisów na stronę'
                    },
                    // scrollCollapse: true,
                    // scrollX: false,
                    // scrollY: '40vmax',
                    responsive: true,
                    // columnDefs: [
                    //     {responsivePriority: 5, targets: 4},
                    //     {responsivePriority: 6, targets: 5},
                    //     {responsivePriority: 3, targets: 2},
                    //     {responsivePriority: 4, targets: 3},
                    //     {responsivePriority: 1, targets: 0},
                    //     {responsivePriority: 2, targets: 1},
                    // ]
                    
                });
            }


            function populateTable(table, points) 
            {
                table.on('draw', function() {
                    table.rows().every(function() {
                        var rowNode = this.node();
                        var rowData = this.data();
                        $(rowNode).attr('id', points.find(p => p.code === rowData[0]).id);
                    });

                    // let height = document.getElementById('table-wrapper').offsetHeight;
                    // // console.log(height);
                    // document.getElementById('map').style.height = height + 'px';

                });

                points.forEach(function(point) {
                    let row=table.row.add([
                        point.code,
                        point.description,
                        "Obszar",
                        point.easting,
                        point.northing,
                        `<button id="${point.id}" class="edit-btn btn btn-warning w-100">Edytuj</button>`,
                        `<button id="${point.id}" class="delete-btn btn btn-danger w-100">Usuń</button>`
                    ]);
                    
                });

                table.draw(false);
                
            }

            // function handleNewButtonClick() {
            //     $('#newPointBtn').on('click', function() {
            //         let editModal = new bootstrap.Modal(document.getElementById('editModal'));
            //         editModal.show();
            //     })
                
            // }

            function handleNewButtonClick()
            {
                $('#newTagBtn').on('click', function() {
                    $('#tagModalLabel').text('Nowy Tag');
                    $('#tagForm').attr('action', '/admin/tags');
                    $('#tagForm').attr('method', 'POST'); // Metoda POST do tworzenia nowego punktu
                    $('#tagForm').find('input[name="_method"]').remove(); 

                    $('#tagForm')[0].reset(); // Wyczyść formularz

                    $('#tagModal').modal('show');
                })
            }

            function handleEditButtonClick()
            {
                $('#table').on('click', '.edit-btn', function() {
                    // Pobranie wiersza, w którym znajduje się kliknięty przycisk
                    let row = $(this).closest('tr');
                    // Pobranie danych z tego wiersza z użyciem DataTables
                    let table = $('#table').DataTable();
                    let rowData = table.row(row).data();
                    // Zapisanie danych w zmiennej pointData
                    let pointData = {
                        code: rowData[0],
                        description: rowData[1],
                        area: rowData[2],
                        easting: rowData[3],
                        northing: rowData[4],
                        id: row.attr('id')  // Pobranie atrybutu id z wiersza
                    };

                    // ustawienie argumentów żądania formularza
                    $('#pointForm').attr('action', '/admin/points/' + pointData.id);
                    $('#pointForm').attr('method', 'POST'); // Ustawienie metody POST, a Laravel obsłuży PUT przez ukryte pole _method
                    $('#pointForm').append('<input type="hidden" name="_method" value="PUT">');
                    // wypełnienie formularza danymi
                    $('#pointModalLabel').text('Edytuj Punkt');
                    $('#pointCode').val(pointData.code);
                    $('#pointDescription').val(pointData.description);
                    $('#pointEasting').val(pointData.easting);
                    $('#pointNorthing').val(pointData.northing);

                    let wsg84coords = transformToWSG84(pointData.easting, pointData.northing);
                    $('#pointLatitude').val(Number(wsg84coords.latitude.toFixed(5)));
                    $('#pointLongitude').val(Number(wsg84coords.longitude.toFixed(5)));

                    // if (rowData.coordinateType === 'WSG84') {
                    //     $('#coordinateWSG84').prop('checked', true);
                    // } else {
                    //     $('#coordinateEPSG2180').prop('checked', true);
                    // }
                    // toggleCoordinateFields(); // Ustaw widoczność pól współrzędnych

                    $('#pointModal').modal('show');
                });
            }

            // function handleEditButtonClickLeg() {
            //     $('#table').on('click', '.edit-btn', function() {
            //         // Pobranie wiersza, w którym znajduje się kliknięty przycisk
            //         let row = $(this).closest('tr');
                    
            //         // Pobranie danych z tego wiersza z użyciem DataTables
            //         let table = $('#table').DataTable();
            //         let rowData = table.row(row).data();
            //         //  console.log(rowData);
            //         //  console.log(rowData.log);
            //         // Zapisanie danych w zmiennej pointData
            //         let pointData = {
            //             code: rowData[0],
            //             description: rowData[1],
            //             area: rowData[2],
            //             easting: rowData[3],
            //             northing: rowData[4],
            //             id: row.attr('id')  // Pobranie atrybutu id z wiersza
            //         };

            //         let wsg84coords = transformToWSG84(pointData.easting, pointData.northing);

            //         // Ustawienie action formularza na "points/{id}"
            //         $('#editForm').attr('action', 'points/' + pointData.id);

            //         // Wypełnienie modala danymi
            //         // $('#editId').val(pointData.id);
            //         $('#editCode').val(pointData.code);
            //         $('#editDescription').val(pointData.description);
            //         $('#editEasting').val(pointData.easting);
            //         $('#editNorthing').val(pointData.northing);
            //         $('#editLatitude').val(Number(wsg84coords.latitude.toFixed(5)));
            //         $('#editLongitude').val(Number(wsg84coords.longitude.toFixed(5)));



                    
            //         // Wyświetlenie modala
            //         let editModal = new bootstrap.Modal(document.getElementById('editModal'));
            //         editModal.show();
            //     });
            // }
            function handleCoordinateToggle() 
            {
                $('input[name="coordinateType"]').on('change', function() {
                    if ($('#coordinateWSG84').is(':checked')) {
                    $('#wsg84Fields').show();
                    $('#epsg2180Fields').hide();
                } else if ($('#coordinateEPSG2180').is(':checked')) {
                    $('#wsg84Fields').hide();
                    $('#epsg2180Fields').show();
                }
                });
            }

            // function toggleCoordinateFields() 
            // {
                
            // }

            function transformToWSG84(easting, northing) 
            {
                let coords = proj4('EPSG:2180', 'WSG:84', [easting, northing]);

                return {
                    longitude: coords[0],
                    latitude: coords[1]
                };
            }

            function transformToEPSG2180(longitude, latitude)
            {
                let coords = proj4('WSG:84', 'EPSG:2180', [longitude, latitude]);
                
                return {
                    easting: coords[0],
                    northing: coords[1]
                };
            }

            function handleCoordinateSynchonization()
            {
                $('#pointEasting').on('input', function() {
                    inputEPSG2180Field();
                })

                $('#pointNorthing').on('input', function() {
                    inputEPSG2180Field();
                })

                $('#pointLatitude').on('input', function() {
                    inputWSG84Field();
                })

                $('#pointLongitude').on('input', function() {
                    inputWSG84Field();
                })

                function inputEPSG2180Field() {
                    let easting = Number($('#pointEasting').val());
                    let northing = Number($('#pointNorthing').val());
                    let wsg84coords = transformToWSG84(easting, northing);

                    $('#pointLatitude').val(Number(wsg84coords.latitude.toFixed(5)));
                    $('#pointLongitude').val(Number(wsg84coords.longitude.toFixed(5)));
                }

                function inputWSG84Field() {
                    let latitude = Number($('#pointLatitude').val());
                    let longitude = Number($('#pointLongitude').val());
                    let epsg2180coords = transformToEPSG2180(longitude, latitude);

                    $('#pointEasting').val(Number(epsg2180coords.easting.toFixed(2)));
                    $('#pointNorthing').val(Number(epsg2180coords.northing.toFixed(2)));
                }
            }

        </script> -->


    </body>
</html>