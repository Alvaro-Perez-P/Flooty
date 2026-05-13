<?php
session_start();

error_reporting(E_ALL);
ini_set("display_errors", 1);

if (!isset($_SESSION["usuario"])) {
    header("location: sesion/login.php");
    exit();
}

require "sesion/conexion.php";

/* Obtener usuario */
$usuario = $_conexion->real_escape_string($_SESSION["usuario"]);
$consulta_usuario = "SELECT id FROM usuarios WHERE usuario = '$usuario' LIMIT 1";
$resultado_usuario = $_conexion->query($consulta_usuario);

if (!$resultado_usuario || $resultado_usuario->num_rows == 0) {
    die("Usuario no encontrado.");
}

$user = $resultado_usuario->fetch_assoc();
$id_usuario = (int)$user["id"];

/* Cambiar estado */
if (isset($_GET["accion"]) && isset($_GET["id"])) {
    $id_reserva = (int)$_GET["id"];
    $accion = $_GET["accion"];

    $nuevo_estado = "";
    $tipo_notificacion = "";
    $mensaje_notificacion = "";

    if ($accion == "aceptar") {
        $nuevo_estado = "aceptada";
        $tipo_notificacion = "reserva_aceptada";
        $mensaje_notificacion = "Tu reserva fue aceptada.";
    }

    if ($accion == "rechazar") {
        $nuevo_estado = "rechazada";
        $tipo_notificacion = "reserva_rechazada";
        $mensaje_notificacion = "Tu reserva fue rechazada.";
    }

    if ($accion == "finalizar") {
        $nuevo_estado = "finalizada";
        $tipo_notificacion = "reserva_finalizada";
        $mensaje_notificacion = "Tu reserva fue finalizada.";
    }

    if ($nuevo_estado != "") {

        $condicion_estado = "pendiente";

        if ($accion == "finalizar") {
            $condicion_estado = "aceptada";
        }

        $_conexion->query("
            UPDATE reservas 
            SET estado = '$nuevo_estado'
            WHERE id = $id_reserva
            AND id_propietario = $id_usuario
            AND estado = '$condicion_estado'
        ");

        if ($_conexion->affected_rows > 0) {

            $consulta_reserva_notif = "
                SELECT 
                    r.id_solicitante,
                    r.id_producto,
                    p.titulo
                FROM reservas r
                INNER JOIN productos p ON r.id_producto = p.id
                WHERE r.id = $id_reserva
                AND r.id_propietario = $id_usuario
                LIMIT 1
            ";

            $resultado_reserva_notif = $_conexion->query($consulta_reserva_notif);

            if ($resultado_reserva_notif && $resultado_reserva_notif->num_rows > 0) {
                $reserva_notif = $resultado_reserva_notif->fetch_assoc();

                $id_solicitante_notif = (int)$reserva_notif["id_solicitante"];
                $id_producto_notif = (int)$reserva_notif["id_producto"];
                $titulo_producto_notif = $reserva_notif["titulo"];

                $mensaje_final = $_conexion->real_escape_string(
                    $mensaje_notificacion . " Producto: " . $titulo_producto_notif
                );

                $_conexion->query("
                    INSERT INTO notificaciones
                    (id_usuario, id_producto, id_reserva, tipo, mensaje)
                    VALUES
                    ($id_solicitante_notif, $id_producto_notif, $id_reserva, '$tipo_notificacion', '$mensaje_final')
                ");
            }
        }
    }

    header("location: reservas_recibidas.php");
    exit();
}

/* Listar reservas recibidas */
$consulta_reservas = "
    SELECT 
        r.*,
        p.titulo,
        p.imagenes,
        p.ciudad,
        u.usuario AS solicitante
    FROM reservas r
    INNER JOIN productos p ON r.id_producto = p.id
    INNER JOIN usuarios u ON r.id_solicitante = u.id
    WHERE r.id_propietario = $id_usuario
    ORDER BY r.fecha_creacion DESC
";

$resultado_reservas = $_conexion->query($consulta_reservas);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reservas recibidas</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, Helvetica, sans-serif;
        }

        body {
            background-color: #f5f1e6;
            min-height: 100vh;
        }

        .contenedor {
            width: 95%;
            max-width: 1100px;
            margin: 30px auto;
            background: rgba(245, 241, 230, 0.9);
            border-radius: 20px;
            border: 1px solid rgba(0,0,0,0.1);
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
            padding: 25px;
        }

        h1 {
            color: #97B770;
            margin-bottom: 25px;
            text-align: center;
        }

        .reserva {
            display: grid;
            grid-template-columns: 120px 1fr auto;
            gap: 20px;
            align-items: center;
            background: white;
            border-radius: 16px;
            padding: 15px;
            margin-bottom: 15px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        }

        .reserva img {
            width: 120px;
            height: 90px;
            object-fit: cover;
            border-radius: 12px;
            background: #ddd;
        }

        .reserva-info h3 {
            color: #333;
            margin-bottom: 6px;
        }

        .reserva-info p {
            color: #666;
            font-size: 14px;
            margin-bottom: 5px;
        }

        .total {
            font-weight: bold;
            color: #97B770;
            font-size: 18px;
        }

        .estado {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: bold;
            margin-top: 5px;
        }

        .pendiente { background: #fff3cd; color: #856404; }
        .aceptada { background: #d4edda; color: #155724; }
        .rechazada { background: #f8d7da; color: #721c24; }
        .cancelada { background: #e2e3e5; color: #383d41; }
        .finalizada { background: #d1ecf1; color: #0c5460; }

        .acciones {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .btn-accion {
            text-decoration: none;
            padding: 9px 14px;
            border-radius: 8px;
            color: white;
            font-size: 14px;
            text-align: center;
            font-weight: bold;
        }

        .btn-aceptar {
            background: #28a745;
        }

        .btn-rechazar {
            background: #dc3545;
        }

        .btn-finalizar {
            background: #17a2b8;
        }

        .btn-ver {
            background: #97B770;
        }

        .btn-volver {
            display: inline-block;
            margin-top: 20px;
            background: #8c8c8c;
            color: white;
            padding: 12px 18px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: bold;
        }

        .mensaje-vacio {
            background: white;
            padding: 25px;
            border-radius: 14px;
            text-align: center;
            color: #666;
        }

        @media(max-width: 768px) {
            .reserva {
                grid-template-columns: 1fr;
            }

            .reserva img {
                width: 100%;
                height: 180px;
            }

            .acciones {
                flex-direction: row;
                flex-wrap: wrap;
            }
        }
    </style>
</head>

<body>

<?php include __DIR__ . "/nav_basico.php"; ?>

<div class="contenedor">
    <h1>Reservas recibidas</h1>

    <?php if ($resultado_reservas && $resultado_reservas->num_rows > 0): ?>

        <?php while ($reserva = $resultado_reservas->fetch_assoc()): ?>
            <?php
                $imagenes = json_decode($reserva["imagenes"], true);
                $imagen = "imagenes/default.jpg";

                if (is_array($imagenes) && count($imagenes) > 0 && !empty($imagenes[0])) {
                    $imagen = $imagenes[0];
                }

                $estado = $reserva["estado"] ?? "pendiente";
            ?>

            <div class="reserva">
                <img src="<?= htmlspecialchars($imagen) ?>" alt="<?= htmlspecialchars($reserva["titulo"]) ?>">

                <div class="reserva-info">
                    <h3><?= htmlspecialchars($reserva["titulo"]) ?></h3>

                    <p><strong>Solicitante:</strong> <?= htmlspecialchars($reserva["solicitante"]) ?></p>
                    <p><strong>Ciudad:</strong> <?= htmlspecialchars($reserva["ciudad"]) ?></p>
                    <p><strong>Fechas:</strong> <?= htmlspecialchars($reserva["fecha_inicio"]) ?> al <?= htmlspecialchars($reserva["fecha_fin"]) ?></p>
                    <p><strong>Días:</strong> <?= (int)$reserva["dias"] ?></p>
                    <p><strong>Precio/día:</strong> <?= number_format((float)$reserva["precio_dia"], 2, ",", ".") ?> €</p>
                    <p><strong>Fianza:</strong> <?= number_format((float)$reserva["fianza"], 2, ",", ".") ?> €</p>

                    <?php if (!empty($reserva["mensaje"])): ?>
                        <p><strong>Mensaje:</strong> <?= htmlspecialchars($reserva["mensaje"]) ?></p>
                    <?php endif; ?>

                    <div class="total">
                        Total: <?= number_format((float)$reserva["total"], 2, ",", ".") ?> €
                    </div>

                    <span class="estado <?= htmlspecialchars($estado) ?>">
                        <?= htmlspecialchars($estado) ?>
                    </span>
                </div>

                <div class="acciones">
                    <a class="btn-accion btn-ver" href="producto.php?id=<?= (int)$reserva["id_producto"] ?>">
                        Ver producto
                    </a>

                    <?php if ($estado == "pendiente"): ?>
                        <a class="btn-accion btn-aceptar"
                           href="reservas_recibidas.php?accion=aceptar&id=<?= (int)$reserva["id"] ?>"
                           onclick="return confirm('¿Aceptar esta reserva?');">
                            Aceptar
                        </a>

                        <a class="btn-accion btn-rechazar"
                           href="reservas_recibidas.php?accion=rechazar&id=<?= (int)$reserva["id"] ?>"
                           onclick="return confirm('¿Rechazar esta reserva?');">
                            Rechazar
                        </a>
                    <?php endif; ?>

                    <?php if ($estado == "aceptada"): ?>
                        <a class="btn-accion btn-finalizar"
                           href="reservas_recibidas.php?accion=finalizar&id=<?= (int)$reserva["id"] ?>"
                           onclick="return confirm('¿Marcar esta reserva como finalizada?');">
                            Finalizar
                        </a>
                    <?php endif; ?>
                </div>
            </div>

        <?php endwhile; ?>

    <?php else: ?>
        <div class="mensaje-vacio">
            Todavía no recibiste reservas.
        </div>
    <?php endif; ?>

    <a href="index.php" class="btn-volver">Volver al inicio</a>
</div>

</body>
</html>