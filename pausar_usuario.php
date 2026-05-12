<?php
session_start();

error_reporting(E_ALL);
ini_set("display_errors", 1);

/*
|--------------------------------------------------------------------------
| SEGURIDAD
|--------------------------------------------------------------------------
*/

if (!isset($_SESSION["usuario"])) {
    header("location: sesion/login.php");
    exit();
}

if (!isset($_SESSION["rol"]) || $_SESSION["rol"] != "admin") {
    header("location: index.php");
    exit();
}

/*
|--------------------------------------------------------------------------
| CONEXIÓN
|--------------------------------------------------------------------------
*/

require_once __DIR__ . "/sesion/conexion.php";

/*
|--------------------------------------------------------------------------
| VALIDAR ID
|--------------------------------------------------------------------------
*/

if (!isset($_GET["id"])) {
    header("location: panel_usuarios.php");
    exit();
}

$id = intval($_GET["id"]);

/*
|--------------------------------------------------------------------------
| BUSCAR USUARIO
|--------------------------------------------------------------------------
*/

$consulta = "SELECT * FROM usuarios WHERE id = '$id'";
$resultado = $_conexion->query($consulta);

if ($resultado->num_rows === 0) {
    header("location: panel_usuarios.php");
    exit();
}

$usuario = $resultado->fetch_assoc();

/*
|--------------------------------------------------------------------------
| CAMBIAR ESTADO
|--------------------------------------------------------------------------
*/

$activo_actual = $usuario["activo"];

/*
|--------------------------------------------------------------------------
| SI ES 1 -> PASA A 0
| SI ES 0 -> PASA A 1
|--------------------------------------------------------------------------
*/

if ($activo_actual == 1) {

    $nuevo_estado = 0;

} else {

    $nuevo_estado = 1;

}

/*
|--------------------------------------------------------------------------
| ACTUALIZAR USUARIO
|--------------------------------------------------------------------------
*/

$update = "UPDATE usuarios 
           SET activo = '$nuevo_estado'
           WHERE id = '$id'";

$_conexion->query($update);

/*
|--------------------------------------------------------------------------
| REDIRECCIÓN
|--------------------------------------------------------------------------
*/

header("location: panel_usuarios.php");
exit();
?>