<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\GitHubAuthController;
use App\Http\Controllers\Auth\GoogleController;

Route::get('/', function () {
    return view('welcome');
});

// Rutes d'autenticació amb xarxes socials
Route::get('/login/github', [GitHubAuthController::class, 'redirectToGithub'])
    ->name('login.github');

Route::get('/auth/github/callback', [GitHubAuthController::class, 'handleGithubCallback'])
    ->name('github.callback');

Route::get('/login/google', [GoogleController::class, 'redirectToGoogle'])
    ->name('login.google');

Route::get('/auth/google/callback', [GoogleController::class, 'handleGoogleCallback'])
    ->name('google.callback');

// Només una ruta per dashboard amb tots els middlewares necessaris
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});

// Ruta per reenviar el correu de verificació
Route::post('/email/resend', function (Request $request) {
    if ($request->user()->hasVerifiedEmail()) {
        return redirect()->route('dashboard')->with('message', 'El teu correu ja està verificat.');
    }

    $request->user()->sendEmailVerificationNotification();

    return back()->with('message', 'S ha enviat un nou enllaç de verificació al teu correu.');
})->middleware(['auth', 'throttle:6,1'])->name('verification.resend');
