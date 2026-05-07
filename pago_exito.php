<?php
include 'conexion.php'; 
session_start();

$total_visual = isset($_GET["total"]) ? (float)$_GET["total"] : 0;

// --- BLOQUE DE DEPURACIÓN (Borrar cuando funcione) ---
if (!isset($_SESSION['reserva_temp'])) {
    echo "DEBUG: La sesión 'reserva_temp' está VACÍA. Revisa procesar_pago.php<br>";
}
if (!isset($_SESSION['id_usuario'])) {
    echo "DEBUG: No existe 'id_usuario' en la sesión. ¿Has hecho login?<br>";
}
// ----------------------------------------------------

if (isset($_SESSION['reserva_temp']) && isset($_SESSION['id_usuario'])) {
    
    $datos = $_SESSION['reserva_temp'];
    $id_logueado = $_SESSION['id_usuario']; 

    // SQL ajustado a tu captura de pantalla
    $sql = "INSERT INTO reservas (
                id_producto, id_solicitante, id_propietario, 
                fecha_inicio, fecha_fin, dias, precio_dia, 
                fianza, total, estado, mensaje, id_usuario
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'aceptada', ?, ?)";

    $stmt = $conexion->prepare($sql);

    // Verificamos si la preparación falló (nombres de columnas mal escritos)
    if (!$stmt) {
        die("Error en la preparación del SQL: " . $conexion->error);
    }

    $stmt->bind_param(
        "iiissidddsi", 
        $datos['id_producto'], 
        $id_logueado, 
        $datos['id_propietario'], 
        $datos['fecha_inicio'], 
        $datos['fecha_fin'], 
        $datos['dias'], 
        $datos['precio_dia'], 
        $datos['fianza'], 
        $datos['total'], 
        $datos['mensaje'], 
        $id_logueado
    );

    if ($stmt->execute()) {
        $mensaje_final = "✅ ¡Ingresado correctamente!";
        unset($_SESSION['reserva_temp']); 
    } else {
        $mensaje_final = "❌ Error al insertar: " . $stmt->error;
    }

} else {
    $mensaje_final = "⚠️ No se pudo intentar la ingresión.";
}
?>

<!DOCTYPE html>
<html lang="es">
<head><meta charset="UTF-8"><title>Resultado</title></head>
<body>
    <h1><?php echo $mensaje_final; ?></h1>
    <p>Total: <?php echo $total_visual; ?> €</p>
    <a href="index.php">Volver</a>
</body>
</html>