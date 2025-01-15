@extends('layouts.client')

@section('title', 'Planer tras')

@section('content')
    <div class="row  border border-success border-4 rounded bg-light">
        <div class="col-12 col-lg-6">
            <div class="m-3">
                <div id="alertMessage" class="alert alert-danger mb-3"></div>
                <div  id="dropdown-container" class="mb-3">
                    
                </div>

                <button id="finish-btn" class="btn btn-success ">Zakończ</button>
                <button id="reset-btn" class="btn btn-outline-success ">Wyczyść</button>
            </div>
        </div>
        
        <div class="col-12 col-lg-6" id="map"></div>
    </div>

    {{-- Modal wyboru mapu podkładowej --}}
    <x-modals.mapChoice />
@endsection

@section('js_files')
    <script src="js/planner.js"></script>
@endsection