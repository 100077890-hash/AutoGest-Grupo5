<?php

require_once __DIR__ . "/../auth/proteger.php";

$nombreCompleto = $_SESSION["nombre_completo"];

?>

<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>AutoGest | Panel principal</title>

    <link rel="stylesheet" href="../css/style.css">

</head>

<body>

    <header id="encabezado-principal">

        <div class="contenedor">

            <h1>AutoGest</h1>

            <p class="subtitulo">
                Panel de administración
            </p>

        </div>

    </header>

    <main>

        <section class="seccion">

            <div class="contenedor">

                <h2>
                    Bienvenido, <?= htmlspecialchars($nombreCompleto) ?>
                </h2>

                <p>
                    Has iniciado sesión correctamente en AutoGest.
                </p>

                <p>
                    Usuario:
                    <strong>
                        <?= htmlspecialchars($_SESSION["nombre_usuario"]) ?>
                    </strong>
                </p>

                <p>
                    Esta es una página privada del sistema.
                </p>

                <a
                    href="../auth/logout.php"
                    class="boton"
                >
                    Cerrar sesión
                </a>

            </div>

        </section>

    </main>

</body>

</html>