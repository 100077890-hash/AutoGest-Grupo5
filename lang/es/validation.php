<?php

return [
    'required' => 'El campo :attribute es obligatorio.',
    'string' => 'El campo :attribute debe ser texto.',
    'email' => 'Introduce un correo electrónico válido.',
    'max' => ['string' => 'El campo :attribute no debe superar :max caracteres.'],
    'integer' => 'El campo :attribute debe ser un número entero.',
    'between' => ['numeric' => 'El campo :attribute debe estar entre :min y :max.'],
    'regex' => 'El formato de :attribute no es válido.',
    'unique' => 'El :attribute ya está registrado.',
    'exists' => 'El servicio seleccionado no está disponible.',
    'date_format' => 'El formato de :attribute no es válido.',
    'after_or_equal' => 'Selecciona una fecha de hoy o posterior.',
    'attributes' => ['nombre' => 'nombre', 'apellido' => 'apellido', 'telefono' => 'teléfono', 'correo' => 'correo', 'anio' => 'año', 'id_servicio' => 'servicio', 'usuario' => 'usuario', 'password' => 'contraseña'],
];
