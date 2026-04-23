<?php

$_servidor = "localhost";
$_usuario = "root";
$_contrasena = "";
$_bd = "flooty_bd";

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$_conexion = new mysqli($_servidor, $_usuario, $_contrasena, $_bd);

if ($_conexion->connect_error) {
    die("Error de conexión: " . $_conexion->connect_error);
}
?>