@extends('layouts.admin')

@section('title', 'Punkty kontrolne')

@section('content')
<div class="row">
    <div id="table-wrapper" class="col-12 col-lg-7">
        <button id="newPointBtn" class="btn btn-success w-100">Dodaj nowy punkt kontrolny</button>
        <table class="table table-hover" id="table">
            <thead>
                <tr>
                    <!-- <th>ID</th> -->
                    <th data-priority="3">Kod</th>
                    <th data-priority="6">Opis</th>
                    <th data-priority="7">Obszar</th>
                    <th data-priority="4">Easting</th>
                    <th data-priority="4">Northing</th>
                    <th data-priority="2"></th>
                    <th data-priority="1"></th>
                </tr>
            </thead>
            <tbody>

            </tbody>
        </table>
    </div>

    <div class="col-12 col-lg-5 sticky-top" id="map"></div>
</div>

    {{-- Modal formularza trasy --}}
    <x-modals.pointForm />
@endsection

@section('js_files')
    <script src="../js/map.js"></script>
    <script src="../js/map-pdf.js"></script>
    <script src="../js/helpers.js"></script>
    <script src="../js/table.js"></script>
    <script src="../js/data.js"></script>
    <script src="../js/admin-punkty-kontrolne.js"></script>
    <script src="../js/admin-logout.js"></script>
@endsection

