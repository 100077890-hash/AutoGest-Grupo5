<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('usuarios', function (Blueprint $table) {
            $table->increments('id_usuario');
            $table->string('nombre_usuario', 50)->unique();
            $table->string('nombre_completo', 100);
            $table->string('password_hash');
            $table->timestamp('fecha_creacion')->useCurrent();
        });
        Schema::create('clientes', function (Blueprint $table) {
            $table->increments('id_cliente');
            $table->string('nombre', 50);
            $table->string('apellido', 50);
            $table->string('telefono', 20);
            $table->string('correo', 100)->unique();
            $table->timestamp('fecha_registro')->useCurrent();
        });
        Schema::create('vehiculos', function (Blueprint $table) {
            $table->increments('id_vehiculo');
            $table->unsignedInteger('id_cliente');
            $table->string('marca', 50);
            $table->string('modelo', 50);
            $table->unsignedSmallInteger('anio');
            $table->string('placa', 20)->unique();
            $table->foreign('id_cliente')->references('id_cliente')->on('clientes')->cascadeOnUpdate()->cascadeOnDelete();
        });
        Schema::create('servicios', function (Blueprint $table) {
            $table->increments('id_servicio');
            $table->string('nombre', 100)->unique();
            $table->string('descripcion')->nullable();
            $table->decimal('precio', 10, 2);
            $table->boolean('activo')->default(true);
        });
        Schema::create('citas', function (Blueprint $table) {
            $table->increments('id_cita');
            $table->unsignedInteger('id_cliente');
            $table->unsignedInteger('id_vehiculo');
            $table->unsignedInteger('id_servicio');
            $table->date('fecha');
            $table->time('hora');
            $table->text('comentarios')->nullable();
            $table->unsignedInteger('id_usuario')->nullable();
            $table->timestamp('fecha_registro')->useCurrent();
            $table->foreign('id_cliente')->references('id_cliente')->on('clientes')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreign('id_vehiculo')->references('id_vehiculo')->on('vehiculos')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreign('id_servicio')->references('id_servicio')->on('servicios')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreign('id_usuario')->references('id_usuario')->on('usuarios')->cascadeOnUpdate()->nullOnDelete();
            $table->index(['fecha', 'hora']);
        });
    }

    public function down(): void
    {
        foreach (['citas', 'vehiculos', 'servicios', 'clientes', 'usuarios'] as $table) {
            Schema::dropIfExists($table);
        }
    }
};
