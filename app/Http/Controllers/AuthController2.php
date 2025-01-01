<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
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
                'message' => 'Użytkownik został poprawnie zarejestrowany.',
                'user.name' => $user->name,
            ], 200);

        } catch (ValidationException $e) {
            $errors = $e->errors();
            if (isset($errors['name'])) {
                return response()->json([
                    'message' => 'Błąd walidacji nazwy użytkownika.',
                    'errors' => $errors['name'],
                ], 422);
            }

            if (isset($errors['password'])) {
                return response()->json([
                    'message' => 'Błąd walidacji hasła.',
                    'errors' => $errors['password'],
                ], 422);
            }
        }    
    }

    public function destroy(User $user)
    {   // usuwanie użytkownika

        if ($user->id === Auth::id()) {
            return response()->json([
                'message' => 'Nie możesz usunąć swojego własnego konta.',
            ], 403); 
        }

        $user->delete();
    
        return response()->json(['message' => 'Użytkownik został usunięty.'], 200); 
    }

    public function updatePassword(Request $request)
    {   // zmiana hasła
        
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', 'min:8', 'string'],
        ]);

        $request->user()->update([
            'password' => Hash::make($request->password),
        ]);

        return response()->json(['message' => 'Hasło zostało zaktualizowane.'], 200);
    }

}
