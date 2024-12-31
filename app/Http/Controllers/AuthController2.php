<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

//kontroler odpowiedzialny zazarządzanie procesami logowaniai wylogowania admina
class AuthController2 extends Controller
{
    public function login()
    {   // przekierowanie na stronę logowania
        return view('admin.login');
    }
    
    public function loginPost(LoginRequest $request)
    {   // uwierzytelnianie użytkownika

        $request->authenticate();
        $request->session()->regenerate();

        return response()->json(['message' => 'Użytkownik został poprawnie zalogowany.'], 200);
    }
    
    public function logout(Request $request)
    {   // wylogowanie użytkownika
        
        error_log('logout()');

        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json(['message' => 'Użytkownik został pomyślnie wylogowany.'], 200);
    }

    public function registerPost(RegisterRequest $request)
    {   // rejestracja użytkownika

        $user = User::create([
            'name' => $request->name,
            'password' => Hash::make($request->password),
            'name_verified_at' => now(),
        ]);

        return response()->json([
            'message' => 'Użytkownik został poprawnie zarejestrowany.',
            'user.name' => $user->name,
        ], 200);
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
