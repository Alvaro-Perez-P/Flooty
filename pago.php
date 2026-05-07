<?php
session_start();
error_reporting(E_ALL);
ini_set("display_errors", 1);
require 'sesion/conexion.php';

/* ── SESIÓN ── */
if (!isset($_SESSION["usuario"]) || !isset($_SESSION["rol"])) {
    header("Location: sesion/login.php");
    exit();
}

/* ── OBTENER id_reserva ── */
$id_reserva = isset($_GET['id_reserva']) ? (int)$_GET['id_reserva'] : 0;
if ($id_reserva <= 0) {
    header("Location: index.php");
    exit();
}

/* ── Obtener id del usuario logueado ── */
$usuario_sesion = $_SESSION["usuario"];
$usuario_sesion_seguro = $_conexion->real_escape_string($usuario_sesion);
$consulta_usuario = "SELECT id FROM usuarios WHERE usuario = '$usuario_sesion_seguro' LIMIT 1";
$resultado_usuario = $_conexion->query($consulta_usuario);

if (!$resultado_usuario || $resultado_usuario->num_rows === 0) {
    header("Location: index.php");
    exit();
}

$user_info = $resultado_usuario->fetch_assoc();
$id_usuario = (int)$user_info["id"];

/* ── Obtener reserva ── */
$consulta_reserva = "SELECT * FROM reservas WHERE id = $id_reserva LIMIT 1";
$resultado_reserva = $_conexion->query($consulta_reserva);

$error = "";
$pago_ok = false;
$mensaje_pago = "";

if (!$resultado_reserva || $resultado_reserva->num_rows === 0) {
    $error = "La reserva no existe";
    $reserva = null;
} else {
    $reserva = $resultado_reserva->fetch_assoc();

    /* ── Solo el solicitante puede pagar ── */
    if ((int)$reserva["id_solicitante"] !== $id_usuario) {
        $error = "No tienes permisos para acceder a esta reserva";
        $reserva = null;
    }
}

/* ── Obtener producto asociado a la reserva ── */
$producto = null;
$imagenes = ["img/default.png"]; 

if ($reserva) {
    $id_producto = (int)$reserva["id_producto"];
    $consulta_producto = "SELECT * FROM productos WHERE id = $id_producto LIMIT 1";
    $resultado_producto = $_conexion->query($consulta_producto);
    if (!$resultado_producto || $resultado_producto->num_rows === 0) {
        $error = "El producto de la reserva no existe";
        $reserva = null;
    } else {
        $producto = $resultado_producto->fetch_assoc();
        $imgs = json_decode($producto["imagenes"], true);
        if (is_array($imgs) && count($imgs) > 0) {
            $imagenes = $imgs;
        }
    }
}

/* ── Booleanos de estado (como pedías) ── */
$aceptada = false;
$denegada = false;
if ($reserva) {
    $aceptada = ($reserva["estado"] === "aceptada");
    $denegada = ($reserva["estado"] === "rechazada");
}

/* ── Simulación de pago (sin tocar BD) ── */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $reserva) {
    if (isset($_POST['accion']) && $_POST['accion'] === 'pagar') {
        $pago_ok = true;
        $mensaje_pago = "Pago realizado correctamente.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pago – Flooty</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: Arial, Helvetica, sans-serif; }
        body { min-height: 100vh; background-color: #f5f1e6; display: flex; flex-direction: column; }

        .pagina { max-width: 1100px; margin: 30px auto; padding: 0 16px; flex: 1; }
        .titulo-pagina { font-size: 28px; font-weight: bold; color: #222; margin-bottom: 18px; }

        .grid { display: grid; grid-template-columns: 1fr 380px; gap: 28px; align-items: start; }
        .card-info { background: rgba(245,241,230,0.92); border-radius: 20px; border: 1px solid rgba(0,0,0,0.1); box-shadow: 0 10px 30px rgba(0,0,0,0.12); padding: 24px; }

        .producto-mini { display: flex; gap: 14px; align-items: center; margin-bottom: 16px; }
        .producto-mini img { width: 86px; height: 86px; object-fit: cover; border-radius: 14px; border: 1px solid rgba(0,0,0,0.1); }
        .producto-mini .nombre { font-weight: bold; color: #222; margin-bottom: 2px; }
        .producto-mini .sub { color: #777; font-size: 13px; }

        .badge-estado { display: inline-block; font-size: 12px; background-color: rgba(151,183,112,0.18); color: #6f8f4e; padding: 4px 12px; border-radius: 20px; font-weight: 700; }

        .fila-doble { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 10px; }
        .label { display: block; font-size: 12px; color: #888; margin-bottom: 5px; font-weight: 600; letter-spacing: 0.04em; }
        .valor { font-size: 15px; color: #333; }

        .resumen-fila { display: flex; justify-content: space-between; font-size: 14px; color: #555; margin-bottom: 7px; }
        .resumen-fila.total { border-top: 1px solid rgba(0,0,0,0.1); padding-top: 10px; margin-top: 5px; font-weight: bold; font-size: 16px; color: #333; }
        .resumen-fila.total span:last-child { color: #97B770; font-size: 18px; }

        .btn-pagar { width: 100%; padding: 14px; background-color: #97B770; color: white; border: none; border-radius: 10px; font-size: 16px; font-weight: bold; cursor: pointer; transition: 0.3s; }
        .btn-pagar:hover { background-color: #7fa45a; }

        .alerta { display: block; margin-bottom: 12px; padding: 12px 16px; border-radius: 10px; font-size: 14px; text-align: center; border: 1px solid rgba(220,80,80,0.3); background: rgba(220,80,80,0.1); color: #b03030; }
        .alerta.ok { border: 1px solid rgba(151,183,112,0.4); background: rgba(151,183,112,0.15); color: #4a7a2a; }

        .footer-container { background: rgba(245,241,230,0.9); border-radius: 20px; border: 1px solid rgba(0,0,0,0.1); box-shadow: 0 10px 30px rgba(0,0,0,0.15); margin: 20px 10px 10px 10px; padding: 18px 25px; }
        .footer-contenido { display: flex; justify-content: space-between; align-items: center; gap: 20px; flex-wrap: wrap; }
        .footer-logo { font-size: 22px; font-weight: bold; color: #97B770; }
        .footer-texto { font-size: 14px; color: #666; }
        .footer-links { display: flex; gap: 15px; flex-wrap: wrap; }
        .footer-links a { text-decoration: none; color: #666; font-size: 14px; transition: 0.2s; }
        .footer-links a:hover { color: #97B770; }

        @media (max-width: 860px) { .grid { grid-template-columns: 1fr; } }
        @media (max-width: 480px) { .fila-doble { grid-template-columns: 1fr; } }
    </style>
</head>
<body>

<?php include __DIR__ . '/nav_basico.php'; ?>

<div class="pagina">
    <div class="titulo-pagina">Pasarela de pago</div>

    <?php if ($error): ?>
        <div class="card-info">
            <div class="alerta">❌ <?= htmlspecialchars($error) ?></div>
            <a href="index.php" class="btn btn-secondary" style="background:#97B770;border:none;">Volver</a>
        </div>
    <?php else: ?>

        <div class="grid">

            <div class="card-info">
                <?php if ($mensaje_pago): ?>
                    <div class="alerta ok">✅ <?= htmlspecialchars($mensaje_pago) ?></div>
                <?php endif; ?>

                <div class="producto-mini">
                    <img src="<?= htmlspecialchars($imagenes[0]) ?>" alt="Producto">
                    <div>
                        <div class="nombre"><?= htmlspecialchars($producto["titulo"]) ?></div>
                        <div class="sub">
                            Reserva #<?= (int)$reserva["id"] ?> ·
                            <span class="badge-estado"><?= htmlspecialchars($reserva["estado"]) ?></span>
                        </div>
                    </div>
                </div>

                <div class="fila-doble">
                    <div>
                        <span class="label">Fecha inicio</span>
                        <div class="valor"><?= htmlspecialchars(date("d/m/Y", strtotime($reserva["fecha_inicio"]))) ?></div>
                    </div>
                    <div>
                        <span class="label">Fecha fin</span>
                        <div class="valor"><?= htmlspecialchars(date("d/m/Y", strtotime($reserva["fecha_fin"]))) ?></div>
                    </div>
                </div>

                <div class="fila-doble">
                    <div>
                        <span class="label">Días</span>
                        <div class="valor"><?= (int)$reserva["dias"] ?></div>
                    </div>
                    <div>
                        <span class="label">Estado (booleanos)</span>
                        <div class="valor">
                            Aceptada: <strong><?= $aceptada ? 'true' : 'false' ?></strong> ·
                            Denegada: <strong><?= $denegada ? 'true' : 'false' ?></strong>
                        </div>
                    </div>
                </div>

                <?php if ($pago_ok): ?>
                    <a href="producto.php?id=<?= (int)$reserva["id_producto"] ?>"
                       class="btn btn-secondary"
                       style="background:#97B770;border:none;margin-top:14px;">
                        Volver al producto
                    </a>
                <?php endif; ?>
            </div>

            <div class="card-info">
                <div style="font-size:17px;color:#97B770;font-weight:bold;margin-bottom:16px;">Resumen del pago</div>

                <div class="resumen-fila">
                    <span><?= number_format((float)$reserva["precio_dia"], 2, ",", ".") ?> € × <?= (int)$reserva["dias"] ?> <?= ((int)$reserva["dias"] === 1) ? 'día' : 'días' ?></span>
                    <span><?= number_format(((float)$reserva["precio_dia"] * (int)$reserva["dias"]), 2, ",", ".") ?> €</span>
                </div>

                <?php if ((float)$reserva["fianza"] > 0): ?>
                    <div class="resumen-fila">
                        <span>Fianza (reembolsable)</span>
                        <span><?= number_format((float)$reserva["fianza"], 2, ",", ".") ?> €</span>
                    </div>
                <?php endif; ?>

                <div class="resumen-fila total">
                    <span>Total</span>
                    <span><?= number_format((float)$reserva["total"], 2, ",", ".") ?> €</span>
                </div>

                <?php if (!$pago_ok): ?>
                    <form method="POST" style="margin-top:14px;">
                        <input type="hidden" name="accion" value="pagar">
                        <button class="btn-pagar" type="submit">Pagar ahora</button>
                    </form>
                <?php else: ?>
                    <div style="margin-top:14px;font-size:13px;color:#666;">✅ Pago registrado (simulado).</div>
                <?php endif; ?>
            </div>

        </div>

    <?php endif; ?>
</div>

<footer class="footer-container">
    <div class="footer-contenido">
        <div class="footer-logo">FLOOTY</div>
        <div class="footer-texto">© 2026 Flooty. Plataforma de alquiler entre personas.</div>
        <div class="footer-links">
            <a href="#">Aviso legal</a>
            <a href="#">Privacidad</a>
            <a href="#">Contacto</a>
            <a href="#">Ayuda</a>
        </div>
    </div>
</footer>

</body>
</html>
