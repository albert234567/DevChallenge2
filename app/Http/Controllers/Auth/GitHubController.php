<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;

class GitHubController extends Controller
{
    public function redirectToGithub()
    {
        return Socialite::driver('github')->redirect();
    }

    public function handleGithubCallback()
    {
        try {
            // Obtiene los datos del usuario de GitHub
            $githubUser = Socialite::driver('github')->stateless()->user();
        } catch (\Exception $e) {
            return redirect('login/github'); // Redirigir si hay un error
        }

        // Verifica si el usuario ya existe
        $user = User::where('email', $githubUser->getEmail())->first();

        if (!$user) {
            // Si no existe, crea uno nuevo
            $user = User::create([
                'name' => $githubUser->getName(),
                'email' => $githubUser->getEmail(),
                'password' => null, // No se requiere contraseña
            ]);
        }

        // Autentica al usuario
        Auth::login($user, true);
        return redirect()->intended('/home'); // Redirige a la página de inicio
    }
}
