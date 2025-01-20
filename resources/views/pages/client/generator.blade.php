@extends('layouts.client')

@section('title', 'Generator tras')

@section('content')
    <div class="row border border-success border-4 rounded bg-light">
        <div class="col-12 col-lg-6">
            <div class="m-3">
                <div id="form-wrapper">
                    <form id="generatorForm">
                        {{-- @csrf --}}
                        <div class="mb-3">
                            <label class="form-label  bg-body-secondary p-1 w-100" for="start-point">Punkt startu trasy:</label>
                            <select class="form-select dropdown-points dropdown-start" id="start-point" required>

                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label bg-body-secondary p-1 w-100" for="end-point">Punkt końca trasy:</label>
                            <select class="form-select dropdown-points dropdown-end" id="end-point" required>

                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label bg-body-secondary p-1 w-100" for="distance">Długość trasy:</label>
                            <select class="form-select" id="distance" name="distance" required>
                                <option value="" disabled selected>Wybierz długość trasy</option>
                                <option value="KM2-5">2 - 5 km</option>
                                <option value="KM6-9">4 - 9 km</option>
                                <option value="KM10-12">10 - 12 km</option>
                                <option value="KM13-15">13 - 15 km</option>
                                <option value="KM16-18">16 - 18 km</option>
                                <option value="KM19-24">19 - 24 km</option>
                            </select>
                            
                        </div>
                        <div class="mb-3">
                            <label class="form-label bg-body-secondary p-1 w-100" for="points">Liczba punktów kontrolnych:</label>
                            <select class="form-select" id="points" name="points" required>
                                <option value="" disabled selected>Wybierz liczbę punktów</option>
                                <option value="P3-5">3 - 5</option>
                                <option value="P6-8">6 - 8</option>
                                <option value="P9-11">9 - 11</option>
                                <option value="P12-14">12 - 14</option>
                                <option value="P15-17">15 - 17</option>
                                <option value="P18-21">18 - 21</option>
                            </select>
                           
                        </div>

                        <!-- Checkboxy dla areas -->
                        <div class="mb-3">
                            <label class="form-label bg-body-secondary p-1 w-100" for="areas">Obszary:</label>
                            <div id="areas-list" class="form-check checkbox-group">

                            </div>
                        </div>

                        <!-- Przełącznik dla virtualpoints -->
                        <div class="mb-3">
                            <label class="form-label bg-body-secondary p-1 w-100" for="virtualpoints">Punkty wirtualne:</label>
                            <div class="form-check form-switch m-3">
                                <input class="form-check-input" type="checkbox" id="virtualpoints" checked>
                                <label class="form-check-label" for="virtualpoints">Uwzględnij punkty wirtualne</label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <button id="tag-btn" class="btn btn-outline-success w-100" type="button">Wybór dodatkowych tagów</button>
                        </div>
                        <button id="generate-btn" class="btn btn-success w-100" type="submit" >Generuj trasy</button>
                    </form>
                </div>
                <div class="d-flex justify-content-center" >
                    <div id="loading-spinner" class="spinner-grow text-success m-5" role="status" style="display: none;">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>

                <div id="alertMessage" style="display: none;">
                    <div class="alert alert-danger mb-3">Nie udało się wygenerować trasy</div>
                    <button class="btn btn-success re-generate-btn">Wygeneruj nowe trasy</button>
                    <button class="btn btn-success change-parameters-btn">Zmień parametry</button>
                </div>
                

                <div id="table-wrapper" style="display: none;">
                    <table class="table table-hover" id="table" >
                        <thead>
                            <tr>
                                <!-- <th>Przebieg</th> -->
                                <th>Obszar</th>
                                <th>Liczba punktów</th>
                                <th>Długość trasy</th>
                                {{-- <th>Typ trasy</th> --}}
                                <th></th>
                            </tr>
                        </thead>
                        <tbody id="tbody">
                        </tbody>
                    </table>
                    <button class="btn btn-success re-generate-btn">Wygeneruj nowe trasy</button>
                    <button class="btn btn-success change-parameters-btn">Zmień parametry</button>
                </div>
            </div>
        </div>
        <div class="col-12 col-lg-6 sticky-top" id="map"></div>
    </div>

    {{-- Modal wyboru mapu podkładowej --}}
    <x-modals.mapChoice />
    {{-- Modal wyboru tagów --}}
    <x-modals.tagChoice />
@endsection

@section('js_files')
    <script src="js/client/generator.js" async></script>
@endsection