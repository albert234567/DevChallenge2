<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use Illuminate\Support\Str;

class GoogleController extends Controller
{
    public function redirectToGoogle()
    {
        Log::info('Començant redirecció a Google');
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            Log::info('Rebut callback de Google');
            $googleUser = Socialite::driver('google')->user();
            Log::info('Dades de Google:', ['email' => $googleUser->email]);
            
            // Buscar usuari per email
            $user = User::where('email', $googleUser->email)->first();
            
            if ($user) {
                Log::info('Usuari trobat a la BD');
                // Actualitzar google_id si no el té
                if (!$user->google_id) {
                    $user->google_id = $googleUser->id;
                    $user->save();
                    Log::info('Google ID actualitzat');
                }
            } else {
                Log::info('Creant nou usuari');
                // Crear nou usuari
                $user = User::create([
                    'name' => $googleUser->name,
                    'email' => $googleUser->email,
                    'google_id' => $googleUser->id,
                    'password' => bcrypt(Str::random(24)) // Contrasenya segura aleatòria
                ]);
                Log::info('Nou usuari creat');
            }

            // Inicia sessió amb l'usuari
            Auth::login($user);
            Log::info('Usuari autenticat, redirigint a dashboard');

            // Redirigir directament a /dashboard
            return redirect('/dashboard');
        } catch (\Exception $e) {
            Log::error('Error d\'autenticació de Google: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            
            return redirect('/login')->with('error', 'Error en l\'autenticació de Google. Comprova els logs per a més informació.');
        }
    }
}