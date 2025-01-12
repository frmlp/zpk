$(document).ready(function () {
    $('#passwordAlertMessage').hide();
    $('#passwordSuccessMessage').hide();
    $('#loginAlertMessage').hide();
    $('#loginSuccessMessage').hide();

    $('#passwordForm').on('submit', function (event) {
        event.preventDefault();
        const currentPassword = $('#currentPassword').val();
        const newPassword = $('#newPassword').val();
        const confirmPassword = $('#confirmPassword').val();

        if (newPassword === currentPassword) {
            let message = 'Nowe hasło nie może być takie samo jak poprzednie!';
            passwordChangeFailure(message);
            return;
        }

        if (newPassword !== confirmPassword) {
            let message = 'Nowe hasło i potwierdzenie muszą być takie same!';
            passwordChangeFailure(message);
            return;
        }

        if(newPassword.length < 8) {
            let message = 'Hasło musi mieć minimum 8 znaków!'
            passwordChangeFailure(message);
            return;
        }

        // Pobranie danych z formularza
        let formData = $(this).serialize();

        // Wysłanie żądania AJAX
        $.ajax({
            url: '/password/change',
            method: 'PUT',
            data: formData,
            success: function (response) {
                passwordChangeSuccess(response.message);
                
            },
            error: function (xhr) {
                let message = xhr.responseJSON?.message || 'Wystąpił błąd';
                passwordChangeFailure(message);
                
            }
        });
    });

    $('#loginForm').on('submit', function (event) {
        event.preventDefault();
        const currentLogin = $('#currentLogin').val();
        const newLogin = $('#newLogin').val();
        const confirmLogin = $('#confirmLogin').val();

        if (newLogin === currentLogin) {
            let message = 'Nowa nazwa użytkownika nie może być taka sama jak poprzednia!';
            loginChangeFailure(message);
            return;
        }

        if (newLogin !== confirmLogin) {
            let message = 'Nowa nazwa użytkownika i potwierdzenie nazwy użytkownika muszą być takie same!';
            loginChangeFailure(message);
            return;
        }

        // Pobranie danych z formularza
        let formData = $(this).serialize();

        // Wysłanie żądania AJAX
        $.ajax({
            url: '/login/change',
            method: 'PUT',
            data: formData,
            success: function (response) {
                loginChangeSuccess(response.message);
                
            },
            error: function (xhr) {
                let message = xhr.responseJSON?.message || 'Wystąpił błąd';
                loginChangeFailure(message);
                
            }
        });
    });


});

function passwordChangeSuccess(message){
    $('#passwordSuccessMessage').text(message).show();
    $('#passwordAlertMessage').hide();
    $('#loginAlertMessage').hide();
    $('#loginSuccessMessage').hide();
    // Wyczyść formularz
    $('#passwordForm')[0].reset();
    
}
function passwordChangeFailure(message){
    $('#passwordAlertMessage').text(message).show();
    $('#passwordSuccessMessage').hide();
    $('#loginAlertMessage').hide();
    $('#loginSuccessMessage').hide();
    // Wyczyść formularz
    $('#passwordForm')[0].reset();
}
function loginChangeSuccess(message){
    $('#loginSuccessMessage').text(message).show();
    $('#loginAlertMessage').hide();
    $('#passwordSuccessMessage').hide();
    $('#passwordAlertMessage').hide();
    // Wyczyść formularz
    $('#loginForm')[0].reset();
}
function loginChangeFailure(message){
    $('#loginAlertMessage').text(message).show();
    $('#loginSuccessMessage').hide();
    $('#passwordSuccessMessage').hide();
    $('#passwordAlertMessage').hide();
    // Wyczyść formularz
    $('#loginForm')[0].reset();
}

