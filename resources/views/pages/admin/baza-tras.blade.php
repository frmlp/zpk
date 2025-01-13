@extends('layouts.admin')

@section('title', 'Baza tras')

@section('content')
    <div class="row ">
        <div class="col-12 col-lg-7">
            <!-- <div class=""> -->
                <button id="newPathBtn" class="btn btn-success w-100">Dodaj nową trasę</button>
                <table class="table table-hover" id="table">
                    <thead>
                        <tr>
                            <th>Nazwa</th>
                            <th>Obszar</th>
                            <th>Liczba punktów</th>
                            <th>Długość trasy</th>
                            <th>Typ trasy</th>
                            <th></th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            <!-- </div> -->
        </div>
        <div class="col-12 col-lg-5 sticky-top" id="map"></div>
    </div>

    {{-- Modal formularza trasy --}}
    <x-modals.pathForm />
@endsection

@section('js_files')
    <script src="../js/admin-baza-tras.js"></script>
@endsection

