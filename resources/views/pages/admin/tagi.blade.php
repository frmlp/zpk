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
    <script src="../js/admin/admin-tagi.js"></script>
@endsection