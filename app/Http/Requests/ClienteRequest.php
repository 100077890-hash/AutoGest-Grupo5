<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ClienteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    protected function prepareForValidation(): void
    {
        foreach (['nombre', 'apellido', 'telefono', 'correo'] as $campo) {
            if (is_string($this->input($campo))) {
                $valor = trim($this->input($campo));
                $this->merge([$campo => $campo === 'correo' ? mb_strtolower($valor) : $valor]);
            }
        }
    }

    public function rules(): array
    {
        return [
            'nombre' => ['required', 'string', 'max:50'],
            'apellido' => ['required', 'string', 'max:50'],
            'telefono' => ['required', 'string', 'max:20', 'regex:/^(?=(?:\D*\d){7,15}\D*$)[\d\s()+-]+$/'],
            'correo' => ['required', 'email', 'max:100', Rule::unique('clientes', 'correo')->ignore($this->route('cliente'))],
        ];
    }
}
