<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class GoogleController extends Controller
{
    public function redirectToGoogle()
    {
        Log::info('Començant redirecció a Google');
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback(Request $request)
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            Log::info('Usuari de Google rebut', ['email' => $googleUser->email]);
            
            // Buscar usuari per email o google_id
            $user = User::where('email', $googleUser->email)
                        ->orWhere('google_id', $googleUser->id)
                        ->first();
            
            if ($user) {
                Log::info('S\'ha trobat un compte existent amb aquest correu', ['user_id' => $user->id]);
                // Actualitzar google_id si no el té
                if (!$user->google_id) {
                    $user->google_id = $googleUser->id;
                    $user->save();
                    Log::info('Google ID associat al compte existent');
                } else {
                    Log::info('El compte ja tenia Google ID associat');
                }
            } else {
                Log::info('No s\'ha trobat cap compte amb aquest correu, creant un de nou');
                // Crear nou usuari amb fallback pel nom i contrasenya aleatòria
                $user = User::create([
                    'name' => $googleUser->name ?? $googleUser->email,
                    'email' => $googleUser->email,
                    'google_id' => $googleUser->id,
                    'password' => bcrypt(Str::random(16)) // Afegir contrasenya aleatòria
                ]);
                Log::info('Nou usuari creat', ['user_id' => $user->id]);
            }
    
            // Inicia sessió amb l'usuari i regenera la sessió
            Auth::login($user, true); // true per "remember me"
            $request->session()->regenerate();
            Log::info('Sessió iniciada correctament', ['user_id' => $user->id]);
    
            // Utilitzar intended per si l'usuari intentava accedir a una ruta protegida
            return redirect()->intended(route('dashboard'));
        } catch (\Exception $e) {
            // Log més simple però més informatiu
            Log::error('Error d\'autenticació de Google: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            
            return redirect()->route('login')->with('error', 'Error en l\'autenticació de Google.');
        }
    }
}