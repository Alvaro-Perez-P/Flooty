<?php
session_start();
error_reporting(E_ALL);
ini_set("display_errors", 1);
require __DIR__ . '/sesion/conexion.php';

header('Content-Type: application/json');

/* ── Solo POST y usuario logueado ── */
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'error' => 'Método no permitido']);
    exit();
}

if (!isset($_SESSION['usuario']) || !isset($_SESSION['rol'])) {
    http_response_code(401);
    echo json_encode(['ok' => false, 'error' => 'Debes iniciar sesión para reservar']);
    exit();
}

/* ── Recibir y sanear datos ── */
$id_producto   = isset($_POST['id_producto'])  ? (int)$_POST['id_producto']  : 0;
$fecha_inicio  = isset($_POST['fecha_inicio']) ? trim($_POST['fecha_inicio']) : '';
$fecha_fin     = isset($_POST['fecha_fin'])    ? trim($_POST['fecha_fin'])    : '';
$mensaje       = isset($_POST['mensaje'])      ? trim($_POST['mensaje'])      : '';

/* ── Validaciones básicas ── */
if ($id_producto <= 0) {
    echo json_encode(['ok' => false, 'error' => 'Producto no válido']);
    exit();
}

if (!$fecha_inicio || !$fecha_fin) {
    echo json_encode(['ok' => false, 'error' => 'Debes seleccionar las fechas de inicio y fin']);
    exit();
}

$ts_inicio = strtotime($fecha_inicio);
$ts_fin    = strtotime($fecha_fin);

if ($ts_inicio === false || $ts_fin === false) {
    echo json_encode(['ok' => false, 'error' => 'Formato de fecha no válido']);
    exit();
}

if ($ts_fin < $ts_inicio) {
    echo json_encode(['ok' => false, 'error' => 'La fecha de fin no puede ser anterior a la de inicio']);
    exit();
}

if ($ts_inicio < strtotime(date('Y-m-d'))) {
    echo json_encode(['ok' => false, 'error' => 'La fecha de inicio no puede ser en el pasado']);
    exit();
}

$dias = (int)(($ts_fin - $ts_inicio) / 86400) + 1;

/* ── Obtener datos del producto ── */
$consulta_prod = "SELECT * FROM productos WHERE id = $id_producto LIMIT 1";
$res_prod = $_conexion->query($consulta_prod);

if (!$res_prod || $res_prod->num_rows === 0) {
    echo json_encode(['ok' => false, 'error' => 'El producto no existe']);
    exit();
}

$producto = $res_prod->fetch_assoc();

/* ── Obtener id del solicitante ── */
$usuario_sesion   = $_conexion->real_escape_string($_SESSION['usuario']);
$consulta_usuario = "SELECT id, usuario FROM usuarios WHERE usuario = '$usuario_sesion' LIMIT 1";
$res_usuario      = $_conexion->query($consulta_usuario);

if (!$res_usuario || $res_usuario->num_rows === 0) {
    echo json_encode(['ok' => false, 'error' => 'Usuario no encontrado']);
    exit();
}

$solicitante      = $res_usuario->fetch_assoc();
$id_solicitante   = (int)$solicitante['id'];
$nombre_solicitante = $solicitante['usuario'];

$id_propietario   = (int)$producto['id_usuario'];

/* ── No puedes reservar tu propio producto ── */
if ($id_solicitante === $id_propietario) {
    echo json_encode(['ok' => false, 'error' => 'No puedes reservar tu propio producto']);
    exit();
}

/* ── Comprobar solapamiento de fechas ── */
$consulta_solape = "
    SELECT id FROM reservas
    WHERE id_producto = $id_producto
      AND estado NOT IN ('rechazada', 'cancelada')
      AND fecha_inicio <= '$fecha_fin'
      AND fecha_fin    >= '$fecha_inicio'
    LIMIT 1
";
$res_solape = $_conexion->query($consulta_solape);

if ($res_solape && $res_solape->num_rows > 0) {
    echo json_encode(['ok' => false, 'error' => 'El producto ya tiene una reserva en esas fechas. Por favor elige otras fechas.']);
    exit();
}

/* ── Calcular importes ── */
$precio_dia  = (float)$producto['precio_dia'];
$fianza      = (float)($producto['fianza'] ?? 0);
$total       = round($precio_dia * $dias + $fianza, 2);
$mensaje_seg = $_conexion->real_escape_string($mensaje);

/* ── Insertar reserva ── */
$consulta_insert = "
    INSERT INTO reservas
        (id_producto, id_solicitante, id_propietario, fecha_inicio, fecha_fin, dias, precio_dia, fianza, total, estado, mensaje)
    VALUES
        ($id_producto, $id_solicitante, $id_propietario, '$fecha_inicio', '$fecha_fin', $dias, $precio_dia, $fianza, $total, 'pendiente', '$mensaje_seg')
";

if ($_conexion->query($consulta_insert)) {

    $id_reserva = $_conexion->insert_id;

    /* ── Crear notificación para el propietario ── */
    $titulo_producto = $producto['titulo'] ?? 'tu producto';

    $mensaje_notificacion = $_conexion->real_escape_string(
        $nombre_solicitante . " quiere reservar tu producto: " . $titulo_producto .
        " del " . date("d/m/Y", strtotime($fecha_inicio)) .
        " al " . date("d/m/Y", strtotime($fecha_fin)) . "."
    );

    $_conexion->query("
        INSERT INTO notificaciones
            (id_usuario, id_producto, id_reserva, tipo, mensaje)
        VALUES
            ($id_propietario, $id_producto, $id_reserva, 'nueva_reserva', '$mensaje_notificacion')
    ");

    echo json_encode([
        'ok'         => true,
        'id_reserva' => $id_reserva,
        'dias'       => $dias,
        'total'      => $total,
        'mensaje'    => 'Reserva enviada correctamente. El propietario la revisará pronto.'
    ]);

} else {
    echo json_encode(['ok' => false, 'error' => 'Error al guardar la reserva: ' . $_conexion->error]);
}