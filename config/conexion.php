<?php

$config = require __DIR__ . "/config.local.php";

$host = $config["host"];
$baseDatos = $config["database"];
$usuario = $config["username"];
$password = $config["password"];

try {

    $conexion = new PDO(
        "mysql:host=$host;dbname=$baseDatos;charset=utf8mb4",
        $usuario,
        $password
    );

    $conexion->setAttribute(
        PDO::ATTR_ERRMODE,
        PDO::ERRMODE_EXCEPTION
    );

    $conexion->setAttribute(
        PDO::ATTR_DEFAULT_FETCH_MODE,
        PDO::FETCH_ASSOC
    );

} catch (PDOException $e) {

    die("No fue posible conectar con la base de datos.");
}