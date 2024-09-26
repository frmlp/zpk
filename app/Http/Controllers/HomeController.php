<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    function index(){
        return view('auth.main');
    }

    function page(){
        return view('auth.page1');
    }
}
