<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="AutoGest: sistema web de gestión para taller automotriz">
    <title>@yield('title', 'Inicio') | AutoGest</title>
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <script src="{{ asset('js/app.js') }}" defer>
</script>
</head>
<body>
<a class="saltar" href="#contenido">Saltar al contenido</a>
<header>
    <div class="contenedor cabecera">
        <a class="marca" href="{{ route('inicio') }}">
<strong>Auto<span>Gest</span>
</strong>
<small>Gestión para taller automotriz</small>
</a>
        <nav aria-label="Navegación principal">
            <ul class="menu">
                <li>
<a href="{{ route('inicio') }}" @if(request()->routeIs('inicio')) aria-current="page" @endif>Inicio</a>
</li>
                <li>
<a href="{{ route('servicios') }}" @if(request()->routeIs('servicios')) aria-current="page" @endif>Servicios</a>
</li>
                <li>
<a href="{{ route('citas.create') }}" @if(request()->routeIs('citas.*')) aria-current="page" @endif>Solicitar cita</a>
</li>
                @auth
                    <li>
<a href="{{ route('admin.dashboard') }}" @if(request()->routeIs('admin.*')) aria-current="page" @endif>Panel</a>
</li>
                    <li>
<a href="{{ route('clientes.index') }}" @if(request()->routeIs('clientes.*')) aria-current="page" @endif>Clientes</a>
</li>
                    <li>
<form method="POST" action="{{ route('logout') }}">@csrf<button class="salir" type="submit">Cerrar sesión</button>
</form>
</li>
                @else
                    <li>
<a class="acceso" href="{{ route('login') }}" @if(request()->routeIs('login')) aria-current="page" @endif>Acceso al taller</a>
</li>
                @endauth
            </ul>
        </nav>
    </div>
</header>
<main id="contenido">
    @if(session('status') || session('error') || $errors->any())
        <div class="contenedor avisos">
            @if(session('status'))<div class="alerta exito" role="status">{{ session('status') }}</div>@endif
            @if(session('error'))<div class="alerta error" role="alert">{{ session('error') }}</div>@endif
            @if($errors->any())
                <div class="alerta error" role="alert">
<strong>Revisa los campos indicados.</strong>
<ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
</div>
            @endif
        </div>
    @endif
    @yield('content')
</main>
<footer>
<div class="contenedor pie">
<div>
<strong>AutoGest</strong>
<p>Sistema Web de Gestión para Taller Automotriz</p>
</div>
<p>Grupo 5 · UAPA<br>Proyecto académico · {{ now()->year }}</p>
</div>
</footer>
</body>
</html>
