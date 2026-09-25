<?php

header("Content-Type: application/json; charset=UTF-8");

require_once __DIR__ . "/../config/conexion.php";

try {

    $consulta = $conexion->prepare(
        "SELECT
            id_cliente,
            nombre,
            apellido,
            telefono,
            correo,
            fecha_registro
         FROM clientes
         ORDER BY id_cliente DESC"
    );

    $consulta->execute();

    $clientes = $consulta->fetchAll();

    echo json_encode(
        $clientes,
        JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
    );

} catch (PDOException $e) {

    http_response_code(500);

    echo json_encode([
        "error" => "No fue posible consultar los clientes."
    ]);
}