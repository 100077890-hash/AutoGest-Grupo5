@extends('layouts.app')
@section('title', 'Solicitar cita')
@section('content')
<section class="hero compacto">
<div class="contenedor">
<p class="etiqueta">ATENCIÓN PARA TU VEHÍCULO</p>
<h1>Solicitar una cita</h1>
<p class="intro">Completa tus datos, el vehículo y el servicio que necesitas.</p>
</div>
</section>
<section class="seccion">
<div class="contenedor cita-layout">
<aside class="ayuda">
<h2>Una solicitud, tres pasos</h2>
<ol>
<li>Introduce tus datos de contacto.</li>
<li>Identifica tu vehículo.</li>
<li>Selecciona servicio, fecha y hora.</li>
</ol>
<p>Los campos marcados con * son obligatorios.</p>
<p>Si ya eres cliente, utiliza los mismos datos registrados. Para modificarlos, contacta al taller.</p>
<a href="{{ route('servicios') }}">Consultar los servicios →</a>
</aside>
<form class="panel" data-submit-form method="POST" action="{{ route('citas.store') }}">@csrf
<fieldset>
<legend>
<span>01</span> Datos del cliente</legend>
<div class="campos">
@include('partials.campo', ['name' => 'nombre', 'label' => 'Nombre', 'max' => 50, 'autocomplete' => 'given-name'])
@include('partials.campo', ['name' => 'apellido', 'label' => 'Apellido', 'max' => 50, 'autocomplete' => 'family-name'])
@include('partials.campo', ['name' => 'telefono', 'label' => 'Teléfono', 'type' => 'tel', 'max' => 20, 'autocomplete' => 'tel', 'help' => 'Ej.: 809-555-0100. Entre 7 y 15 dígitos.'])
@include('partials.campo', ['name' => 'correo', 'label' => 'Correo electrónico', 'type' => 'email', 'max' => 100, 'autocomplete' => 'email'])
</div>
</fieldset>
<fieldset>
<legend>
<span>02</span> Datos del vehículo</legend>
<div class="campos">
@include('partials.campo', ['name' => 'marca', 'label' => 'Marca', 'max' => 50])
@include('partials.campo', ['name' => 'modelo', 'label' => 'Modelo', 'max' => 50])
@include('partials.campo', ['name' => 'anio', 'label' => 'Año', 'type' => 'number', 'min' => 1900, 'maximum' => now()->year])
@include('partials.campo', ['name' => 'placa', 'label' => 'Placa', 'max' => 20, 'autocapitalize' => 'characters', 'help' => 'Letras, números y guiones. Ej.: A123456.'])
</div>
</fieldset>
<fieldset>
<legend>
<span>03</span> Servicio y cita</legend>
<div class="grupo-campo">
<label for="id_servicio">Servicio *</label>
<select id="id_servicio" name="id_servicio" required @error('id_servicio') aria-invalid="true" aria-describedby="error-id_servicio" @enderror>
<option value="">Selecciona un servicio</option>@foreach($servicios as $servicio)<option value="{{ $servicio->id_servicio }}" @selected((string) (is_scalar(old('id_servicio', request()->integer('servicio'))) ? old('id_servicio', request()->integer('servicio')) : '') === (string) $servicio->id_servicio)>{{ $servicio->nombre }}</option>@endforeach</select>@error('id_servicio')<p class="error-campo" id="error-id_servicio">{{ $message }}</p>@enderror</div>
<div class="campos">
@include('partials.campo', ['name' => 'fecha', 'label' => 'Fecha', 'type' => 'date', 'min' => now()->toDateString()])
@include('partials.campo', ['name' => 'hora', 'label' => 'Hora', 'type' => 'time'])
</div>
<div class="grupo-campo">
<label for="comentarios">Comentarios <span class="opcional">(opcional)</span>
</label>
<textarea id="comentarios" name="comentarios" rows="4" maxlength="2000" @error('comentarios') aria-invalid="true" aria-describedby="error-comentarios" @enderror>{{ is_string(old('comentarios')) ? old('comentarios') : '' }}</textarea>@error('comentarios')<p class="error-campo" id="error-comentarios">{{ $message }}</p>@enderror</div>
</fieldset>
<div class="acciones">
<button class="boton" type="submit" data-loading-text="Registrando cita…" @disabled($servicios->isEmpty())>Registrar cita</button>
<button class="boton secundario" type="reset">Restablecer campos</button>
</div>
<p class="nota">La solicitud quedará registrada. El taller coordinará la atención.</p>
</form>
</div>
</section>
@endsection
