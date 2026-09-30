<?php

require_once __DIR__ . "/../auth/proteger.php";
require_once __DIR__ . "/../config/conexion.php";

$mensaje = "";
$tipoMensaje = "";

$idCliente = (int) ($_GET["id"] ?? $_POST["id_cliente"] ?? 0);

if ($idCliente <= 0) {
    header("Location: clientes.php");
    exit;
}

// ACTUALIZAR CLIENTE
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["actualizar_cliente"])) {

    $nombre = trim($_POST["nombre"] ?? "");
    $apellido = trim($_POST["apellido"] ?? "");
    $telefono = trim($_POST["telefono"] ?? "");
    $correo = trim($_POST["correo"] ?? "");

    if ($nombre === "" || $apellido === "" || $telefono === "" || $correo === "") {

        $mensaje = "Todos los campos son obligatorios.";
        $tipoMensaje = "error";

    } elseif (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {

        $mensaje = "Ingrese un correo electrónico válido.";
        $tipoMensaje = "error";

    } else {

        try {

            $consulta = $conexion->prepare(
                "UPDATE clientes
                 SET nombre = :nombre,
                     apellido = :apellido,
                     telefono = :telefono,
                     correo = :correo
                 WHERE id_cliente = :id_cliente"
            );

            $consulta->execute([
                "nombre" => $nombre,
                "apellido" => $apellido,
                "telefono" => $telefono,
                "correo" => $correo,
                "id_cliente" => $idCliente
            ]);

            $mensaje = "Cliente actualizado correctamente.";
            $tipoMensaje = "exito";

        } catch (PDOException $e) {

            $mensaje = "No fue posible actualizar el cliente.";
            $tipoMensaje = "error";
        }
    }
}

// OBTENER CLIENTE
$consulta = $conexion->prepare(
    "SELECT
        id_cliente,
        nombre,
        apellido,
        telefono,
        correo
     FROM clientes
     WHERE id_cliente = :id_cliente
     LIMIT 1"
);

$consulta->execute([
    "id_cliente" => $idCliente
]);

$cliente = $consulta->fetch();

if (!$cliente) {
    header("Location: clientes.php");
    exit;
}

?>

<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>AutoGest | Editar cliente</title>

    <link rel="stylesheet" href="../css/style.css">

</head>

<body>

    <header id="encabezado-principal">

        <div class="contenedor">

            <h1>AutoGest</h1>

            <p class="subtitulo">
                Editar cliente
            </p>

        </div>

    </header>

    <main>

        <section class="seccion">

            <div class="contenedor">

                <h2>Actualizar información del cliente</h2>

                <?php if ($mensaje !== ""): ?>

                    <p class="<?= $tipoMensaje === "exito"
                        ? "mensaje-exito"
                        : "mensaje-error" ?>">

                        <?= htmlspecialchars($mensaje) ?>

                    </p>

                <?php endif; ?>

                <form method="POST" action="editar_cliente.php">

                    <input
                        type="hidden"
                        name="id_cliente"
                        value="<?= $cliente["id_cliente"] ?>"
                    >

                    <div class="grupo-campo">

                        <label for="nombre">
                            Nombre
                        </label>

                        <input
                            type="text"
                            id="nombre"
                            name="nombre"
                            value="<?= htmlspecialchars($cliente["nombre"]) ?>"
                            required
                        >

                    </div>

                    <div class="grupo-campo">

                        <label for="apellido">
                            Apellido
                        </label>

                        <input
                            type="text"
                            id="apellido"
                            name="apellido"
                            value="<?= htmlspecialchars($cliente["apellido"]) ?>"
                            required
                        >

                    </div>

                    <div class="grupo-campo">

                        <label for="telefono">
                            Teléfono
                        </label>

                        <input
                            type="tel"
                            id="telefono"
                            name="telefono"
                            value="<?= htmlspecialchars($cliente["telefono"]) ?>"
                            required
                        >

                    </div>

                    <div class="grupo-campo">

                        <label for="correo">
                            Correo electrónico
                        </label>

                        <input
                            type="email"
                            id="correo"
                            name="correo"
                            value="<?= htmlspecialchars($cliente["correo"]) ?>"
                            required
                        >

                    </div>

                    <div class="acciones-formulario">

                        <button
                            type="submit"
                            name="actualizar_cliente"
                            class="boton"
                        >
                            Guardar cambios
                        </button>

                        <a
                            href="clientes.php"
                            class="boton boton-secundario"
                        >
                            Cancelar
                        </a>

                    </div>

                </form>

            </div>

        </section>

    </main>

</body>

</html>