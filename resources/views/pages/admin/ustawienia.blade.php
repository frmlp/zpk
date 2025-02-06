@extends('layouts.admin')

@section('title', 'Ustawienia')

@section('content')
<h1 class="text-center mb-4">Zmień nazwę użytkownika lub hasło</h1>

    <!-- Formularz zmiany nazwy użytkownika -->
    <div class="card mb-4">
        <div class="card-body">
            <h2 class="card-title">Zmiana nazwy użytkownika</h2>
            
            {{-- <div id="messageWrapper" class="m-3"> --}}
                <div id="loginAlertMessage" class="alert alert-danger"></div>
                <div id="loginSuccessMessage" class="alert alert-success"></div>
            {{-- </div> --}}
            

            <form id="loginForm">
                @csrf
                <div class="mb-3">
                    <label for="currentLogin" class="form-label">Podaj obecną nazwę użytkownika</label>
                    <input type="text" class="form-control" id="currentLogin" name="current_name" required>
                </div>
                <div class="mb-3">
                    <label for="newLogin" class="form-label">Wprowadź nową nazwę użytkownika</label>
                    <input type="text" class="form-control" id="newLogin" name="name" required>
                </div>
                <div class="mb-3">
                    <label for="confirmLogin" class="form-label">Potwierdź nową nazwę użytkownika</label>
                    <input type="text" class="form-control" id="confirmLogin" name="name_confirmation" required>
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
            <div id="passwordSuccessMessage" class="alert alert-success"></div>

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
    <script src="../js/admin/admin-ustawienia.js"></script>
@endsection

