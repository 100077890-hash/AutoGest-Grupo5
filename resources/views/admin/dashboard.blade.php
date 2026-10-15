@extends('layouts.app')
@section('title', 'Panel del taller')
@section('content')
<section class="seccion">
<div class="contenedor">
<div class="titulo-fila">
<div>
<p class="etiqueta">PANEL DEL TALLER</p>
<h1>Bienvenido, {{ auth()->user()->nombre_completo }}</h1>
<p>Resumen de la información registrada en AutoGest.</p>
</div>
<a class="boton" href="{{ route('clientes.index') }}">Gestionar clientes</a>
</div>
<div class="estadisticas">@foreach(['Clientes' => $clientes, 'Vehículos' => $vehiculos, 'Citas registradas' => $citas, 'Servicios activos' => $servicios] as $label => $count)<article class="tarjeta">
<p>{{ $label }}</p>
<strong>{{ $count }}</strong>
</article>@endforeach</div>
<div class="titulo-fila espacio">
<h2>Próximas citas</h2>
<a href="{{ route('citas.create') }}">Registrar cita →</a>
</div>
<p class="tabla-ayuda">Desliza la tabla o utiliza las flechas del teclado para ver todas las columnas.</p>
<div class="tabla-contenedor" role="region" aria-label="Próximas citas, tabla con desplazamiento horizontal" tabindex="0">
<table>
<caption>Hasta ocho próximas citas registradas</caption>
<thead>
<tr>
<th scope="col">Fecha / hora</th>
<th scope="col">Cliente</th>
<th scope="col">Vehículo</th>
<th scope="col">Servicio</th>
</tr>
</thead>
<tbody>
@forelse($proximas as $cita)<tr>
<td>{{ $cita->fecha->format('d/m/Y') }}<small>{{ substr($cita->hora, 0, 5) }}</small>
</td>
<td>{{ $cita->cliente->nombre }} {{ $cita->cliente->apellido }}</td>
<td>{{ $cita->vehiculo->marca }} {{ $cita->vehiculo->modelo }}<small>{{ $cita->vehiculo->placa }}</small>
</td>
<td>{{ $cita->servicio->nombre }}</td>
</tr>@empty<tr>
<td colspan="4" class="vacio">No hay próximas citas registradas.</td>
</tr>@endforelse
</tbody>
</table>
</div>
</div>
</section>
@endsection
