@extends('layouts.app')
@section('title', 'Servicios')
@section('content')
<section class="hero compacto">
<div class="contenedor">
<p class="etiqueta">NUESTROS SERVICIOS</p>
<h1>Soluciones para el mantenimiento de tu vehículo</h1>
<p class="intro">Consulta el catálogo y los precios de referencia del taller.</p>
</div>
</section>
<section class="seccion">
<div class="contenedor">
<div class="titulo-fila">
<h2>Servicios disponibles</h2>
<div class="buscador" hidden data-buscador>
<label for="buscar">Buscar servicios</label>
<input aria-controls="lista-servicios" id="buscar" type="search" placeholder="Nombre del servicio" autocomplete="off">
<p class="ayuda-campo" id="resultado-busqueda" role="status" hidden></p>
</div>
</div>
<div class="tarjetas" id="lista-servicios">
@foreach($servicios as $servicio)
    <article class="tarjeta servicio" data-servicio="{{ mb_strtolower($servicio->nombre) }}">
        @php($imagen = ['Cambio de aceite' => 'CambioAceite.jpg', 'Cambio de frenos' => 'frenos.jpg', 'Alineación y balanceo' => 'alineacion.jpg'][$servicio->nombre] ?? null)
        @if($imagen)<img src="{{ asset('img/'.$imagen) }}" alt="{{ $servicio->nombre }}" loading="lazy">@else<div class="servicio-ilustracion" aria-hidden="true">AutoGest <span>+ cuidado</span>
</div>@endif
        <div class="servicio-texto">
<h3>{{ $servicio->nombre }}</h3>
<p>{{ $servicio->descripcion }}</p>
<p class="precio">{{ $servicio->precio > 0 ? 'RD$ '.number_format($servicio->precio, 2) : 'Precio a consultar' }}</p>
<a aria-label="Solicitar cita para {{ $servicio->nombre }}" href="{{ route('citas.create', ['servicio' => $servicio->id_servicio]) }}">Solicitar este servicio →</a>
</div>
    </article>
@endforeach
</div>
<p id="sin-resultados" class="vacio" @if($servicios->isNotEmpty()) hidden @endif>No se encontraron servicios.</p>
<p class="nota">Precios de referencia para el proyecto académico. La solicitud registra la cita; la atención se coordina con el taller.</p>
</div>
</section>
@endsection
