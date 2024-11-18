<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

//kontroler odpowiedzialny zazarządzanie procesami logowaniai wylogowania admina
class AuthController2 extends Controller
{
    // przekierowanie na stronę logowania
    public function login()
    {
        return view('admin.login');
    }

    // przetwarzenie danych podanych do logowania
    public function loginPost(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        // przekierowanie na stronę /api/admin/zpk
        return redirect()->intended(route('admin.zpk', absolute: false));

    }

    // obsługa procesu wylogowania
    public function logout(Request $request): RedirectResponse
    {
        
        error_log('logout()');

        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        // przekierowanie na stronę logowania api/login
        return redirect('/login')->with('success', 'wylogowano');
        

    }

}
