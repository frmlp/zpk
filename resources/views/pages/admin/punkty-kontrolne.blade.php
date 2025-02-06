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
                    <th>Kod</th>
                    <th>Opis</th>
                    <th>Obszar</th>
                    <th>Easting</th>
                    <th>Northing</th>
                    <th></th>
                    <th></th>
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
    <script src="../js/admin/admin-punkty-kontrolne.js"></script>
@endsection

