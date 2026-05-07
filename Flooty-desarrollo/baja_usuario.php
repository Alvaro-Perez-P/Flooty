<?php
session_start();
// Importante: ajusta la ruta a tu archivo de conexión según tu estructura
require 'sesion/conexion.php'; 

// 1. Verificación de seguridad: si no hay sesión, al login
if (!isset($_SESSION["usuario"])) {
    header("location: sesion/login.php");
    exit();
}

// 2. Obtenemos el nombre de usuario de la sesión actual
$user_session = $_SESSION["usuario"];

// 3. Ejecutamos el "Borrado Lógico" (cambiar activo a 0)
// Usamos el nombre de usuario que tenemos en la sesión para identificarlo
$sql = "UPDATE usuarios SET activo = 0 WHERE usuario = '$user_session'";

if ($_conexion->query($sql)) {
    // 4. Si la actualización fue exitosa, cerramos la sesión
    // ya que el usuario ya no debe poder navegar por su panel
    session_destroy();
    
    // 5. Redirigimos al login con un mensaje de éxito en la URL
    header("location: sesion/login.php?baja=exitosa");
    exit();
} else {
    // Si algo falla, mostramos el error 
    echo "Error al procesar la baja: " . $_conexion->error;
}
?>