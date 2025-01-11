@extends('layouts.client')

@section('title', 'Generator tras')

@section('content')
    <div class="row border border-success border-4 rounded bg-light">
        <div class="col-12 col-md-6">
            <div class="m-3">
                <div id="form-wrapper">
                    <form id="generator-form">
                        <div class="mb-4">
                            <label class="form-label " for="start-point">Punkt startu trasy:</label>
                            <select class="form-select dropdown-points dropdown-start" id="start-point" required>

                            </select>
                        </div>
                        <div class="mb-4">
                            <label class="form-label" for="end-point">Punkt końca trasy:</label>
                            <select class="form-select dropdown-points dropdown-end" id="end-point" required>

                            </select>
                        </div>
                        <div class="mb-4">
                            <label class="form-label" for="distance">Długość trasy:</label>
                            <div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="distance" id="KM2-4" value="KM2-4" required >
                                    <label class="form-check-label" for="KM2-4">2 - 4 km</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="distance" id="KM5-7" value="KM5-7" required>
                                    <label class="form-check-label" for="KM5-7">5 - 7 km</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="distance" id="KM8-10" value="KM8-10" required>
                                    <label class="form-check-label" for="KM8-10">8 - 10 km</label>
                                </div>
                            </div>
                        </div>
                        <div class="mb-4">
                            <label class="form-label" for="points">Liczba punktów kontrolnych:</label>
                            <div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="points" id="P4-6" value="P4-6" required>
                                    <label class="form-check-label" for="P4-6">4 - 6</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="points" id="P7-9" value="P7-9" required>
                                    <label class="form-check-label" for="P7-9">7 - 9</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="points" id="P10-12" value="P10-12" required>
                                    <label class="form-check-label" for="P10-12">10 - 12</label>
                                </div>
                            </div>
                        </div>
                        <button class="btn btn-success generate-btn" type="submit" >Generuj trasy</button>
                    </form>
                </div>

                <div id="table-wrapper" style="display: none;">
                    <table class="table table-hover" id="table" >
                        <thead>
                            <tr>
                                <!-- <th>Przebieg</th> -->
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
                    <button class="btn btn-success generate-btn">Wygeneruj nowe trasy</button>
                    <button id="change-parameters-btn" class="btn btn-success">Zmień parametry</button>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6 sticky-top" id="map"></div>
    </div>

    {{-- Modal wyboru mapu podkładowej --}}
    <x-modals.mapChoice />
@endsection

@section('js_files')
<script src="js/map.js" async></script>
<script src="js/map-pdf.js" async></script>
<script src="js/helpers.js" async></script>
<script src="js/table.js" async></script>
<script src="js/data.js" async></script>
<script src="js/generator.js" async></script>
@endsection