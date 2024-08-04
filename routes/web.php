<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('index');
});

Route::get('/baza', function () {
    return view('baza-tras');
});

Route::get('/generator', function () {
    return view('generator');
});

Route::get('/planner', function () {
    return view('planner-tras');
});

