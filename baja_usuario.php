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

/* Desactivar usuario */
$update = "
    UPDATE usuarios 
    SET activo = 0
    WHERE usuario = '$usuario_sesion'
    LIMIT 1
";

if ($_conexion->query($update)) {
    session_destroy();
    header("location: index.php");
    exit();
} else {
    header("location: mis_datos.php?error=No se pudo dar de baja la cuenta");
    exit();
}
