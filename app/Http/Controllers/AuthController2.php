<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;


//kontroler odpowiedzialny zazarządzanie procesami logowaniai wylogowania admina
class AuthController2 extends Controller
{

    public function login()
    {  
        // przekieruj do panelu admina jeśli użytkownik już zalogowany
        if(Auth::check()) {
            return redirect('/admin/baza-tras');
        }
        // przekierowanie na stronę logowania
        return view('pages.admin.login');
    }
    
    public function loginPost(LoginRequest $request)
    {   // uwierzytelnianie użytkownika

        $request->authenticate();
        $request->session()->regenerate();

        return response()->json(['message' => 'Uzytkownik zostal poprawnie zalogowany.'], 200);
    }
    
    public function logout(Request $request)
    {   // wylogowanie użytkownika
        
        error_log('logout()');

        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json(['message' => 'Uzytkownik zostal pomyslnie wylogowany.'], 200);
    }

    public function registerPost(Request $request)
    {   // rejestracja użytkownika

        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255|unique:users',
                'password' => 'required|string|min:8|confirmed',
            ]);
        
            $validator->validate();
            $user = User::create([
                'name' => $request->name,
                'password' => Hash::make($request->password),
                'name_verified_at' => now(),
            ]);

            return response()->json([
                'message' => 'Użytkownik zostal poprawnie zarejestrowany.',
                'user.name' => $user->name,
            ], 200);

        } catch (ValidationException $e) {
            $errors = $e->errors();
            if (isset($errors['name'])) {
                return response()->json([
                    'message' => 'Bląd walidacji nazwy uzytkownika.',
                    'errors' => $errors['name'],
                ], 422);
            }

            if (isset($errors['password'])) {
                return response()->json([
                    'message' => 'Blad walidacji hasla.',
                    'errors' => $errors['password'],
                ], 422);
            }
        }    
    }

    public function destroy(User $user)
    {   // usuwanie użytkownika

        if ($user->id === Auth::id()) {
            return response()->json([
                'message' => 'Nie mozesz usunac swojego wlasnego konta.',
            ], 403); 
        }

        $user->delete();
    
        return response()->json(['message' => 'Uzytkownik zostal usuniety.'], 200); 
    }

    public function updatePassword(Request $request)
    {   // zmiana hasła
        try {
            $request->validate([
                'current_password' => ['required', 'current_password'],
                'password' => ['required', 'confirmed', 'min:8', 'string'],
            ]);
    
            $request->user()->update([
                'password' => Hash::make($request->password),
            ]);
    
            return response()->json(['message' => 'Hasło zostało zaktualizowane.'], 200);

        } catch (ValidationException $e){
            // Błąd walidacji
            return response()->json([
                'message' => 'Błąd walidacji danych.',
                'errors' => $e->errors(),
            ], 422);

        } catch(Exception $error) {
            // Pozostałe błędy
            return response()->json([
                'message' => 'Wystąpił błąd przy próbie zmiany hasła.'
            ], 500);
        }
    
        
    }

    public function updateLogin(Request $request)
    {   // zmiana loginu
        try {
            $request->validate([
                'current_name' => ['required', 'string', function ($attribute, $value, $fail) {
                    if ($value !== Auth::user()->name) {
                        $fail('Podana obecna nazwa użytkownika jest nieprawidłowa.');
                    }
                }],
                'name' => ['required', 'confirmed', 'string', 'max:255', 'different:current_name', 'unique:users,name,' . Auth::id()],
            ]);
        
            $request->user()->update([
                'name' => $request->name,
            ]);
        
            return response()->json(['message' => 'Nazwa użytkownika została zaktualizowana.'], 200);
        }  catch (ValidationException $e){
            // Błąd walidacji
            return response()->json([
                'message' => 'Błąd walidacji danych.',
                'errors' => $e->errors(),
            ], 422);

        } catch(Exception $error) {
            // Pozostałe błędy
            return response()->json([
                'message' => 'Wystąpił błąd przy próbie zmiany nazwy użytkownika.'
            ], 500);
        }
        
    }
}
