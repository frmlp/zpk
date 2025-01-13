@extends('layouts.client')

@section('title', 'Strona Główna')

@section('content')
    <h2>Zielony punkt kontrolny UMG</h2>
    <hr class="border border-success border-2 opacity-75">
    <p class="fs-5">
        Uniwersytet Morski w Gdyni, we współpracy z Nadleśnictwem Gdańsk, stworzył sieć stałych punktów kontrolnych w formie dębowych słupków, rozmieszczonych na obszarze ok. 9 km2 w lasach Gdyni (Zwierzyniec i Cisowa). Punkty kontrolne są częścią koncepcji "Zielony Punkt Kontrolny", które umożliwiają naukę i ćwiczenie nawigacji, orientacji sportowej, turystycznej i rekreacyjnej. Dzięki szczegółowym mapom można rozwijać umiejętności czytania mapy, aktywnie spędzać czas na świeżym powietrzu oraz podziwiać przyrodę przez cały rok.
    </p>
    <hr class="border border-success border-2 opacity-75">
    <div id="buttonsGeneral" class="text-center">
        <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-4 g-3">
            <div class="col d-flex">
                <button class="btn btn-success btn-lg w-100" data-bs-toggle="modal" data-bs-target="#modalRules">Na czym to polega?</button>
            </div>
            <div class="col d-flex">
                <button class="btn btn-success btn-lg w-100" data-bs-toggle="modal" data-bs-target="#modalTermsOfUse">Regulamin</button>
            </div>
            <div class="col d-flex">
                <button class="btn btn-success btn-lg w-100" data-bs-toggle="modal" data-bs-target="#modalCertificate">Certyfikat ZPK</button>
            </div>
            <div class="col d-flex">
                <button class="btn btn-success btn-lg w-100" data-bs-toggle="modal" data-bs-target="#modalContact">Kontakt</button>
            </div>
            
        </div>
        <hr class="border border-success border-2 opacity-75">
        <div id="buttonsMaps" class="text-center">
            <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-4 g-3">
                <div class="col d-flex">
                    <button class="btn btn-success btn-lg w-100" data-bs-toggle="modal" data-bs-target="#modalChylonia">Mapa "Chylonia"</button>
                </div>
                <div class="col d-flex">
                    <button class="btn btn-success btn-lg w-100" data-bs-toggle="modal" data-bs-target="#modalGrabowek">Mapa "Grabówek"</button>
                </div>
                <div class="col d-flex">
                    <button class="btn btn-success btn-lg w-100" data-bs-toggle="modal" data-bs-target="#modalChyloniaGrabowek">Mapa "Chylonia + Grabówek"</button>
                </div>
                <div class="col d-flex">
                    <button class="btn btn-success btn-lg w-100" data-bs-toggle="modal" data-bs-target="#modalLegend">Legenda</button>
                </div>
            </div>
        </div>
        <hr class="border border-success border-2 opacity-75">
        <div id="buttonsOthers" class="text-center">
            <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-4 g-3">
                <div class="col d-flex">
                    <button class="btn btn-success btn-lg w-100" data-bs-toggle="modal" data-bs-target="#modalStartCard">Karta startowa</button>
                </div>
                <div class="col d-flex">
                    <button class="btn btn-success btn-lg w-100" data-bs-toggle="modal" data-bs-target="#modalPunchCodes">Kody perforatorów</button>
                </div>
                <div class="col d-flex">
                    <button class="btn btn-success btn-lg w-100" data-bs-toggle="modal" data-bs-target="#modalSymbols">Znaki kartograficzne BnO</button>
                </div>
                <div class="col d-flex">
                    <button class="btn btn-success btn-lg w-100" data-bs-toggle="modal" data-bs-target="#modalPictograms">Opis piktogramów</button>
                </div>
            </div>
        </div>
    </div>
        

    {{-- Modale obsługujące przyciski strony głównej --}}
    <x-modals.rules />
    <x-modals.termsOfUse />
    <x-modals.certificate />
    <x-modals.contact />
    <x-modals.mapChylonia />
    <x-modals.mapGrabowek />
    <x-modals.mapChyloniaGrabowek />
    <x-modals.legend />
    <x-modals.startCard />
    <x-modals.punchCodes />
    <x-modals.symbols />
    <x-modals.pictograms />
@endsection

