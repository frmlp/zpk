// Funkcja inicjalizuje widok 
$(document).ready(function() {
    let tags = [];

    // Konfiguracja kolumn w tabeli
    let columnsConfig = [
        {width: '50%'},
        null,
        null
    ]

    // Definicja zachowań kolumn (wyłączenie sortowania dla kolumn 1 i 2)
    let columnDefsConfig = [
        { orderable: false, targets: [1, 2]}
    ];

    // Konfiguracja CSRF tokenów dla zapytań AJAX nieobsługiwanych jako formularz
    csrfAjaxSetup();

    // Pobranie danych o tagach z API
    getAdminTagsData()
        .then(function(result){
            tags = result.tags

            // Generowanie wierszy tabeli na podstawie danych tagów
            let rows = tags.map(tag => `
                <tr class="" data-id="${tag.id}" id="${tag.id}">
                    <td>${tag.name}</td>
                    <td><button data-id="${tag.id}" class="btn btn-warning btn-sm w-100 edit-btn">Edytuj</button></td>
                    <td><button data-id="${tag.id}" class="btn btn-danger btn-sm w-100 delete-btn">Usuń</button></td>
                </tr>
            `).join('');

            // Uzupełnienie tabeli danymi
            populateTable(rows, columnsConfig, columnDefsConfig);
        }).catch((xhr) => {
            const message = xhr.responseJSON?.message || 'Wystąpił błąd';
            alert(message);
        });

    // Obsługa kliknięcia przycisku "Nowy Tag"
    $('#newTagBtn').on('click', function() {
        $('#alertMessage').hide();
        $('#tagModalLabel').text('Nowy Tag');
        $('#tagForm').attr('action', '/admin/tags');
        $('#tagForm').attr('method', 'POST');
        $('#tagForm').find('input[name="_method"]').remove(); 

        $('#tagForm')[0].reset(); // Wyczyść formularz

        $('#pointsContainer').empty();
        $('#pointsContainerWrapper').hide();

        $('#tagModal').modal('show');
        
    });

    // Obsługa przesyłania formularza tagów
    $('#tagForm').on('submit', function(event) {
        event.preventDefault();

        const form = $(this);
        const actionUrl = form.attr('action');
        const formData = form.serialize();

        $.ajax({
            url: actionUrl,
            method: form.attr('method'),
            data:formData,
            success: function(response, status, xhr) {
                location.reload(); 
            },
            error: function(xhr) {
                const message = xhr.responseJSON?.message || 'Wystąpił błąd. Spróbuj ponownie.';
                $('#alertMessage').text(message).show();
            }
        })

    });

    // Obsługa kliknięcia przycisku "Edytuj"
    $('#table').on('click', '.edit-btn', function() {
        $('#alertMessage').hide();

        const id = $(this).data('id');
        const tag = tags.find(tag => tag.id === id);
        

        $('#tagForm').attr('action', '/admin/tags/' + tag.id);
        $('#tagForm').attr('method', 'POST');
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
    
    // Obsługa kliknięcia przycisku "Usuń"
    $('#table').on('click', '.delete-btn', function(event) {
        
        event.preventDefault();

        let id = $(this).data('id');
        const deleteUrl = `/admin/tags/${id}`;
        const confirmMessage = 'Czy na pewno chcesz usunąć tę ścieżkę?';

        if (!confirm(confirmMessage)) {
            return;
        }

        $.ajax({
            url: deleteUrl,
            type: 'DELETE',
            success: function(response) {
                alert('Usunięto pomyślnie');
                location.reload();
            },
            error: function(xhr) {
                const message = xhr.responseJSON?.message || 'Wystąpił błąd podczas usuwania.';
                alert(message);
            }
            
        })
    });

})