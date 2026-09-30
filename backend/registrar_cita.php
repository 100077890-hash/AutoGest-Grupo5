<?php

header("Content-Type: application/json; charset=UTF-8");

require_once __DIR__ . "/../config/conexion.php";

session_start();

function responder($estado, $datos)
{
    http_response_code($estado);
    echo json_encode(
        $datos,
        JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
    );
    exit;
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    responder(405, [
        "exito" => false,
        "mensaje" => "Método no permitido."
    ]);
}

// RECIBIR DATOS DEL FORMULARIO

$nombre = trim($_POST["nombre"] ?? "");
$apellido = trim($_POST["apellido"] ?? "");
$telefono = trim($_POST["telefono"] ?? "");
$correo = trim($_POST["correo"] ?? "");

$marca = trim($_POST["marca"] ?? "");
$modelo = trim($_POST["modelo"] ?? "");
$anio = filter_var($_POST["anio"] ?? null, FILTER_VALIDATE_INT);
$placa = trim($_POST["placa"] ?? "");

$servicio = trim($_POST["servicio"] ?? "");
$fecha = trim($_POST["fecha"] ?? "");
$hora = trim($_POST["hora"] ?? "");
$comentarios = trim($_POST["comentarios"] ?? "");

// VALIDACIONES DEL SERVIDOR

if (
    $nombre === "" ||
    $apellido === "" ||
    $telefono === "" ||
    $correo === "" ||
    $marca === "" ||
    $modelo === "" ||
    $anio === false ||
    $placa === "" ||
    $servicio === "" ||
    $fecha === "" ||
    $hora === ""
) {
    responder(400, [
        "exito" => false,
        "mensaje" => "Todos los campos obligatorios deben completarse."
    ]);
}

if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
    responder(400, [
        "exito" => false,
        "mensaje" => "El correo electrónico no es válido."
    ]);
}

$anioActual = (int) date("Y");

if ($anio < 1900 || $anio > $anioActual) {
    responder(400, [
        "exito" => false,
        "mensaje" => "El año del vehículo no es válido."
    ]);
}

// RELACIONAR EL VALOR DEL SELECT
// CON EL NOMBRE DEL SERVICIO EN LA BD

$serviciosPermitidos = [
    "aceite" => "Cambio de aceite",
    "frenos" => "Cambio de frenos",
    "alineacion" => "Alineación y balanceo",
    "diagnostico" => "Diagnóstico computarizado",
    "mantenimiento" => "Mantenimiento preventivo",
    "suspension" => "Reparación del sistema de suspensión"
];

if (!isset($serviciosPermitidos[$servicio])) {
    responder(400, [
        "exito" => false,
        "mensaje" => "El servicio seleccionado no es válido."
    ]);
}

$nombreServicio = $serviciosPermitidos[$servicio];

// TRANSACCIÓN

try {

    $conexion->beginTransaction();

    // 1. Buscar el servicio

    $consultaServicio = $conexion->prepare(
        "SELECT id_servicio
         FROM servicios
         WHERE nombre = :nombre
         AND activo = 1
         LIMIT 1"
    );

    $consultaServicio->execute([
        "nombre" => $nombreServicio
    ]);

    $servicioBD = $consultaServicio->fetch();

    if (!$servicioBD) {

        $conexion->rollBack();

        responder(400, [
            "exito" => false,
            "mensaje" => "El servicio seleccionado no está registrado en la base de datos."
        ]);
    }

    $idServicio = $servicioBD["id_servicio"];

    // 2. Buscar cliente por correo

    $consultaCliente = $conexion->prepare(
        "SELECT id_cliente
         FROM clientes
         WHERE correo = :correo
         LIMIT 1"
    );

    $consultaCliente->execute([
        "correo" => $correo
    ]);

    $cliente = $consultaCliente->fetch();

    if ($cliente) {

        $idCliente = $cliente["id_cliente"];

        // Actualizar datos básicos del cliente
        $actualizarCliente = $conexion->prepare(
            "UPDATE clientes
             SET nombre = :nombre,
                 apellido = :apellido,
                 telefono = :telefono
             WHERE id_cliente = :id_cliente"
        );

        $actualizarCliente->execute([
            "nombre" => $nombre,
            "apellido" => $apellido,
            "telefono" => $telefono,
            "id_cliente" => $idCliente
        ]);

    } else {

        // Crear nuevo cliente
        $insertarCliente = $conexion->prepare(
            "INSERT INTO clientes
            (nombre, apellido, telefono, correo)
            VALUES
            (:nombre, :apellido, :telefono, :correo)"
        );

        $insertarCliente->execute([
            "nombre" => $nombre,
            "apellido" => $apellido,
            "telefono" => $telefono,
            "correo" => $correo
        ]);

        $idCliente = $conexion->lastInsertId();
    }

    // 3. Buscar vehículo por placa

    $consultaVehiculo = $conexion->prepare(
        "SELECT id_vehiculo, id_cliente
         FROM vehiculos
         WHERE placa = :placa
         LIMIT 1"
    );

    $consultaVehiculo->execute([
        "placa" => $placa
    ]);

    $vehiculo = $consultaVehiculo->fetch();

    if ($vehiculo) {

        if ($vehiculo["id_cliente"] != $idCliente) {

            $conexion->rollBack();

            responder(400, [
                "exito" => false,
                "mensaje" => "La placa indicada ya está asociada a otro cliente."
            ]);
        }

        $idVehiculo = $vehiculo["id_vehiculo"];

        $actualizarVehiculo = $conexion->prepare(
            "UPDATE vehiculos
             SET marca = :marca,
                 modelo = :modelo,
                 anio = :anio
             WHERE id_vehiculo = :id_vehiculo"
        );

        $actualizarVehiculo->execute([
            "marca" => $marca,
            "modelo" => $modelo,
            "anio" => $anio,
            "id_vehiculo" => $idVehiculo
        ]);

    } else {

        // Crear nuevo vehículo
        $insertarVehiculo = $conexion->prepare(
            "INSERT INTO vehiculos
            (id_cliente, marca, modelo, anio, placa)
            VALUES
            (:id_cliente, :marca, :modelo, :anio, :placa)"
        );

        $insertarVehiculo->execute([
            "id_cliente" => $idCliente,
            "marca" => $marca,
            "modelo" => $modelo,
            "anio" => $anio,
            "placa" => $placa
        ]);

        $idVehiculo = $conexion->lastInsertId();
    }

    // 4. Usuario de sesión, si existe

    $idUsuario = $_SESSION["usuario_id"] ?? null;

    // 5. Registrar cita

    $insertarCita = $conexion->prepare(
        "INSERT INTO citas
        (
            id_cliente,
            id_vehiculo,
            id_servicio,
            fecha,
            hora,
            comentarios,
            id_usuario
        )
        VALUES
        (
            :id_cliente,
            :id_vehiculo,
            :id_servicio,
            :fecha,
            :hora,
            :comentarios,
            :id_usuario
        )"
    );

    $insertarCita->execute([
        "id_cliente" => $idCliente,
        "id_vehiculo" => $idVehiculo,
        "id_servicio" => $idServicio,
        "fecha" => $fecha,
        "hora" => $hora,
        "comentarios" => $comentarios,
        "id_usuario" => $idUsuario
    ]);

    $idCita = $conexion->lastInsertId();

    $conexion->commit();

    responder(200, [
        "exito" => true,
        "mensaje" => "Cita registrada correctamente.",
        "id_cita" => $idCita
    ]);

} catch (PDOException $e) {

    if ($conexion->inTransaction()) {
        $conexion->rollBack();
    }

    responder(500, [
        "exito" => false,
        "mensaje" => "No fue posible registrar la cita."
    ]);
}