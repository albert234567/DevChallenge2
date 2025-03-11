<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    public function showRegistrationForm()
    {
        return view('auth.register'); // Necessitaràs una vista de registre
    }

    public function register(Request $request)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
        ];
        
        // Només validem contrasenya si és un registre manual
        if (User::passwordRequired()) {
            $rules['password'] = 'required|string|min:8|confirmed';
        }
        
        $request->validate($rules);
        
        $userData = [
            'name' => $request->name,
            'email' => $request->email,
        ];
        
        // Només afegim contrasenya si existeix
        if ($request->password) {
            $userData['password'] = Hash::make($request->password);
        }
        
        $user = User::create($userData);
        
        // 📩 Enviar email de verificació
        $user->sendEmailVerificationNotification();
        
        Auth::login($user);
        
        return redirect()->route('verification.notice');
    }
}
