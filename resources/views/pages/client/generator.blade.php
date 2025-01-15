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
                                <option disabled selected>Wybierz długość trasy</option>
                                <option value="KM2-4">2 - 4 km</option>
                                <option value="KM5-7">5 - 7 km</option>
                                <option value="KM8-10">8 - 10 km</option>
                                <option value="KM11-13">11 - 13 km</option>
                                <option value="KM14-16">14 - 16 km</option>
                                <option value="KM17-19">17 - 20 km</option>
                            </select>
                            
                        </div>
                        <div class="mb-3">
                            <label class="form-label bg-body-secondary p-1 w-100" for="points">Liczba punktów kontrolnych:</label>
                            <select class="form-select" id="points" name="points" required>
                                <option value="" disabled selected>Wybierz liczbę punktów</option>
                                <option value="P4-6">4 - 6</option>
                                <option value="P7-9">7 - 9</option>
                                <option value="P10-12">10 - 12</option>
                                <option value="P13-15">13 - 15</option>
                                <option value="P16-18">16 - 18</option>
                                <option value="P19-21">19 - 21</option>
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
                    <button class="btn btn-success generate-btn">Wygeneruj nowe trasy</button>
                    <button id="change-parameters-btn" class="btn btn-success">Zmień parametry</button>
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
    <script src="js/generator.js" async></script>
@endsection