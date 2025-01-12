$(document).ready(function () {
    $('#passwordAlertMessage').hide();
    $('#passwordSuccessMessage').hide();
    $('#usernameAlertMessage').hide();
    $('#usernameSuccessMessage').hide();

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
            url: '/password/change',     // Adres endpointu
            method: 'PUT',               // Metoda żądania
            data: formData,
            success: function (response) {
                // Jeśli odpowiedź ma status 200
                passwordChangeSuccess(response.message);
                
            },
            error: function (xhr) {
                // Jeśli odpowiedź ma inny status niż 200
                let message = xhr.responseJSON?.message || 'Wystąpił błąd';
                passwordChangeFailure(message);
                
            }
        });
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

function passwordChangeSuccess(message){
    $('#passwordSuccessMessage').text(message).show();
    $('#passwordAlertMessage').hide();
    $('#usernameAlertMessage').hide();
    $('#usernameSuccessMessage').hide();
    // Wyczyść formularz
    $('#passwordForm')[0].reset();
    
}
function passwordChangeFailure(message){
    $('#passwordAlertMessage').text(message).show();
    $('#passwordSuccessMessage').hide();
    $('#usernameAlertMessage').hide();
    $('#usernameSuccessMessage').hide();
    // Wyczyść formularz
    $('#passwordForm')[0].reset();
}
function usernameChangeSuccess(message){

}
function usernameChangefailure(message){
    
}

