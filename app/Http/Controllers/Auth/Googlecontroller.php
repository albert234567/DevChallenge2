<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User; 
use Illuminate\Support\Facades\Auth;
use Exception;

class Googlecontroller extends Controller
{
    // Redirige al usuario a la página de Google para autenticarse
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    // Maneja la respuesta de Google después de la autenticación
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();

            // Busca al usuario por su correo
            $user = User::where('email', $googleUser->getEmail())->first();

            // Si no existe, crea un nuevo usuario
            if (!$user) {
                $user = User::create([
                    'name' => $googleUser->getName(),
                    'email' => $googleUser->getEmail(),
                    'google_id' => $googleUser->getId(),
                    'password' => bcrypt('password'), // Una contraseña por defecto
                ]);
            }

            // Inicia sesión al usuario
            Auth::login($user);

            // Redirige a la página de inicio después de loguearse
            return redirect()->intended('/home');
        } catch (Exception $e) {
            // Manejar errores en caso de que haya un problema
            return redirect('/login')->withErrors(['msg' => 'Hubo un error al iniciar sesión con Google']);
        }
    }
}
