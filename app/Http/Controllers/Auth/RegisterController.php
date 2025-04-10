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
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
        ];
        
        // Asumiendo que tienes un método en el modelo User para verificar si se requiere contraseña
        if (method_exists(User::class, 'passwordRequired') && User::passwordRequired()) {
            $rules['password'] = 'required|string|min:8|confirmed';
        }
        
        $request->validate($rules);
        
        $userData = [
            'name' => $request->name,
            'email' => $request->email,
        ];
        
        if ($request->password) {
            $userData['password'] = Hash::make($request->password);
        }
        
        $user = User::create($userData);
        
        // Enviar email de verificación
        $user->sendEmailVerificationNotification();
        
        // Iniciar sesión y regenerar sesión para evitar hijacking
        Auth::login($user, true);
        $request->session()->regenerate();
        
        return redirect()->route('verification.notice')
                         ->with('success', 'Registre completat! Si us plau, verifiqueu el vostre correu electrònic.');
    }
}