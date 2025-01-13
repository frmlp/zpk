@extends('layouts.client')

@section('title', 'Baza tras')

@section('content')
    <div class="row  border border-success border-4 rounded bg-light">
        <div class="col-12 col-lg-7 ">
            <div class="mt-3 mb-3">
                <table class="table table-hover" id="table">
                    <thead>
                        <tr>
                            <th>Trasa</th>
                            <th>Obszar</th>
                            <th>Liczba punktów</th>
                            <th>Długość trasy</th>
                            <th>Typ trasy</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody id="tbody">

                    </tbody>
                </table>
            </div>
        </div>
        <div class="col-12 col-lg-5 sticky-top" id="map"></div>
    </div>

    {{-- Modal wyboru mapu podkładowej --}}
    <x-modals.mapChoice />
@endsection

@section('js_files')
    <script src="js/baza-tras.js"></script>
@endsection