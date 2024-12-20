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
                        <a class="nav-link active" href="/admin/ustawienia"><i class="bi-gear"></i> Ustawienia</a>
                        <a class="nav-link" href="/admin/baza-tras"><i class="bi-table"></i> Baza tras</a>
                        <a class="nav-link " href="/admin/zpk"><i class="bi-geo"></i> Punkty kontrolne</a>
                        <!-- <a class="nav-link" href="#"><i class="bi-tree"></i> Punkty wirtualne</a> -->
                        <a class="nav-link" href="/admin/tagi"><i class="bi-tags"></i> Tagi</a>
                    </div>
                    <div class="navbar-nav ms-auto">
                        <form id="logoutForm" method="POST" action="/logout"><button class="nav-link" id="logout-btn" type="submit"><i class="bi-person-circle"></i> Wyloguj</button></form>
                        
                    </div>
                </div>
            </div>
        </nav>

        <div class="content">
            <div class="container-md bg-light">
                <h1 class="text-center mb-4">Zmień nazwę użytkownika lub hasło</h1>

                <!-- Formularz zmiany nazwy użytkownika -->
                <div class="card mb-4">
                    <div class="card-body">
                        <h2 class="card-title">Zmiana nazwy użytkownika</h2>
                        
                        <div id="messageWrapper" class="m-3">
                            <div id="usernameAlertMessage" class="text-danger"></div>
                            <div id="usernameSuccessMessage" class="text-success">Nazwa użytkownika została zmieniona</div>
                        </div>
                        

                        <form id="usernameForm" action="/username" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label for="currentUsername" class="form-label">Podaj obecną nazwę użytkownika</label>
                                <input type="text" class="form-control" id="currentUsername" name="currentUsername" required>
                            </div>
                            <div class="mb-3">
                                <label for="newUsername" class="form-label">Wprowadź nową nazwę użytkownika</label>
                                <input type="text" class="form-control" id="newUsername" name="newUsername" required>
                            </div>
                            <div class="mb-3">
                                <label for="confirmUsername" class="form-label">Potwierdź nową nazwę użytkownika</label>
                                <input type="text" class="form-control" id="confirmUsername" name="confirmUsername" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Zmień nazwę użytkownika</button>
                        </form>
                    </div>
                </div>

                <!-- Formularz zmiany hasła -->
                <div class="card">
                    <div class="card-body">
                        <h2 class="card-title">Zmiana hasła</h2>

                        <div id="passwordAlertMessage" class="text-danger m-3"></div>
                        <div id="passwordSuccessMessage" class="text-success m3">Hasło zostało zmienione</div>

                        <form id="passwordForm" action="/password" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label for="currentPassword" class="form-label">Podaj obecne hasło</label>
                                <input type="password" class="form-control" id="currentPassword" name="currentPassword" required>
                            </div>
                            <div class="mb-3">
                                <label for="newPassword" class="form-label">Wprowadź nowe hasło</label>
                                <input type="password" class="form-control" id="newPassword" name="newPassword" required>
                            </div>
                            <div class="mb-3">
                                <label for="confirmPassword" class="form-label">Potwierdź nowe hasło</label>
                                <input type="password" class="form-control" id="confirmPassword" name="confirmPassword" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Zmień hasło</button>
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
        
        <script>
            $(document).ready(function () {
                $('#passwordAlertMessage').hide();
                $('#passwordSuccessMessage').hide();
                $('#usernamedAlertMessage').hide();
                $('#usernameSuccessMessage').hide();

                $('#passwordForm').on('submit', function (e) {
                    const currentPassword = $('#currentPassword').val();
                    const newPassword = $('#newPassword').val();
                    const confirmPassword = $('#confirmPassword').val();
    
                    if (newPassword === currentPassword) {
                        e.preventDefault();
                        $('#passwordAlertMessage').text('Nowe hasło nie może być takie samo jak poprzednie!');
                        $('#passwordAlertMessage').show();
                        // alert('Nowe hasło nie może być takie samo jak poprzednie');
                    }

                    if (newPassword !== confirmPassword) {
                        e.preventDefault();
                        $('#passwordAlertMessage').text('Nowe hasło i potwierdzenie muszą być takie same!');
                        $('#passwordAlertMessage').show();
                        // alert('Nowe hasło i potwierdzenie hasła muszą być takie same!');
                    }
                });
           
                $('#usernameForm').on('submit', function (e) {
                    const currentUsername = $('#currentUsername').val();
                    const newUsername = $('#newUsername').val();
                    const confirmUsername = $('#confirmUsername').val();
    
                    if (newUsername === currentUsername) {
                        e.preventDefault();
                        $('#usernameAlertMessage').text('Nowa nazwa użytkownika nie może być taka sama jak poprzednia!');
                        $('#usernameAlertMessage').show();
                        // alert('Nowa nazwa użytkownika nie może być taka sama jak poprzednia');
                    }

                    if (newUsername !== confirmUsername) {
                        $('#usernameAlertMessage').text('Nowa nazwa użytkownika i potwierdzenie nazwy użytkownika muszą być takie same!');
                        $('#usernameAlertMessage').show();
                        e.preventDefault();
                        // alert('Nowa nazwa użytkownika i potwierdzenie nazwy użytkownika muszą być takie same!');
                    }
                });
            });
        </script>
      

    </body>
</html>