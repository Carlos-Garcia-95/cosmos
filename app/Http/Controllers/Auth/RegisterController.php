<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Rules\letraDNI;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;


class RegisterController extends Controller
{

    public function registrar(Request $request)
    {
        $recaptchaResponse = $request->input('g-recaptcha-response');

        if (empty($recaptchaResponse)) {
            return redirect()->route('principal')
                ->withErrors(['recaptcha' => 'Por favor, completa el desafío reCAPTCHA.'], 'registro')
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
                ->withErrors(['recaptcha' => 'La verificación reCAPTCHA falló. Inténtalo de nuevo.'], 'registro')
                ->withInput();
        }

        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|unique:users,email,regex:/^[^<>]*$/',
            'email_confirmation' => 'required|string|email|same:email',
            'password' => 'required|string|min:8|confirmed',
            'nombre' => 'required|string|max:255|alpha',
            'apellidos' => 'required|string|max:255|alpha',
            'direccion' => 'required|string|max:255|regex:/^[a-zA-Z0-9\s\/\-#.,]*$/',
            'ciudad' => 'required',
            'codigo_postal' => 'required|string|max:5|min:5',
            'telefono' => 'required|digits:9',
            'dni' => ['required','regex:/^\d{8}[A-Za-z]$/','unique:users,dni', new letraDNI],
            'mayor_edad' => 'required|accepted',
            'fecha_nacimiento' => 'required|date',
        ]);

        if ($validator->fails()) {
            return redirect()->route('principal')
            ->withErrors($validator, 'registro')
            ->withInput();
        }

        User::create([
            'nombre' => $request->nombre,
            'apellidos' => $request->apellidos,
            'email' => $request->email,
            'fecha_nacimiento' => $request->fecha_nacimiento,
            'numero_telefono' => $request->telefono,
            'dni' => $request->dni,
            'direccion' => $request->direccion,
            'ciudad' => $request->ciudad,
            'codigo_postal' => $request->codigo_postal,
            'password' => Hash::make($request->password),
            'mayor_edad' => $request->mayor_edad === 'on' ? true : false,
            'id_descuento' => 2,
            'tipo_usuario' => 3
        ]);

        return redirect()->route('principal')
            ->with('success', '¡Registro completado con éxito!');
    }
}

