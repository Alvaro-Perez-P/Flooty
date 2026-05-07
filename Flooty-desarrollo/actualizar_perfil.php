<?php
session_start();
require 'sesion/conexion.php';

if (!isset($_SESSION["usuario"])) {
    header("location: sesion/login.php");
    exit();
}

$user_session = $_SESSION["usuario"];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST["nombre"] ?? "";
    $telefono = $_POST["telefono"] ?? "";
    $direccion = $_POST["direccion"] ?? "";
    
    // 1. Manejo de la Imagen (Avatar)
    $nombre_imagen = null;
    if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
        $ruta_temporal = $_FILES['avatar']['tmp_name'];
        $extension = pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION);
        // Creamos un nombre único para la imagen para que no se sobrescriba
        $nombre_imagen = "avatar_" . time() . "." . $extension;
        $directorio_destino = "img/avatares/";

        // Si no existe la carpeta, la creamos
        if (!is_dir($directorio_destino)) {
            mkdir($directorio_destino, 0777, true);
        }

        move_uploaded_file($ruta_temporal, $directorio_destino . $nombre_imagen);
    }

    // 2. Preparamos la consulta de actualización
    if ($nombre_imagen) {
        // Si subió imagen, actualizamos todo incluyendo el campo imagen
        $sql = "UPDATE usuarios SET nombre = '$nombre', telefono = '$telefono', direccion = '$direccion', imagen = '$nombre_imagen' WHERE usuario = '$user_session'";
    } else {
        // Si no subió imagen, solo actualizamos los campos de texto
        $sql = "UPDATE usuarios SET nombre = '$nombre', telefono = '$telefono', direccion = '$direccion' WHERE usuario = '$user_session'";
    }

    if ($_conexion->query($sql)) {
        // Redirigimos de vuelta a la página de datos con un mensaje de éxito
        header("Location: mis_datos.php?actualizado=1");
    } else {
        echo "Error al actualizar: " . $_conexion->error;
    }
}
?>