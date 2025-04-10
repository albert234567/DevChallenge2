<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Http\Request;

class GitHubAuthController extends Controller
{
    public function redirectToGithub()
    {
        return Socialite::driver('github')->redirect();
    }

    public function handleGithubCallback(Request $request)
    {
        try {
            $githubUser = Socialite::driver('github')->user();

            // Comprovar si l'usuari ja existeix per email o github_id
            $user = User::where('email', $githubUser->email)
                        ->orWhere('github_id', $githubUser->id)
                        ->first();

            if ($user) {
                // Si ja existeix, actualitzem el github_id si és necessari
                if (!$user->github_id) {
                    $user->github_id = $githubUser->id;
                    $user->save();
                }
            } else {
                // Si no existeix, creem un nou usuari
                $user = User::create([
                    'name' => $githubUser->name ?? $githubUser->nickname,
                    'email' => $githubUser->email,
                    'github_id' => $githubUser->id,
                    'password' => bcrypt(Str::random(16)), // Contraseña aleatoria
                ]);
            }

            // Login del usuario y regeneración de sesión
            Auth::login($user, true); // true para "remember me"
            $request->session()->regenerate();
            
            return redirect()->intended(route('dashboard'));
        } catch (\Exception $e) {
            \Log::error('Error en autenticación de GitHub: ' . $e->getMessage());
            return redirect()->route('login')->with('error', 'S\'ha produït un error amb l\'autenticació de GitHub: ' . $e->getMessage());
        }
    }
}