<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class GitHubAuthController extends Controller
{
    /**
     * Redirigeix l'usuari a la pàgina d'autorització de GitHub
     */
    public function redirectToGithub()
    {
        return Socialite::driver('github')->redirect();
    }

    /**
     * Gestiona la resposta de callback de GitHub
     */
    public function handleGithubCallback()
    {
        try {
            $githubUser = Socialite::driver('github')->user();
            
            // Busca o crea l'usuari
            $user = User::firstOrCreate(
                ['email' => $githubUser->email],
                [
                    'name' => $githubUser->name ?? $githubUser->nickname,
                    'email' => $githubUser->email,
                    'github_id' => $githubUser->id,
                    'password' => bcrypt(Str::random(16)), // Genera contrasenya aleatòria
                    'avatar' => $githubUser->avatar,
                ]
            );

            // Actualitza el github_id si ja existeix
            if (!$user->github_id) {
                $user->update(['github_id' => $githubUser->id]);
            }

            // Inicia sessió
            Auth::login($user);

            return redirect()->intended('/dashboard');
        } catch (\Exception $e) {
            // Gestiona possibles errors
            return redirect('/login')->with('error', 'Error en iniciar sessió amb GitHub');
        }
    }
}
