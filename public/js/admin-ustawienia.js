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