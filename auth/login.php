<?php

session_start();

require_once __DIR__ . "/../config/conexion.php";

$mensajeError = "";
$usuarioIngresado = "";

// Si ya existe una sesion activa, enviar al panel
if (isset($_SESSION["usuario_id"])) {
    header("Location: ../admin/dashboard.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $usuarioIngresado = trim($_POST["usuario"] ?? "");
    $password = $_POST["password"] ?? "";

    if ($usuarioIngresado === "" || $password === "") {

        $mensajeError = "Debe completar todos los campos.";

    } else {

        try {

            $consulta = $conexion->prepare(
                "SELECT
                    id_usuario,
                    nombre_usuario,
                    nombre_completo,
                    password_hash
                 FROM usuarios
                 WHERE nombre_usuario = :usuario
                 LIMIT 1"
            );

            $consulta->execute([
                "usuario" => $usuarioIngresado
            ]);

            $usuario = $consulta->fetch();

            if ($usuario && password_verify($password, $usuario["password_hash"])) {

                session_regenerate_id(true);

                $_SESSION["usuario_id"] = $usuario["id_usuario"];
                $_SESSION["nombre_usuario"] = $usuario["nombre_usuario"];
                $_SESSION["nombre_completo"] = $usuario["nombre_completo"];

                header("Location: ../admin/dashboard.php");
                exit;

            } else {

                $mensajeError = "Usuario o contraseña incorrectos.";
            }

        } catch (PDOException $e) {

            $mensajeError = "No fue posible procesar el inicio de sesión.";
        }
    }
}

?>

<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>AutoGest | Iniciar sesión</title>

    <link rel="stylesheet" href="../css/style.css">

</head>

<body>

    <header id="encabezado-principal">

        <div class="contenedor">

            <h1>AutoGest</h1>

            <p class="subtitulo">
                Sistema Web de Gestión para Taller Automotriz
            </p>

        </div>

    </header>

    <main>

        <section class="seccion">

            <div class="contenedor">

                <h2>Iniciar sesión</h2>

                <?php if ($mensajeError !== ""): ?>

                    <p class="mensaje-error">
                        <?= htmlspecialchars($mensajeError) ?>
                    </p>

                <?php endif; ?>

                <form method="POST" action="login.php">

                    <div class="grupo-campo">

                        <label for="usuario">
                            Usuario
                        </label>

                        <input
                            type="text"
                            id="usuario"
                            name="usuario"
                            value="<?= htmlspecialchars($usuarioIngresado) ?>"
                            required
                        >

                    </div>

                    <div class="grupo-campo">

                        <label for="password">
                            Contraseña
                        </label>

                        <input
                            type="password"
                            id="password"
                            name="password"
                            required
                        >

                    </div>

                    <div class="acciones-formulario">

                        <button
                            type="submit"
                            class="boton"
                        >
                            Iniciar sesión
                        </button>

                    </div>

                </form>

            </div>

        </section>

    </main>

</body>

</html>