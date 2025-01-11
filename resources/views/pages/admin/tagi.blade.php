@extends('layouts.admin')

@section('title', 'Tagi')

@section('content')
    <button id="newTagBtn" class="btn btn-success w-100">Dodaj nowy tag</button>
    <table class="table table-hover" id="table">
        <thead>
            <tr>
                <!-- <th>ID</th> -->
                
                <th>Nazwa</th>
                <th></th>
                <th></th>
            </tr>
        </thead>
        <tbody>

        </tbody>
    </table>


    {{-- Modal formularza tagu --}}
    <x-modals.tagForm />
@endsection

@section('js_files')
    <script src="../js/map.js"></script>
    <script src="../js/map-pdf.js"></script>
    <script src="../js/helpers.js"></script>
    <script src="../js/table.js"></script>
    <script src="../js/data.js"></script>
    <script src="../js/admin-tagi.js"></script>
    <script src="../js/admin-logout.js"></script>
@endsection