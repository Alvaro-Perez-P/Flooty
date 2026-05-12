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

$pass_actual = $_POST["pass_actual"] ?? "";
$pass_nueva = $_POST["pass_nueva"] ?? "";
$pass_confirm = $_POST["pass_confirm"] ?? "";

if ($pass_actual == "" || $pass_nueva == "" || $pass_confirm == "") {
    header("location: mis_datos.php?error=Completa todos los campos de contraseña");
    exit();
}

if ($pass_nueva !== $pass_confirm) {
    header("location: mis_datos.php?error=Las contraseñas nuevas no coinciden");
    exit();
}

if (strlen($pass_nueva) < 6) {
    header("location: mis_datos.php?error=La nueva contraseña debe tener al menos 6 caracteres");
    exit();
}

$consulta = "SELECT id, clave FROM usuarios WHERE usuario = '$usuario_sesion' LIMIT 1";
$resultado = $_conexion->query($consulta);

if (!$resultado || $resultado->num_rows == 0) {
    header("location: mis_datos.php?error=Usuario no encontrado");
    exit();
}

$usuario = $resultado->fetch_assoc();
$id_usuario = (int)$usuario["id"];
$clave_bd = $usuario["clave"];

/* Sirve para claves con password_hash */
$clave_correcta = password_verify($pass_actual, $clave_bd);

/* Por si tenías alguna contraseña antigua sin hash */
if (!$clave_correcta && $pass_actual === $clave_bd) {
    $clave_correcta = true;
}

if (!$clave_correcta) {
    header("location: mis_datos.php?error=La contraseña actual no es correcta");
    exit();
}

$nuevo_hash = password_hash($pass_nueva, PASSWORD_DEFAULT);
$nuevo_hash_seguro = $_conexion->real_escape_string($nuevo_hash);

$update = "UPDATE usuarios SET clave = '$nuevo_hash_seguro' WHERE id = $id_usuario";

if ($_conexion->query($update)) {
    header("location: mis_datos.php?mensaje=Contraseña actualizada correctamente");
    exit();
} else {
    header("location: mis_datos.php?error=Error al actualizar la contraseña");
    exit();
}
