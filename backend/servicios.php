<?php

header("Content-Type: application/json; charset=UTF-8");

require_once __DIR__ . "/../config/conexion.php";

try {

    $consulta = $conexion->prepare(
        "SELECT 
            id_servicio,
            nombre,
            descripcion,
            precio
         FROM servicios
         WHERE activo = :activo
         ORDER BY nombre"
    );

    $consulta->execute([
        "activo" => 1
    ]);

    $servicios = $consulta->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(
        $servicios,
        JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
    );

} catch (PDOException $e) {

    http_response_code(500);

    echo json_encode([
        "error" => "No fue posible consultar los servicios."
    ]);
}