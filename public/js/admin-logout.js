$(document).ready(function () {
    $('#logoutForm').on('submit', function (e) {
        e.preventDefault(); // Zablokowanie domyślnego wysłania formularza

        $.ajax({
            url: '/logout',
            method: 'POST',
            data: $(this).serialize(),
            success: function () {
                window.location.href = '/login'; // Przekierowanie po pomyślnym wylogowaniu
            },
            error: function (xhr) {
                alert('Wystąpił błąd podczas wylogowywania. Spróbuj ponownie.');
            }
        });
    });
});