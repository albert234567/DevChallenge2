<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login'); // Crea la vista login.blade.php
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            // L'usuari ha iniciat sessió correctament
            return redirect()->intended('/home'); // Redirigeix a la vista d'inici
        }   

        // Si no, redirigeix de nou amb un missatge d'error
        return back()->withErrors([
            'email' => 'Les credencials són incorrectes.',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        return redirect('/login'); // Redirigeix a la pàgina de login
    }
}
