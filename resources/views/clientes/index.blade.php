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
        @php($clienteAEliminar = $clientes->firstWhere('id_cliente', request()->integer('eliminar')))
        @if($clienteAEliminar)
            <section class="confirmacion" aria-labelledby="titulo-eliminar" tabindex="-1" data-confirmacion>
                <h2 id="titulo-eliminar">Eliminar cliente</h2>
                @if($clienteAEliminar->citas_count > 0)
                    <p><strong>{{ $clienteAEliminar->nombre }} {{ $clienteAEliminar->apellido }}</strong> tiene citas relacionadas y no puede eliminarse.</p>
                    <a class="boton pequeno secundario" href="{{ route('clientes.index', ['page' => $clientes->currentPage()]) }}">Volver a clientes</a>
                @else
                    <p>¿Eliminar a <strong>{{ $clienteAEliminar->nombre }} {{ $clienteAEliminar->apellido }}</strong>? También se eliminarán sus vehículos sin citas. Esta acción no se puede deshacer.</p>
                    <form class="acciones" method="POST" action="{{ route('clientes.destroy', $clienteAEliminar) }}" data-submit-form>
                        @csrf
                        @method('DELETE')
                        <button class="boton pequeno peligro" type="submit" data-loading-text="Eliminando…">Confirmar eliminación</button>
                        <a class="boton pequeno secundario" href="{{ route('clientes.index', ['page' => $clientes->currentPage()]) }}">Cancelar</a>
                    </form>
                @endif
            </section>
        @endif
        <p class="tabla-ayuda">Desliza la tabla o utiliza las flechas del teclado para ver todas las columnas.</p>
        <div class="tabla-contenedor tabla-clientes" role="region" aria-label="Tabla de clientes, con desplazamiento horizontal" tabindex="0">
            <table>
                <caption>Clientes registrados en AutoGest</caption>
                <thead><tr><th scope="col">Cliente</th><th scope="col">Contacto</th><th scope="col">Vehículos / citas</th><th scope="col">Acciones</th></tr></thead>
                <tbody>
                    @forelse($clientes as $cliente)
                        <tr>
                            <td><strong>{{ $cliente->nombre }} {{ $cliente->apellido }}</strong><small>#{{ $cliente->id_cliente }}</small></td>
                            <td>{{ $cliente->correo }}<small>{{ $cliente->telefono }}</small></td>
                            <td>{{ $cliente->vehiculos_count }} {{ $cliente->vehiculos_count === 1 ? 'vehículo' : 'vehículos' }} · {{ $cliente->citas_count }} {{ $cliente->citas_count === 1 ? 'cita' : 'citas' }}</td>
                            <td>
                                <div class="acciones-tabla">
                                    <a class="boton pequeno secundario" href="{{ route('clientes.edit', $cliente) }}">Editar<span class="sr-only"> a {{ $cliente->nombre }} {{ $cliente->apellido }}</span></a>
                                    <a class="boton pequeno peligro" href="{{ route('clientes.index', ['eliminar' => $cliente->id_cliente, 'page' => $clientes->currentPage()]) }}">Eliminar<span class="sr-only"> a {{ $cliente->nombre }} {{ $cliente->apellido }}</span></a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="vacio"><strong>Todavía no hay clientes.</strong><br>Registra el primero con el botón «Nuevo cliente» para comenzar.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($clientes->hasPages())
            <nav class="paginacion" aria-label="Paginación de clientes">
                @if($clientes->onFirstPage())<span>Anterior</span>@else<a href="{{ $clientes->previousPageUrl() }}">← Anterior</a>@endif
                <span>Página {{ $clientes->currentPage() }} de {{ $clientes->lastPage() }}</span>
                @if($clientes->hasMorePages())<a href="{{ $clientes->nextPageUrl() }}">Siguiente →</a>@else<span>Siguiente</span>@endif
            </nav>
        @endif
    </div>
</section>
@endsection
