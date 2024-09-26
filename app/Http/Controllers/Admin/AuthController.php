<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function authenticate(Request $request)
    {
        $credetials = [
            'email' => $request->email,
            'password' => $request->password,
        ];
        if (Auth::attempt($credetials)) {
            error_log('authenticated');
            return redirect('/admin/home')->with('success', 'Udane logowanie');
        }
        return back()->with('error', 'Podane email lub hasło są nieprawidłowe');
    }
}
