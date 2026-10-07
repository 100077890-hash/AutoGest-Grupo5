@extends('layouts.app')
@section('title', 'Iniciar sesión')
@section('content')
<section class="seccion">
<div class="contenedor estrecho">
<p class="etiqueta">ACCESO AL TALLER</p>
<h1>Iniciar sesión</h1>
<p class="intro">Administra clientes y consulta las citas registradas.</p>
<form class="panel" method="POST" action="{{ route('login.store') }}">@csrf
    @include('partials.campo', ['name' => 'usuario', 'label' => 'Usuario', 'max' => 50, 'autocomplete' => 'username'])
    @include('partials.campo', ['name' => 'password', 'label' => 'Contraseña', 'type' => 'password', 'max' => 255, 'autocomplete' => 'current-password'])
    <button class="boton ancho" type="submit">Iniciar sesión</button>
</form>
</div>
</section>
@endsection
