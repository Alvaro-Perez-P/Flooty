<?php
session_start();

error_reporting(E_ALL);
ini_set("display_errors", 1);

if (!isset($_SESSION["usuario"])) {
    header("Location: sesion/login.php");
    exit();
}

if (!isset($_SESSION["rol"]) || ($_SESSION["rol"] != "usuario" && $_SESSION["rol"] != "admin")) {
    header("Location: sesion/login.php");
    exit();
}

require 'sesion/conexion.php';

$consulta = "SELECT * FROM usuarios WHERE usuario = '".$_SESSION["usuario"]."'";
$resultado = $_conexion->query($consulta);
$user_info = $resultado->fetch_assoc();
$user_name = $user_info["usuario"];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de usuario</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

    <?php include __DIR__.'/nav_basico.php'; ?>

    <div class="container mt-5">
        <div class="card shadow p-4">
            <h2 class="mb-4">Hola <?= htmlspecialchars($user_name) ?></h2>

            <div class="list-group">
                <a href="#" class="list-group-item list-group-item-action">Mis reservas</a>
                <a href="#" class="list-group-item list-group-item-action">Reservas recibidas</a>
                <a href="#" class="list-group-item list-group-item-action">Mis anuncios</a>
                <a href="#" class="list-group-item list-group-item-action">Mis datos</a>
            </div>

            <div class="mt-4">
                <a href="index.php" class="btn btn-secondary">Volver</a>
            </div>
        </div>
    </div>

</body>
</html>