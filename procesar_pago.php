<?php
session_start();

// 1. Recogemos todos los datos del formulario
$id_producto    = (int)$_POST["id_producto"];
$id_propietario = (int)$_POST["id_propietario"];
$fecha_inicio   = $_POST["fecha_inicio"];
$fecha_fin      = $_POST["fecha_fin"];
$dias           = (int)$_POST["dias"];
$precio_dia     = (float)$_POST["precio_dia"];
$fianza         = (float)$_POST["fianza"];
$total          = (float)$_POST["total"];
$mensaje        = $_POST["mensaje"] ?? '';

// 2. Guardamos todo en una sesión para que 'pago_exito.php' pueda leerlo
$_SESSION['reserva_temp'] = [
    'id_producto'    => $id_producto,
    'id_propietario' => $id_propietario,
    'fecha_inicio'   => $fecha_inicio,
    'fecha_fin'      => $fecha_fin,
    'dias'           => $dias,
    'precio_dia'     => $precio_dia,
    'fianza'         => $fianza,
    'total'          => $total,
    'mensaje'        => $mensaje
];

/* SIMULACIÓN DE PASARELA (80% éxito) */
$random = rand(1, 100);

if ($random <= 80) {
    // Si el pago es "aceptado", vamos a la página de éxito
    header("Location: pago_exito.php?total=$total");
    exit();
} else {
    // Si falla, vamos a la página de error
    header("Location: pago_error.php");
    exit();
}