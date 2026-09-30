<?php

require_once __DIR__ . "/../auth/proteger.php";
require_once __DIR__ . "/../config/conexion.php";

$mensaje = "";
$tipoMensaje = "";

// CREAR CLIENTE
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["crear_cliente"])) {

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
                "INSERT INTO clientes
                (nombre, apellido, telefono, correo)
                VALUES (:nombre, :apellido, :telefono, :correo)"
            );

            $consulta->execute([
                "nombre" => $nombre,
                "apellido" => $apellido,
                "telefono" => $telefono,
                "correo" => $correo
            ]);

            $mensaje = "Cliente registrado correctamente.";
            $tipoMensaje = "exito";

        } catch (PDOException $e) {

            $mensaje = "No fue posible registrar el cliente.";
            $tipoMensaje = "error";
        }
    }
}

// ELIMINAR CLIENTE
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["eliminar_cliente"])) {

    $idCliente = (int) ($_POST["id_cliente"] ?? 0);

    if ($idCliente > 0) {

        try {

            $consulta = $conexion->prepare(
                "DELETE FROM clientes
                 WHERE id_cliente = :id_cliente"
            );

            $consulta->execute([
                "id_cliente" => $idCliente
            ]);

            if ($consulta->rowCount() > 0) {

                $mensaje = "Cliente eliminado correctamente.";
                $tipoMensaje = "exito";

            } else {

                $mensaje = "El cliente no existe.";
                $tipoMensaje = "error";
            }

        } catch (PDOException $e) {

            $mensaje = "No se puede eliminar el cliente porque tiene registros relacionados.";
            $tipoMensaje = "error";
        }
    }
}

// CONSULTAR CLIENTES
$consultaClientes = $conexion->query(
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

$clientes = $consultaClientes->fetchAll();

?>

<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>AutoGest | Clientes</title>

    <link rel="stylesheet" href="../css/style.css">

</head>

<body>

    <header id="encabezado-principal">

        <div class="contenedor">

            <h1>AutoGest</h1>

            <p class="subtitulo">
                Gestión de clientes
            </p>

        </div>

    </header>

    <main>

        <section class="seccion">

            <div class="contenedor">

                <h2>Registrar cliente</h2>

                <?php if ($mensaje !== ""): ?>

                    <p class="<?= $tipoMensaje === "exito"
                        ? "mensaje-exito"
                        : "mensaje-error" ?>">

                        <?= htmlspecialchars($mensaje) ?>

                    </p>

                <?php endif; ?>

                <form method="POST" action="clientes.php">

                    <div class="grupo-campo">

                        <label for="nombre">
                            Nombre
                        </label>

                        <input
                            type="text"
                            id="nombre"
                            name="nombre"
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
                            required
                        >

                    </div>

                    <div class="acciones-formulario">

                        <button
                            type="submit"
                            name="crear_cliente"
                            class="boton"
                        >
                            Crear cliente
                        </button>

                    </div>

                </form>

            </div>

        </section>

        <section class="seccion">

            <div class="contenedor">

                <h2>Clientes registrados</h2>

                <div style="overflow-x: auto;">

                    <table>

                        <thead>

                            <tr>

                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Apellido</th>
                                <th>Teléfono</th>
                                <th>Correo</th>
                                <th>Acciones</th>

                            </tr>

                        </thead>

                        <tbody>

                            <?php if (count($clientes) > 0): ?>

                                <?php foreach ($clientes as $cliente): ?>

                                    <tr>

                                        <td>
                                            <?= htmlspecialchars($cliente["id_cliente"]) ?>
                                        </td>

                                        <td>
                                            <?= htmlspecialchars($cliente["nombre"]) ?>
                                        </td>

                                        <td>
                                            <?= htmlspecialchars($cliente["apellido"]) ?>
                                        </td>

                                        <td>
                                            <?= htmlspecialchars($cliente["telefono"]) ?>
                                        </td>

                                        <td>
                                            <?= htmlspecialchars($cliente["correo"]) ?>
                                        </td>

                                        <td>

                                            <a
                                                href="editar_cliente.php?id=<?= $cliente["id_cliente"] ?>"
                                                class="boton"
                                            >
                                                Editar
                                            </a>

                                            <form
                                                method="POST"
                                                action="clientes.php"
                                                style="display: inline;"
                                                onsubmit="return confirm('¿Está seguro de que desea eliminar este cliente?');"
                                            >

                                                <input
                                                    type="hidden"
                                                    name="id_cliente"
                                                    value="<?= $cliente["id_cliente"] ?>"
                                                >

                                                <button
                                                    type="submit"
                                                    name="eliminar_cliente"
                                                    class="boton boton-secundario"
                                                >
                                                    Eliminar
                                                </button>

                                            </form>

                                        </td>

                                    </tr>

                                <?php endforeach; ?>

                            <?php else: ?>

                                <tr>

                                    <td colspan="6">
                                        No hay clientes registrados.
                                    </td>

                                </tr>

                            <?php endif; ?>

                        </tbody>

                    </table>

                </div>

            </div>

        </section>

    </main>

</body>

</html>