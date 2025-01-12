@extends('layouts.client')

@section('title', 'Planer tras')

@section('content')
    <div class="row  border border-success border-4 rounded bg-light">
        <div class="col-12 col-lg-6">
            <div class="m-3">
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
<script src="js/map.js"></script>
<script src="js/map-pdf.js"></script>
<script src="js/helpers.js"></script>
<script src="js/table.js"></script>
<script src="js/data.js"></script>
<script src="js/planner.js"></script>
@endsection