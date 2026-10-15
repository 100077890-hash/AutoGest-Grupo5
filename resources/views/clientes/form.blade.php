@extends('layouts.app')
@section('title', $cliente->exists ? 'Editar cliente' : 'Nuevo cliente')
@section('content')
<section class="seccion">
<div class="contenedor formulario-ancho">
<a href="{{ route('clientes.index') }}">← Volver a clientes</a>
<p class="etiqueta espacio">GESTIÓN DEL TALLER</p>
<h1>{{ $cliente->exists ? 'Editar cliente' : 'Nuevo cliente' }}</h1>
<p>Los campos marcados con * son obligatorios.</p>
<form class="panel" data-submit-form method="POST" action="{{ $cliente->exists ? route('clientes.update', $cliente) : route('clientes.store') }}">@csrf @if($cliente->exists) @method('PUT') @endif
<div class="campos">
    @include('partials.campo', ['name' => 'nombre', 'label' => 'Nombre', 'value' => $cliente->nombre, 'max' => 50, 'autocomplete' => 'given-name'])
    @include('partials.campo', ['name' => 'apellido', 'label' => 'Apellido', 'value' => $cliente->apellido, 'max' => 50, 'autocomplete' => 'family-name'])
    @include('partials.campo', ['name' => 'telefono', 'label' => 'Teléfono', 'type' => 'tel', 'value' => $cliente->telefono, 'max' => 20, 'autocomplete' => 'tel', 'help' => 'Ej.: 809-555-0100. Entre 7 y 15 dígitos.'])
    @include('partials.campo', ['name' => 'correo', 'label' => 'Correo electrónico', 'type' => 'email', 'value' => $cliente->correo, 'max' => 100, 'autocomplete' => 'email'])
</div>
<div class="acciones">
<button class="boton" type="submit" data-loading-text="Guardando cliente…">{{ $cliente->exists ? 'Guardar cambios' : 'Registrar cliente' }}</button>
<a class="boton secundario" href="{{ route('clientes.index') }}">Cancelar</a>
</div>
</form>
</div>
</section>
@endsection
