<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController2 extends Controller
{
    public function login()
    {
        error_log('AuthenticatedSessionController::login()');
        return view('admin.login');
    }

    public function loginPost(LoginRequest $request): RedirectResponse
    {
        // error_log('loginPost()');
        // $credetials = [
        //     'email' => $request->email,
        //     'password' => $request->password,
        // ];
        // if (Auth::attempt($credetials)) {
        //     return redirect('/admin/zpk')->with('success', 'udane logowanie');
        // }
        // return back()->with('error', 'niepoprawny email lub hasło');

        error_log('AuthenticatedSessionController::store()');
        $request->authenticate();

        $request->session()->regenerate();

        return redirect()->intended(route('admin.zpk', absolute: false));
        

    }

    public function logout(Request $request): RedirectResponse
    {
        // error_log('logout()');
        // // Auth::logout();
        // Auth::guard('web')->logout();

        // $request->session()->invalidate();
    
        // $request->session()->regenerateToken();

        // return redirect()->route('login');
        // // return redirect('/login');
        error_log('logout()');

        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/login')->with('success', 'wylogowano');
        

    }

}
