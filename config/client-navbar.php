<?php
/**
 * dane konfiguracyjne paska nawigacyjnego aplikacji użytkownika
 * wykorzystywane w komponencie resources/views/components/client-navbar.blage.php
 */
return [
    [
        'name' => 'Strona główna',
        'icon' => 'bi-house-door',
        'url' => '/',
    ],
    [
        'name' => 'Baza tras',
        'icon' => 'bi-table',
        'url' => '/baza',
    ],
    [
        'name' => 'Generator tras',
        'icon' => 'bi-toggles',
        'url' => '/generator',
    ],
    [
        'name' => 'Planer tras',
        'icon' => 'bi-geo-alt',
        'url' => '/planner',
    ],
    [
        'name' => 'Spacer wirtualny',
        'icon' => 'bi-signpost-2',
        'url' => '/spacer',
    ],
];