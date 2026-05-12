<?php
session_start();
error_reporting(E_ALL);
ini_set("display_errors", 1);

if (!isset($_SESSION["usuario"])) {
    header("location: sesion/login.php");
    exit();
}

require 'sesion/conexion.php';

$usuario_sesion = $_conexion->real_escape_string($_SESSION["usuario"]);

$consulta_usuario = "SELECT id, imagen FROM usuarios WHERE usuario = '$usuario_sesion' LIMIT 1";
$resultado_usuario = $_conexion->query($consulta_usuario);

if (!$resultado_usuario || $resultado_usuario->num_rows == 0) {
    header("location: mis_datos.php?error=Usuario no encontrado");
    exit();
}

$usuario = $resultado_usuario->fetch_assoc();
$id_usuario = (int)$usuario["id"];
$imagen_actual = $usuario["imagen"] ?? "";

$email = trim($_POST["email"] ?? "");
$nombre = trim($_POST["nombre"] ?? "");
$telefono = trim($_POST["telefono"] ?? "");
$direccion = trim($_POST["direccion"] ?? "");

if ($email == "") {
    header("location: mis_datos.php?error=El email es obligatorio");
    exit();
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header("location: mis_datos.php?error=El email no es válido");
    exit();
}

/* Comprobar que el email no esté usado por otro usuario */
$email_seguro = $_conexion->real_escape_string($email);

$consulta_email = "
    SELECT id 
    FROM usuarios 
    WHERE email = '$email_seguro' 
    AND id != $id_usuario
    LIMIT 1
";
$resultado_email = $_conexion->query($consulta_email);

if ($resultado_email && $resultado_email->num_rows > 0) {
    header("location: mis_datos.php?error=Ese email ya está usado por otro usuario");
    exit();
}

$ruta_imagen = $imagen_actual;

/* Subir avatar */
if (isset($_FILES["avatar"]) && $_FILES["avatar"]["name"] != "") {

    if ($_FILES["avatar"]["error"] !== 0) {
        header("location: mis_datos.php?error=Error al subir la imagen");
        exit();
    }

    $carpeta = __DIR__ . "/img/avatares/";

    if (!is_dir($carpeta)) {
        mkdir($carpeta, 0777, true);
    }

    $tmp = $_FILES["avatar"]["tmp_name"];
    $tipo = mime_content_type($tmp);

    $permitidos = ["image/jpeg", "image/png", "image/webp", "image/jpg"];

    if (!in_array($tipo, $permitidos)) {
        header("location: mis_datos.php?error=Solo se permiten imágenes JPG, PNG o WEBP");
        exit();
    }

    $extension = strtolower(pathinfo($_FILES["avatar"]["name"], PATHINFO_EXTENSION));
    $nombre_archivo = "avatar_" . $id_usuario . "_" . time() . "." . $extension;
    $destino = $carpeta . $nombre_archivo;

    if (!move_uploaded_file($tmp, $destino)) {
        header("location: mis_datos.php?error=No se pudo guardar la imagen");
        exit();
    }

    $ruta_imagen = "img/avatares/" . $nombre_archivo;
}

$nombre_seguro = $_conexion->real_escape_string($nombre);
$telefono_seguro = $_conexion->real_escape_string($telefono);
$direccion_segura = $_conexion->real_escape_string($direccion);
$imagen_segura = $_conexion->real_escape_string($ruta_imagen);

$update = "
    UPDATE usuarios SET
        email = '$email_seguro',
        nombre = '$nombre_seguro',
        telefono = '$telefono_seguro',
        direccion = '$direccion_segura',
        imagen = '$imagen_segura'
    WHERE id = $id_usuario
";

if ($_conexion->query($update)) {
    header("location: mis_datos.php?mensaje=Perfil actualizado correctamente");
    exit();
} else {
    header("location: mis_datos.php?error=Error al actualizar perfil");
    exit();
}
