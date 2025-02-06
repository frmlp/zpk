<?php
/**
 * dane konfiguracyjne paska nawigacyjnego aplikacji administratora
 * wykorzystywane w komponencie resources/views/components/admin-navbar.blage.php
 */

return [
    
    [
        'name' => 'Baza tras',
        'icon' => 'bi-table',
        'url' => '/admin/baza-tras',
    ],
    [
        'name' => 'Punkty kontrolne',
        'icon' => 'bi-geo',
        'url' => '/admin/zpk',
    ],
    [
        'name' => 'Tagi',
        'icon' => 'bi-tags',
        'url' => '/admin/tagi',
    ],
    [
        'name' => 'Ustawienia',
        'icon' => 'bi-gear',
        'url' => '/admin/ustawienia',
    ],
];