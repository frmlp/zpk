$(document).ready(function () {
    $('#loginForm').on('submit', function (e) {
        e.preventDefault(); // Zablokowanie domyślnego wysyłania formularza
        console.log("próba logowania");
        $.ajax({
            url: '/login',
            method: 'POST',
            data: $(this).serialize(), // Serializowanie danych formularza
            // headers: {
            //     'X-CSRF-TOKEN': $('input[name="_token"]').val() // Ustawienie tokenu CSRF
            // },
            success: function (response, status, xhr) {
                if (xhr.status === 200 || xhr.status === 201) {
                    window.location.href = '/admin/baza-tras'; // Przekierowanie po sukcesie
                } 
                
            },
            error: function (xhr) {
                
                alert('Nieprawidłowa nazwa użytkownika lub hasło.');
            }
                
        });
    });
});