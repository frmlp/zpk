<!DOCTYPE html>
<html lang="pl">
    <x-head />
    <body>
        <x-background />

        <div class="content">
            <div class="container-md">
                @yield('content')
            </div>
        </div>

        <div class="content mt-5">
            <div class="form-signin m-auto bg-light">
                    <form id="loginForm">
                        @csrf
                        <h1 class="mb-3">Zaloguj się:</h1>
                        <div class="form-floating mb-1">
                            <input class="form-control" type="name" id="name" name="name" required>
                            <label for="name">Nazwa użytkownika</label>
                        </div>
                        <div class="form-floating mb-1">
                            <input class="form-control" type="password" id="password" name="password" required>
                            <label for="password">Hasło</label>
                        </div>
                        
                        <button class="btn btn-primary w-100 mt-1" type="submit">Zaloguj</button>
                    </form>
            </div>
        </div>

        <x-scripts />
        <script src="js/admin-login.js" async></script>

    </body>
</html>