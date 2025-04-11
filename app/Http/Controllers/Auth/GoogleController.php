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
            // Obtenir les dades de l'usuari de Google
            $googleUser = Socialite::driver('google')->user();
            Log::info('Usuari de Google rebut', ['email' => $googleUser->email]);
            
            // Buscar usuari per email o google_id
            $user = User::where('email', $googleUser->email)
                        ->orWhere('google_id', $googleUser->id)
                        ->first();
            
            if ($user) {
                Log::info('S\'ha trobat un compte existent amb aquest correu', ['user_id' => $user->id]);
                
                // Comprovació si l'usuari ja té associat un google_id
                if (!$user->google_id) {
                    // Si no té, assignem el google_id
                    $user->google_id = $googleUser->id;
                    $user->save();
                    Log::info('Google ID associat al compte existent');
                } else {
                    Log::info('El compte ja tenia Google ID associat');
                }
            } else {
                Log::info('No s\'ha trobat cap compte amb aquest correu, creant un de nou');
                
                // Crear un nou usuari amb el google_id i les altres dades de Google
                $user = User::create([
                    'name' => $googleUser->name ?? $googleUser->email, // Nom de l'usuari
                    'email' => $googleUser->email, // Email de l'usuari
                    'google_id' => $googleUser->id, // Google ID
                    'password' => bcrypt(Str::random(16)) // Contrasenya aleatòria
                ]);
                Log::info('Nou usuari creat', ['user_id' => $user->id]);
            }
    
            // Inicia sessió amb l'usuari i regenera la sessió
            Auth::login($user, true); // true per "remember me"
            $request->session()->regenerate();
            Log::info('Sessió iniciada correctament', ['user_id' => $user->id]);
    
            // Redirigeix a la ruta protegida (dashboard)
            return redirect()->intended(route('dashboard'));
        } catch (\Exception $e) {
            // Log d'error si alguna cosa falla durant l'autenticació
            Log::error('Error d\'autenticació de Google: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
    
            // Redirigeix al login si hi ha error
            return redirect()->route('login')->with('error', 'Error en l\'autenticació de Google.');
        }
    }
}