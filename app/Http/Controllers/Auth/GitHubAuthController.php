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

            // Comprovar si l'usuari ja existeix per email
            $user = User::where('email', $githubUser->email)->first();

            if ($user) {
                // Si ja existeix, simplement iniciem sessió
                if (!$user->github_id) {
                    $user->github_id = $githubUser->id;
                    $user->save();
                }

                Auth::login($user);
                return redirect()->route('dashboard');
            }

            // Si no existeix, creem un nou usuari
            $user = User::create([
                'name' => $githubUser->name ?? $githubUser->nickname,
                'email' => $githubUser->email,
                'github_id' => $githubUser->id,
            ]);

            Auth::login($user);
            return redirect()->route('dashboard');
        } catch (\Exception $e) {
            return redirect()->route('login')->with('error', 'S\'ha produït un error amb l\'autenticació de GitHub: ' . $e->getMessage());
        }
    }
}