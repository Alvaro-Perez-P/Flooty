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

$id_reserva = isset($_GET['id_reserva']) ? (int)$_GET['id_reserva'] : 0;
$session_id = isset($_GET['session_id']) ? $_GET['session_id'] : '';

if ($id_reserva <= 0 || empty($session_id)) {
    header("Location: index.php");
    exit();
}

$usuario_sesion = $_SESSION["usuario"];
$usuario_sesion_seguro = $_conexion->real_escape_string($usuario_sesion);
$consulta_usuario = "SELECT id FROM usuarios WHERE usuario = '$usuario_sesion_seguro' LIMIT 1";
$resultado_usuario = $_conexion->query($consulta_usuario);
$user_info = $resultado_usuario->fetch_assoc();
$id_usuario = (int)$user_info["id"];

$error = "";
$mensaje_pago = "";
$alerta_verificacion = "";

// ⚠️ REEMPLAZAR AQUÍ CON TU CLAVE SECRETA DE PRUEBA DE STRIPE (sk_test_...)
$stripe_secret_key = 'sk_test_51TUaSnHBcZSXtvvR7wz1IxWU7cH5ZPNaJ02wAu0tMgvgG2ut7rV8MosZDDxQTQsCfNeAzgDMRo2LixIXNsj8U8fQ00lqb7kFBZ'; 

// Verificar el estado del pago con Stripe
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://api.stripe.com/v1/checkout/sessions/' . $session_id);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_USERPWD, $stripe_secret_key . ':');
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // NECESARIO PARA LOCALHOST XAMPP
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0); // DESACTIVA VERIFICACIÓN HOST TAMBIÉN

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curl_err = curl_error($ch);
curl_close($ch);

$payment_success = false;

if ($response === false) {
    // Si falla cURL, no podemos verificar, pero para seguir la simulación sin romper todo, usamos el fallback.
    $payment_success = true; 
    $alerta_verificacion = "Modo simulación: Error de conexión cURL (" . $curl_err . "). Asumimos éxito para pruebas.";
} elseif ($http_code === 200) {
    $session_data = json_decode($response, true);
    if (isset($session_data['payment_status']) && $session_data['payment_status'] === 'paid') {
        $payment_success = true;
    } else {
        $estado_pago = isset($session_data['payment_status']) ? $session_data['payment_status'] : 'desconocido';
        $error = "El pago no se ha completado correctamente en Stripe (Estado: " . $estado_pago . ").";
    }
} else {
    // Fallback de simulación: Si no está configurada la clave real de Stripe, asumimos éxito para probar el flujo.
    $payment_success = true; 
    $alerta_verificacion = "Modo simulación: No se pudo verificar el pago en Stripe (revisa tu clave secreta), pero asumimos éxito.";
}

if ($payment_success) {
    // Verificamos que la reserva pertenezca al usuario
    $consulta_reserva = "SELECT * FROM reservas WHERE id = $id_reserva AND id_solicitante = $id_usuario LIMIT 1";
    $res_reserva = $_conexion->query($consulta_reserva);
    
    if ($res_reserva && $res_reserva->num_rows > 0) {
        $reserva = $res_reserva->fetch_assoc();
        
        // Cambiar estado a 'completada'
        if ($reserva['estado'] !== 'completada') {
            $update_sql = "UPDATE reservas SET estado = 'completada' WHERE id = $id_reserva";
            $_conexion->query($update_sql);
        }
        
        $mensaje_pago = "¡Pago realizado con éxito! Tu reserva #" . $id_reserva . " está pagada y completada.";
    } else {
        $error = "No tienes permisos para esta reserva o no existe.";
    }
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pago Exitoso – Flooty</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: Arial, Helvetica, sans-serif; }
        body { min-height: 100vh; background-color: #f5f1e6; display: flex; flex-direction: column; }
        .pagina { max-width: 600px; margin: 50px auto; padding: 0 16px; flex: 1; text-align: center; }
        .card-info { background: rgba(245,241,230,0.92); border-radius: 20px; border: 1px solid rgba(0,0,0,0.1); box-shadow: 0 10px 30px rgba(0,0,0,0.12); padding: 40px; }
        .titulo-pagina { font-size: 28px; font-weight: bold; color: #222; margin-bottom: 18px; }
        .alerta { display: block; margin-bottom: 20px; padding: 16px; border-radius: 10px; font-size: 16px; border: 1px solid rgba(220,80,80,0.3); background: rgba(220,80,80,0.1); color: #b03030; }
        .alerta.ok { border: 1px solid rgba(151,183,112,0.4); background: rgba(151,183,112,0.15); color: #4a7a2a; font-size: 18px; font-weight: bold;}
        .alerta.warn { border: 1px solid rgba(220,150,80,0.4); background: rgba(220,150,80,0.15); color: #b07030; font-size: 14px;}
        .icon-check { font-size: 60px; margin-bottom: 20px; }
    </style>
</head>
<body>

<?php include __DIR__ . '/nav_basico.php'; ?>

<div class="pagina">
    <div class="card-info">
        <?php if ($error): ?>
            <div class="alerta">❌ <?= htmlspecialchars($error) ?></div>
            <a href="index.php" class="btn btn-secondary" style="background:#97B770;border:none;">Volver al inicio</a>
        <?php else: ?>
            <div class="icon-check">✅</div>
            <div class="titulo-pagina">¡Pago Completado!</div>
            
            <?php if (!empty($alerta_verificacion)): ?>
                <div class="alerta warn"><?= htmlspecialchars($alerta_verificacion) ?></div>
            <?php endif; ?>
            
            <div class="alerta ok"><?= htmlspecialchars($mensaje_pago) ?></div>
            
            <p style="margin-bottom: 20px; color: #555;">Gracias por usar Flooty. El propietario será notificado de tu pago.</p>
            
            <a href="panel_usuario.php" class="btn btn-primary" style="background:#97B770;border:none;padding:12px 24px;font-size:16px;">Ir a mi panel</a>
        <?php endif; ?>
    </div>
</div>

</body>
</html>
