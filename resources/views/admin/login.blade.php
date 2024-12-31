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
        <link href='https://cdn.datatables.net/2.1.2/css/dataTables.dataTables.min.css' rel='stylesheet'>
        <link href='https://cdn.datatables.net/2.1.2/css/dataTables.bootstrap5.css' rel='stylesheet'>
        <!-- <link href='https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/css/bootstrap.min.css' rel='stylesheet'> -->
        <!-- LEAFLET -->
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
        <!-- PDF-LIB -->
        <script src="https://unpkg.com/pdf-lib"></script>
        <!-- PROJ4JS -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/proj4js/2.11.0/proj4.min.js"></script>
        <link rel="stylesheet" href="css/style.css"/>
        
        
    </head>
    <body>

        <div class="background">
            <span></span>
            <span></span>
            <span></span>
            <span></span>
            <span></span>
            <span></span>
            <span></span>
            <span></span>
        </div>

        <div class="content mt-5">
            <div class="form-signin m-auto bg-light">
                <!-- <main class="form-signin w-100 m-auto"> -->
                    <form method="POST" action="/login">
                        @csrf
                        <!-- <img class="mb-4" src="../assets/brand/bootstrap-logo.svg" alt="" width="72" height="57"> -->
                        <h1 class="mb-3">Zaloguj się:</h1>
                        <div class="form-floating mb-1">
                            <input class="form-control" type="text" id="name" name="name">
                            <label for="name">Adres email1</label>
                        </div>
                        <div class="form-floating mb-1">
                            <input class="form-control" type="password" id="password" name="password">
                            <label for="password">Hasło</label>
                        </div>
                        <!-- <div class="form-check text-start my-3">
                            <input class="form-check-input" type="checkbox" value="remember-me" id="flexCheckDefault">
                            <label class="form-check-label" for="flexCheckDefault">
                            Remember me
                            </label>
                        </div> -->
                        <button class="btn btn-primary w-100 mt-1" type="submit">Zaloguj</button>
                        <!-- <p class="mt-5 mb-3 text-body-secondary">&copy; 2017–2024</p> -->
                    </form>
                <!-- </main> -->
            </div>
        </div>



        

      
      








        <!-- JQUERY -->
        <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
        <!-- BOOTSTRAP -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
        <!-- DATATABLES -->
        <script src="https://cdn.datatables.net/2.1.2/js/dataTables.min.js"></script>
        <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script> -->
        <script src="https://cdn.datatables.net/2.1.2/js/dataTables.bootstrap5.js"></script>
        <script>
            // $(document).ready(function() {
            //     $.ajax({
            //         url: 'token',
            //         type: 'GET',
            //         dataType: 'json',
            //         success: function(response) {
            //             console.log(response.token);

            //             $('form').append('<input type="hidden" name="_token" value="' + response.token + '">');
            //         },
            //         error: function(error){
            //             console.log("Błąd: ", error);
            //         }
            //     })

            // })
        </script>
    </body>
</html>