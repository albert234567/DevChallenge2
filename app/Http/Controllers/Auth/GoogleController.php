<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class GoogleController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            
            // Busca o crea un usuari amb aquest email
            $user = User::firstOrCreate([
                'email' => $googleUser->email
            ], [
                'name' => $googleUser->name,
                'google_id' => $googleUser->id,
                'password' => bcrypt(uniqid()) // Genera una contrasenya aleatòria
            ]);

            // Inicia sessió amb l'usuari
            Auth::login($user);

            return redirect('/dashboard'); // Canvia a la ruta que vulguis
        } catch (\Exception $e) {
            // Gestiona possibles errors
            return redirect('/login')->with('error', 'Error en l\'autenticació de Google');
        }
    }
}