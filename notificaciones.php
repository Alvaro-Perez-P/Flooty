<?php
session_start();

error_reporting(E_ALL);
ini_set("display_errors", 1);

require 'sesion/conexion.php';

if (!isset($_SESSION["usuario"])) {
    header("location: sesion/login.php");
    exit();
}

$usuario_sesion = $_conexion->real_escape_string($_SESSION["usuario"]);

$consulta_usuario = "
    SELECT id, usuario
    FROM usuarios
    WHERE usuario = '$usuario_sesion'
    LIMIT 1
";

$resultado_usuario = $_conexion->query($consulta_usuario);

if (!$resultado_usuario || $resultado_usuario->num_rows === 0) {
    header("location: sesion/login.php");
    exit();
}

$usuario = $resultado_usuario->fetch_assoc();
$id_usuario = (int)$usuario["id"];

/* TRAER NOTIFICACIONES */
$notificaciones = [];

$consulta_notificaciones = "
    SELECT *
    FROM notificaciones
    WHERE id_usuario = $id_usuario
    ORDER BY fecha_creacion DESC
";

$resultado_notificaciones = $_conexion->query($consulta_notificaciones);

if ($resultado_notificaciones && $resultado_notificaciones->num_rows > 0) {
    while ($fila = $resultado_notificaciones->fetch_assoc()) {
        $notificaciones[] = $fila;
    }
}

/* MARCAR COMO LEÍDAS DESPUÉS DE GUARDARLAS */
$_conexion->query("
    UPDATE notificaciones
    SET leida = 1
    WHERE id_usuario = $id_usuario
    AND leida = 0
");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Notificaciones - Flooty</title>

    <style>
        body {
            background: #f5f1e6;
            font-family: Arial, Helvetica, sans-serif;
            margin: 0;
        }

        .contenedor {
            max-width: 900px;
            margin: 30px auto;
            padding: 0 15px;
        }

        .titulo {
            color: #97B770;
            font-size: 32px;
            font-weight: bold;
            margin-bottom: 25px;
        }

        .notificacion {
            background: white;
            padding: 18px;
            border-radius: 18px;
            margin-bottom: 15px;
            box-shadow: 0 6px 18px rgba(0,0,0,0.08);
            border-left: 6px solid #97B770;
        }

        .notificacion-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            gap: 10px;
        }

        .tipo {
            background: rgba(151,183,112,0.15);
            color: #6f8f4e;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .fecha {
            color: #888;
            font-size: 13px;
        }

        .mensaje {
            color: #444;
            font-size: 15px;
            line-height: 1.5;
        }

        .sin-notificaciones {
            background: white;
            padding: 35px;
            border-radius: 18px;
            text-align: center;
            color: #777;
            box-shadow: 0 6px 18px rgba(0,0,0,0.08);
        }

        .btn-volver {
            display: inline-block;
            margin-top: 20px;
            background: #97B770;
            color: white;
            padding: 12px 18px;
            border-radius: 10px;
            text-decoration: none;
            font-weight: bold;
        }
    </style>
</head>

<body>

<?php include __DIR__ . '/nav_basico.php'; ?>

<div class="contenedor">

    <h1 class="titulo">🔔 Notificaciones</h1>

    <?php if (count($notificaciones) > 0): ?>

        <?php foreach ($notificaciones as $notificacion): ?>

            <div class="notificacion">

                <div class="notificacion-header">
                    <span class="tipo">
                        <?= htmlspecialchars($notificacion["tipo"]) ?>
                    </span>

                    <span class="fecha">
                        <?= date("d/m/Y H:i", strtotime($notificacion["fecha_creacion"])) ?>
                    </span>
                </div>

                <div class="mensaje">
                    <?= nl2br(htmlspecialchars($notificacion["mensaje"])) ?>
                </div>

            </div>

        <?php endforeach; ?>

    <?php else: ?>

        <div class="sin-notificaciones">
            No tienes notificaciones todavía.
        </div>

    <?php endif; ?>

    <a href="index.php" class="btn-volver">← Volver al inicio</a>

</div>

</body>
</html>