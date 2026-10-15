<?php

namespace App\Http\Controllers;

use App\Http\Requests\CitaRequest;
use App\Models\Cita;
use App\Models\Cliente;
use App\Models\Servicio;
use App\Models\Vehiculo;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CitaController extends Controller
{
    public function create()
    {
        return view('citas.create', ['servicios' => Servicio::where('activo', true)->orderBy('id_servicio')->get()]);
    }

    public function store(CitaRequest $request)
    {
        $data = $request->validated();
        $cita = DB::transaction(function () use ($data, $request) {
            $cliente = Cliente::firstOrCreate(['correo' => $data['correo']], array_intersect_key($data, array_flip(['nombre', 'apellido', 'telefono'])));
            // El formulario público no modifica la identidad de clientes ya registrados.
            foreach (['nombre', 'apellido', 'telefono'] as $campo) {
                if (mb_strtolower($cliente->$campo) !== mb_strtolower($data[$campo])) {
                    throw ValidationException::withMessages(['correo' => 'Los datos no coinciden con el cliente registrado. Contacta al taller para actualizarlos.']);
                }
            }
            $vehiculo = Vehiculo::firstOrCreate(['placa' => $data['placa']], [
                'id_cliente' => $cliente->id_cliente,
                'marca' => $data['marca'], 'modelo' => $data['modelo'], 'anio' => $data['anio'],
            ]);
            if ($vehiculo->id_cliente !== $cliente->id_cliente) {
                throw ValidationException::withMessages(['placa' => 'La placa indicada ya está asociada a otro cliente.']);
            }
            if (mb_strtolower($vehiculo->marca) !== mb_strtolower($data['marca']) ||
                mb_strtolower($vehiculo->modelo) !== mb_strtolower($data['modelo']) || $vehiculo->anio != $data['anio']) {
                throw ValidationException::withMessages(['placa' => 'Los datos del vehículo no coinciden con el registro. Contacta al taller para actualizarlos.']);
            }

            return Cita::create([
                'id_cliente' => $cliente->id_cliente, 'id_vehiculo' => $vehiculo->id_vehiculo,
                'id_servicio' => $data['id_servicio'], 'fecha' => $data['fecha'], 'hora' => $data['hora'],
                'comentarios' => $data['comentarios'] ?? null, 'id_usuario' => $request->user()?->id_usuario,
            ]);
        });

        return redirect()->route('citas.create')->with('status', 'Cita #'.$cita->id_cita.' registrada correctamente. Hemos recibido tu solicitud.');
    }
}
