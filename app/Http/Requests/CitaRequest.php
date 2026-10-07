<?php

namespace App\Http\Requests;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CitaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        foreach (['nombre', 'apellido', 'telefono', 'correo', 'marca', 'modelo', 'placa', 'comentarios'] as $campo) {
            if (is_string($this->input($campo))) {
                $this->merge([$campo => trim($this->input($campo))]);
            }
        }
        foreach (['correo' => 'mb_strtolower', 'placa' => 'mb_strtoupper'] as $campo => $transformar) {
            if (is_string($this->input($campo))) {
                $this->merge([$campo => $transformar($this->input($campo))]);
            }
        }
    }

    public function rules(): array
    {
        return [
            'nombre' => ['required', 'string', 'max:50'],
            'apellido' => ['required', 'string', 'max:50'],
            'telefono' => ['required', 'string', 'max:20', 'regex:/^(?=(?:\D*\d){7,15}\D*$)[\d\s()+-]+$/'],
            'correo' => ['required', 'email', 'max:100'],
            'marca' => ['required', 'string', 'max:50'],
            'modelo' => ['required', 'string', 'max:50'],
            'anio' => ['required', 'integer', 'between:1900,'.now()->year],
            'placa' => ['required', 'string', 'max:20', 'regex:/^[A-Z0-9-]+$/'],
            'id_servicio' => ['required', 'integer', Rule::exists('servicios', 'id_servicio')->where('activo', true)],
            'fecha' => ['required', 'date_format:Y-m-d', 'after_or_equal:today'],
            'hora' => ['required', 'date_format:H:i'],
            'comentarios' => ['nullable', 'string', 'max:2000'],
        ];
    }

    public function after(): array
    {
        return [function ($validator) {
            if (! $validator->errors()->hasAny(['fecha', 'hora']) &&
                Carbon::parse($this->input('fecha').' '.$this->input('hora'))->isPast()) {
                $validator->errors()->add('hora', 'Selecciona una fecha y hora futuras.');
            }
        }];
    }
}
