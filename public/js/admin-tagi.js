$(document).ready(function() {
    let tags = [];

    let columnsConfig = [
        {width: '50%'},
        null,
        null
    ]

    let columnDefsConfig = [
        { orderable: false, targets: [1, 2]}
    ];

    csrfAjaxSetup();

    getTagData()
        .then(function(result){
            tags = result.tags

            let rows = tags.map(tag => `
                <tr class="" data-id="${tag.id}" id="${tag.id}">
                    <td>${tag.name}</td>
                    <td><button data-id="${tag.id}" class="btn btn-warning btn-sm w-100 edit-btn">Edytuj</button></td>
                    <td><button data-id="${tag.id}" class="btn btn-danger btn-sm w-100 delete-btn">Usuń</button></td>
                </tr>
            `).join('');

            populateTable2(rows, columnsConfig, columnDefsConfig);
        }).catch((error) => console.log(error));

    $('#newTagBtn').on('click', function() {
        $('#tagModalLabel').text('Nowy Tag');
        $('#tagForm').attr('action', '/admin/tags');
        $('#tagForm').attr('method', 'POST'); // Metoda POST do tworzenia nowego punktu
        $('#tagForm').find('input[name="_method"]').remove(); 

        $('#tagForm')[0].reset(); // Wyczyść formularz

        $('#pointsContainer').empty();
        $('#pointsContainerWrapper').hide();

        $('#tagModal').modal('show');
        
    });

    $('#tagForm').on('submit', function(event) {
        event.preventDefault();

        const form = $(this);
        const actionUrl = form.attr('action'); // Pobierz URL akcji
        const formData = form.serialize(); // Serializuj dane formularza

        $.ajax({
            url: actionUrl,
            method: form.attr('method'),
            data:formData,
            success: function(response, status, xhr) {
                if (xhr.status === 200 || xhr.status === 201) {
                    location.reload(); // Odśwież stronę
                } else {
                    $('#error-message').text('Wystąpił nieoczekiwany błąd. Spróbuj ponownie.');
                }
            },
            error: function(xhr) {
                const errorMessage = xhr.responseJSON?.message || 'Wystąpił błąd. Spróbuj ponownie.';
                $('#error-message').text(errorMessage).show();
            }
        })

    });

    $('#table').on('click', '.edit-btn', function() {
        const id = $(this).data('id');
        const tag = tags.find(tag => tag.id === id);
        

        $('#tagForm').attr('action', '/admin/tags/' + tag.id);
        $('#tagForm').attr('method', 'POST'); // Ustawienie metody POST, a Laravel obsłuży PUT przez ukryte pole _method
        $('#tagForm').append('<input type="hidden" name="_method" value="PUT">');

        $('#tagModalLabel').text('Edytuj Tag');
        $('#name').val(tag.name);

        $('#pointsContainerWrapper').show();
        const pointsContainer = $('#pointsContainer');
        pointsContainer.empty();

      // Dodanie każdego punktu jako osobny element w kolumnie
        tag.points.forEach(point => {
            pointsContainer.append(`<div class="card bg-success-subtle m-1"><div class="card-body">${point}</div></div>`);
        });

        $('#tagModal').modal('show');
    });
    
    $('#table').on('click', '.delete-btn', function(event) {
        
        event.preventDefault();

        let id = $(this).data('id');
        const deleteUrl = `/admin/tags/${id}`; // Endpoint do usunięcia
        const confirmMessage = 'Czy na pewno chcesz usunąć tę ścieżkę?';

        // Potwierdzenie akcji użytkownika
        if (!confirm(confirmMessage)) {
            return;
        }

        $.ajax({
            url: deleteUrl,
            type: 'DELETE',
            success: function(response) {
                // Obsługa sukcesu (np. odświeżenie listy)
                alert('Usunięto pomyślnie');
                location.reload(); // Odśwież stronę
            },
            error: function(xhr) {
                // Obsługa błędu
                const errorMessage = xhr.responseJSON?.message || 'Wystąpił błąd podczas usuwania.';
                alert(errorMessage);
            }
            
        })
    });

    
    // $('#logout-btn').on('click', function(event){
    //     event.preventDefault();
    //     console.log('logout button clicked');
    //     // console.log(token);
    //     $.ajax({
    //         url: 'http://localhost:8000/logout',
    //         type: 'POST',
    //         data: {
    //             _token: token,
    //         },
    //         success: function(response) {
    //             // alert('Wylogowano');
    //             console.log(response);
    //         },
    //         error: function(xhr, status, error) {
    //             console.log('error');
    //         }
    //     })
    // });

})