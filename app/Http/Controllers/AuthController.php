<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function authenticate(Request $request): RedirectResponse
    {
        error_log('AuthController::authenticate()');
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required']
        ]);
        foreach($credentials as $k => $v) {
            error_log('key: ' . $k . '; value: ' . $v);
        }

        if(Auth::attempt($credentials)) {
            $request->session()->regenerate();

            return redirect()->intended('auth.main');
        }
        error_log('Auth::attempt() was false');
        return back()->withErrors([
            'email' => 'Złe dane'
        ])->onlyInput('email');
    }


    public function login(Request $request)
    {
        error_log('AuthController::login()');
        error_log($request->email);
        error_log($request->password);

        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $user = User::where('email', $request->email)->first();

        if(!$user) {
            throw ValidationException::withMessages([
                'email' => ['Podano nieprawidłowe dane logowania.']
            ]);
        }

        if(!Hash::check($request->password, $user->password)) {
            // error_log($request->password);
            // error_log($user->password);
            throw ValidationException::withMessages([
                'email' => ['Podano nieprawidłowe dane logowania...']
            ]);
        }

        $token = $user->createToken('api-token')->plainTextToken;



        return response()->json([
            'token' => $token
        ]);

    // ===================
        // $credentials = $request->validate('email', 'password');

        // error_log("AuthController");

        // if(Auth::attempt($credentials)) {
        //     return response()->json(['success' => true]);

        // }

        // return response()->json(['success' => false, 'message' => 'Nieprawidłowe dane']);
    }

   public function logout(Request $request) 
   {
        $request->user()->tokens()->delete();

        return response()->json([
            'message' => 'Logged out successfully'
        ]);


   }
}
