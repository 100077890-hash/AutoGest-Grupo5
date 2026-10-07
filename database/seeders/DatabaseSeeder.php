<?php

namespace Database\Seeders;

use App\Models\Cita;
use App\Models\Cliente;
use App\Models\Servicio;
use App\Models\Usuario;
use App\Models\Vehiculo;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Cuenta exclusivamente académica. No sustituye contraseñas de usuarios existentes.
        $usuario = Usuario::firstOrCreate(['nombre_usuario' => 'demo'], [
            'nombre_completo' => 'Administrador Demo',
            'password_hash' => Hash::make('AutoGestDemo2026!'),
        ]);
        foreach ([
            ['Cambio de aceite', 'Cambio de aceite y filtro', 2500],
            ['Cambio de frenos', 'Revisión y mantenimiento del sistema de frenos', 4000],
            ['Alineación y balanceo', 'Corrección de alineación y balanceo', 2000],
            ['Diagnóstico computarizado', 'Evaluación mediante diagnóstico computarizado', 1500],
            ['Mantenimiento preventivo', 'Revisión general del vehículo', 3500],
            ['Reparación del sistema de suspensión', 'Revisión y reparación del sistema de suspensión', 0],
        ] as [$nombre, $descripcion, $precio]) {
            Servicio::firstOrCreate(['nombre' => $nombre], compact('descripcion', 'precio'));
        }
        $cliente = Cliente::firstOrCreate(['correo' => 'cliente@example.com'], [
            'nombre' => 'Ana', 'apellido' => 'Pérez', 'telefono' => '809-555-0100',
        ]);
        $vehiculo = Vehiculo::firstOrCreate(['placa' => 'DEMO-001'], [
            'id_cliente' => $cliente->id_cliente, 'marca' => 'Toyota', 'modelo' => 'Corolla', 'anio' => 2020,
        ]);
        if ($vehiculo->id_cliente === $cliente->id_cliente) {
            Cita::firstOrCreate(['comentarios' => 'Cita de demostración académica.'], [
                'id_cliente' => $cliente->id_cliente, 'id_vehiculo' => $vehiculo->id_vehiculo,
                'id_servicio' => Servicio::where('nombre', 'Cambio de aceite')->firstOrFail()->id_servicio,
                'fecha' => '2026-12-15', 'hora' => '09:00', 'id_usuario' => $usuario->id_usuario,
            ]);
        }
    }
}
