<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Mail\VerifyEmail;
use Illuminate\Support\Facades\Mail;
// Si tu regla letraDNI está en App\Rules, no necesitas importarla explícitamente
// a menos que esté en un subdirectorio de App\Rules.
// use App\Rules\letraDNI;

class RegisterController extends Controller
{
    public function registrar(Request $request)
    {
        $recaptchaResponse = $request->input('g-recaptcha-response');

        if (empty($recaptchaResponse)) {
            return redirect()->route('principal')
                ->withErrors(['recaptcha_registro' => 'Por favor, completa el desafío reCAPTCHA.'], 'registro')
                ->withInput();
        }

        $verificationUrl = 'https://www.google.com/recaptcha/api/siteverify';
        $response = Http::asForm()->post($verificationUrl, [
            'secret' => env('RECAPTCHA_SECRET_KEY'),
            'response' => $recaptchaResponse,
            'remoteip' => $request->ip(),
        ]);

        $recaptchaResult = $response->json();

        if (!isset($recaptchaResult['success']) || !$recaptchaResult['success']) {
            return redirect()->route('principal')
                ->withErrors(['recaptcha_registro' => 'La verificación reCAPTCHA falló. Inténtalo de nuevo.'], 'registro')
                ->withInput();
        }

        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:191|unique:users,email',
            'email_confirmation' => 'required|string|email|same:email',
            'password' => 'required|string|min:8|confirmed',
        ], [
            'email.required' => 'El campo email es obligatorio.',
            'email.email' => 'El email debe ser una dirección de correo válida.',
            'email.max' => 'El email no puede tener más de 191 caracteres.',
            'email.unique' => 'Este email ya ha sido registrado.',
            'email_confirmation.required' => 'El campo de confirmación de email es obligatorio.',
            'email_confirmation.same' => 'El email y su confirmación no coinciden.',
            'password.required' => 'El campo contraseña es obligatorio.',
            'password.min' => 'La contraseña debe tener al menos :min caracteres.',
            'password.confirmed' => 'La contraseña y su confirmación no coinciden.',
        ]);

        if ($validator->fails()) {
            return redirect()->route('principal') // O la ruta que muestra el modal de registro
                ->withErrors($validator, 'registro') // Usar el error bag 'registro'
                ->withInput();
        }

        $user = User::create([
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password')),
            'email_verification_token' => Str::uuid(), // Genera un UUID único
            'id_descuento' => 2, // O null si no hay descuento por defecto
            'tipo_usuario' => 3, // O el ID correspondiente al tipo de usuario por defecto
        ]);

        $user->save();

        Mail::to($user->email)->send(new VerifyEmail($user));

        return redirect()->route('principal') // Redirige a una página de aviso
                    ->with('success', '¡Registro exitoso! Por favor, revisa tu correo electrónico para verificar tu cuenta.');
    }
}