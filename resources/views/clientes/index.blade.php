@extends('layouts.app')
@section('title', 'Clientes')
@section('content')
<section class="seccion">
<div class="contenedor">
<div class="titulo-fila">
<div>
<p class="etiqueta">GESTIÓN DEL TALLER</p>
<h1>Clientes</h1>
<p>{{ $clientes->total() }} {{ $clientes->total() === 1 ? 'cliente registrado' : 'clientes registrados' }}</p>
</div>
<a class="boton" href="{{ route('clientes.create') }}">+ Nuevo cliente</a>
</div>
<div class="tabla-contenedor">
<table>
<caption>Clientes registrados en AutoGest</caption>
<thead>
<tr>
<th scope="col">Cliente</th>
<th scope="col">Contacto</th>
<th scope="col">Vehículos / citas</th>
<th scope="col">Acciones</th>
</tr>
</thead>
<tbody>
@forelse($clientes as $cliente)<tr>
<td>
<strong>{{ $cliente->nombre }} {{ $cliente->apellido }}</strong>
<small>#{{ $cliente->id_cliente }}</small>
</td>
<td>{{ $cliente->correo }}<small>{{ $cliente->telefono }}</small>
</td>
<td>{{ $cliente->vehiculos_count }} vehículos · {{ $cliente->citas_count }} citas</td>
<td>
<div class="acciones-tabla">
<a class="boton pequeno secundario" href="{{ route('clientes.edit', $cliente) }}">Editar<span class="sr-only"> a {{ $cliente->nombre }} {{ $cliente->apellido }}</span>
</a>
<a class="boton pequeno peligro" href="{{ route('clientes.index', ['eliminar' => $cliente->id_cliente, 'page' => $clientes->currentPage()]) }}">Eliminar<span class="sr-only"> a {{ $cliente->nombre }} {{ $cliente->apellido }}</span>
</a>
</div>
@if((string) request()->integer('eliminar') === (string) $cliente->id_cliente)<div class="confirmacion">
<p>¿Eliminar a {{ $cliente->nombre }} {{ $cliente->apellido }} y sus vehículos? Las citas relacionadas impedirán la eliminación.</p>
<form method="POST" action="{{ route('clientes.destroy', $cliente) }}">@csrf @method('DELETE')<button class="boton pequeno peligro" type="submit">Confirmar eliminación</button> <a href="{{ route('clientes.index', ['page' => $clientes->currentPage()]) }}">Cancelar</a>
</form>
</div>@endif
</td>
</tr>@empty<tr>
<td colspan="4" class="vacio">Todavía no hay clientes. Registra el primero para comenzar.</td>
</tr>@endforelse
</tbody>
</table>
</div>
@if($clientes->hasPages())<nav class="paginacion" aria-label="Paginación de clientes">@if($clientes->onFirstPage())<span>Anterior</span>@else<a href="{{ $clientes->previousPageUrl() }}">← Anterior</a>@endif<span>Página {{ $clientes->currentPage() }} de {{ $clientes->lastPage() }}</span>@if($clientes->hasMorePages())<a href="{{ $clientes->nextPageUrl() }}">Siguiente →</a>@else<span>Siguiente</span>@endif</nav>@endif
</div>
</section>
@endsection
