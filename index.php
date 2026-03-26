<?php
session_start();

error_reporting(E_ALL);
ini_set("display_errors", 1);

// Verificar sesión
if (!isset($_SESSION["usuario"])) {
    header("location: sesion/login.php");
    exit();
}

// Verificar rol (usuario o admin)
if (!isset($_SESSION["rol"]) || ($_SESSION["rol"] != "usuario" && $_SESSION["rol"] != "admin")) {
    header("location: sesion/login.php");
    exit();
}

require 'sesion/conexion.php';

// Obtener datos del usuario
$consulta = "SELECT * FROM usuarios WHERE usuario = '".$_SESSION["usuario"]."'";
$resultado = $_conexion->query($consulta);
$user_info = $resultado->fetch_assoc();
$user_name = $user_info["usuario"];
$user_rol = $user_info["rol"];
?>

<h1 style="text-align:center; margin-top:20px;">Bienvenido a FLOOTY</h1>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

    <div class="container text-center mt-5">
        <h2>Hola <?= htmlspecialchars($user_name) ?> (<?= htmlspecialchars($user_rol) ?>)</h2>

        <div class="d-grid gap-3 col-6 mx-auto mt-4">

            <a href="sesion/logout.php" class="btn btn-danger">Cerrar sesión</a>

        </div>
    </div>

</body>
</html>