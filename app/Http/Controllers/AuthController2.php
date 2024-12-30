<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

//kontroler odpowiedzialny zazarządzanie procesami logowaniai wylogowania admina
class AuthController2 extends Controller
{
    public function login()
    {   // przekierowanie na stronę logowania
        return view('admin.login');
    }
    
    public function loginPost(LoginRequest $request): RedirectResponse
    {   // uwierzytelnianie użytkownika
        $request->authenticate();

        $request->session()->regenerate();

        // przekierowanie na stronę /api/admin/zpk
        return redirect()->intended(route('admin.zpk', absolute: false));
    }
    
    public function logout(Request $request): RedirectResponse
    {   // wylogowanie użytkownika
        error_log('logout()');

        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        // przekierowanie na stronę logowania api/login
        return redirect('/login')->with('success', 'wylogowano');
    }


    // GGH TODO:
 
    public function register()
    {   // przekierowanie na stronę rejestracji użytkownika
        return view('admin.register'); // PF todo: widok formularza rejestracji
    }

    public function registerPost(Request $request)
    {   // rejestracja użytkownika

        // Tworzenie nowego użytkownika
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        // Logowanie użytkownika
        // Auth::login($user);

        // ggh ask: Przekierowanie na stronę? 
        // return redirect()->route('admin.zpk'); 
    }

    public function profile()
    {   // przekierowanie na strone edycji użytkownika
        return view('admin.profile'); // // PF todo: widok formularza edycji
    }
    
    // public function profileUpdate(Request $request)
    // {   // edycja użytkownika

    //     // Walidacja danych 
    //     $validatedData = $request->validate([
    //         'name' => 'required|string|max:255', 
    //         'password' => 'required|string|min:8',
    //     ]);

    //     // Pobierz aktualnie zalogowanego użytkownika
    //     $user = Auth::user();

    //     // Zaktualizuj dane użytkownika
    //     $user->update([
    //         // ... dane użytkownika ...
    //     ]);

    //     // ggh ask: czy mam zwracać widok czy tylko status zapytania?
    //     // Zwróć odpowiedź 
    //     return redirect()->route('profile')->with('success', 'Profil zaktualizowany!'); 
    // }

}
