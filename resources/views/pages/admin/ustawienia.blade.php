@extends('layouts.admin')

@section('title', 'Ustawienia')

@section('content')
<h1 class="text-center mb-4">Zmień nazwę użytkownika lub hasło</h1>

    <!-- Formularz zmiany nazwy użytkownika -->
    <div class="card mb-4">
        <div class="card-body">
            <h2 class="card-title">Zmiana nazwy użytkownika</h2>
            
            <div id="messageWrapper" class="m-3">
                <div id="usernameAlertMessage" class="alert alert-danger"></div>
                <div id="usernameSuccessMessage" class="alert alert-success">Nazwa użytkownika została zmieniona</div>
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

            <div id="passwordAlertMessage" class="alert alert-danger"></div>
            <div id="passwordSuccessMessage" class="alert alert-success">Hasło zostało zmienione</div>

            <form id="passwordForm" action="/password/change" method="POST">
                @csrf
                {{-- <input type="hidden" name="_method" value="PUT"> --}}
                <div class="mb-3">
                    <label for="currentPassword" class="form-label">Podaj obecne hasło</label>
                    <input type="password" class="form-control" id="currentPassword" name="current_password" required>
                </div>
                <div class="mb-3">
                    <label for="newPassword" class="form-label">Wprowadź nowe hasło</label>
                    <input type="password" class="form-control" id="newPassword" name="password" required>
                </div>
                <div class="mb-3">
                    <label for="confirmPassword" class="form-label">Potwierdź nowe hasło</label>
                    <input type="password" class="form-control" id="confirmPassword" name="password_confirmation" required>
                </div>
                <button type="submit" class="btn btn-primary">Zmień hasło</button>
            </form>
        </div>
    </div>
@endsection

@section('js_files')
    <script src="../js/admin-ustawienia.js"></script>
    <script src="../js/admin-logout.js"></script>
@endsection

