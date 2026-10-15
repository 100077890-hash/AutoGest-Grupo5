@extends('layouts.app')
@section('title', 'Inicio')
@section('content')
<section class="hero">
<div class="contenedor hero-contenido">
<div>
    <p class="etiqueta">GESTIÓN AUTOMOTRIZ</p>
    <h1>Una forma organizada de gestionar tu taller</h1>
    <p class="intro">Clientes, vehículos, servicios y citas en un solo lugar. AutoGest facilita la atención y la organización del taller.</p>
    <div class="acciones">
<a class="boton" href="{{ route('citas.create') }}">Solicitar una cita <span aria-hidden="true">→</span>
</a>
<a class="boton secundario" href="{{ route('servicios') }}">Ver servicios</a>
</div>
</div>
<figure class="imagen-principal">
<img src="{{ asset('img/CambioAceite.jpg') }}" alt="Mantenimiento de un vehículo en el taller">
<figcaption>Cuidado y mantenimiento para tu vehículo</figcaption>
</figure>
</div>
</section>
<section class="seccion">
<div class="contenedor">
<p class="etiqueta">EL PROYECTO</p>
<h2>¿Qué es AutoGest?</h2>
<p class="intro">Un sistema web diseñado para centralizar la información de un taller automotriz y facilitar su administración mediante una interfaz clara y organizada.</p>
<div class="tarjetas beneficios">
<article class="tarjeta">
<span class="numero">01</span>
<h3>Organización</h3>
<p>Información de clientes y vehículos vinculada a cada cita.</p>
</article>
<article class="tarjeta">
<span class="numero">02</span>
<h3>Facilidad de gestión</h3>
<p>Registro, consulta y actualización de clientes desde el panel del taller.</p>
</article>
<article class="tarjeta">
<span class="numero">03</span>
<h3>Atención práctica</h3>
<p>Consulta los servicios y solicita una cita desde cualquier dispositivo.</p>
</article>
</div>
</div>
</section>
<section class="seccion alternativa">
<div class="contenedor">
<div class="titulo-fila">
<div>
<p class="etiqueta">MANTENIMIENTO Y REPARACIÓN</p>
<h2>Principales servicios</h2>
</div>
<a href="{{ route('servicios') }}">Consultar servicios →</a>
</div>
<ul class="lista-servicios">@forelse($servicios as $servicio)<li>{{ $servicio->nombre }}</li>@empty<li>No hay servicios disponibles.</li>@endforelse</ul>
</div>
</section>
<section class="seccion">
<div class="contenedor llamada">
<div>
<h2>¿Necesitas un servicio para tu vehículo?</h2>
<p>Elige un servicio y envíanos tu solicitud de cita.</p>
</div>
<a class="boton" href="{{ route('citas.create') }}">Solicitar cita</a>
</div>
</section>
@endsection
