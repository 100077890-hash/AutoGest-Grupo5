<?php

namespace App\Http\Controllers;

use App\Models\Cita;
use App\Models\Cliente;
use App\Models\Servicio;
use App\Models\Vehiculo;

class PaginaController extends Controller
{
    public function inicio()
    {
        return view('inicio', ['servicios' => Servicio::where('activo', true)->orderBy('id_servicio')->get()]);
    }

    public function servicios()
    {
        return view('servicios', ['servicios' => Servicio::where('activo', true)->orderBy('id_servicio')->get()]);
    }

    public function dashboard()
    {
        return view('admin.dashboard', [
            'clientes' => Cliente::count(), 'vehiculos' => Vehiculo::count(),
            'citas' => Cita::count(), 'servicios' => Servicio::where('activo', true)->count(),
            'proximas' => Cita::with(['cliente', 'vehiculo', 'servicio'])->where('fecha', '>=', today())->orderBy('fecha')->orderBy('hora')->limit(8)->get(),
        ]);
    }
}
