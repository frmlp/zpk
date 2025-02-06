$(document).ready(function () {
    // Obsługa zdarzenia wysyłania formularza logowania.
    $('#loginForm').on('submit', function (e) {
        e.preventDefault();
        $.ajax({
            url: '/login',
            method: 'POST',
            data: $(this).serialize(),

            success: function (response, status, xhr) {
                if (xhr.status === 200 || xhr.status === 201) {
                    window.location.href = '/admin/baza-tras';
                }    
            },
            error: function (xhr) {  
                alert('Nieprawidłowa nazwa użytkownika lub hasło.');
            }
                
        });
    });
});