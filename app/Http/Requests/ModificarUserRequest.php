<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class ModificarUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        //Solo si esta autentificado, permitira la petición de patch
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    //Reglas de validación
    {
        return [
            'nombre' => ['required', 'string', 'max:50', 'regex:/^[a-zA-ZÀ-ÖØ-öø-ÿ\s]*$/u'],
            'apellidos' => ['required', 'string', 'max:50', 'regex:/^[a-zA-ZÀ-ÖØ-öø-ÿ\s]*$/u'],
            'numero_telefono' => ['required', 'string', 'digits:9', 'numeric'],
            'direccion' => ['nullable', 'string', 'max:150'],
            'ciudad_id' => ['required', 'integer', 'exists:ciudades,id'],
            'codigo_postal' => ['required', 'string', 'digits:5', 'numeric'],
        ];
    }

    public function messages(): array{

        return [
            'nombre.required' => 'El nombre es obligatorio.',
            'nombre.string' => 'El nombre debe ser una cadena de texto.',
            'nombre.max' => 'El nombre no debe exceder los :max caracteres.',
            'nombre.regex' => 'El nombre solo debe contener letras, espacios.',

            'apellidos.required' => 'Los apellidos son obligatorios.',
            'apellidos.string' => 'Los apellidos deben ser una cadena de texto.',
            'apellidos.max' => 'Los apellidos no deben exceder los :max caracteres.',
            'apellidos.regex' => 'Los apellidos solo deben contener letras, espacios.',

            'numero_telefono.required' => 'El número de teléfono es obligatorio.',
            'numero_telefono.string' => 'El número de teléfono debe ser una cadena de texto.',
            'numero_telefono.digits' => 'El número de teléfono debe tener exactamente :digits dígitos.',
            'numero_telefono.numeric' => 'El número de teléfono solo debe contener números.',

            'direccion.string' => 'La dirección debe ser una cadena de texto.',
            'direccion.max' => 'La dirección no debe exceder los :max caracteres.',

            'ciudad_id.required' => 'Debes seleccionar una ciudad.',
            'ciudad_id.integer' => 'El valor de la ciudad no es válido.',
            'ciudad_id.exists' => 'La ciudad seleccionada no es válida.', // Importante para validar que el ID existe

            'codigo_postal.required' => 'El código postal es obligatorio.',
            'codigo_postal.string' => 'El código postal debe ser una cadena de texto.',
            'codigo_postal.digits' => 'El código postal debe tener exactamente :digits dígitos.',
            'codigo_postal.numeric' => 'El código postal solo debe contener números.',
        ];
    }
}
