$(document).ready(function () {
    //Obsługa zdarzenia wysyłania formularza wylogowania.
    $('#logoutForm').on('submit', function (e) {
        e.preventDefault();

        $.ajax({
            url: '/logout',
            method: 'POST',
            data: $(this).serialize(),
            success: function () {
                window.location.href = '/login';
            },
            error: function (xhr) {
                alert('Wystąpił błąd podczas wylogowywania. Spróbuj ponownie.');
            }
        });
    });
});