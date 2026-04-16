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
<style>
    *{
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: Arial, Helvetica, sans-serif;
    }

    body{
        min-height: 100vh;
        background-color: #f5f1e6; /* beige clarito */
        padding: 20px;
    }

    .container.mt-5{
        max-width: 700px;
        margin: 60px auto !important;
    }

    .card{
        background: rgba(245, 241, 230, 0.85); /* beige transparente */
        border-radius: 20px;
        border: 1px solid rgba(0,0,0,0.1);
        box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        backdrop-filter: blur(4px);
        padding: 40px;
    }

    h2{
        color: #97B770; /* verde del logo */
        font-size: 38px;
        margin-bottom: 25px;
    }

    .list-group-item{
        background: transparent;
        border: none;
        border-bottom: 1px solid rgba(0,0,0,0.1);
        padding: 14px;
        font-size: 16px;
        color: #333;
        transition: 0.2s;
    }

    .list-group-item:hover{
        background: rgba(151, 183, 112, 0.15); /* verde suave */
        color: #97B770;
        cursor: pointer;
    }

    .btn-secondary{
        background-color: #97B770;
        border: none;
        color: white;
        padding: 12px 20px;
        font-weight: bold;
        border-radius: 6px;
        transition: 0.3s;
    }

    .btn-secondary:hover{
        background-color: #7fa45a;
    }

    @media(max-width: 768px){
        .card{
            padding: 25px;
        }

        h2{
            font-size: 28px;
        }
    }
</style>
<body>

    <?php include __DIR__.'/nav_basico.php'; ?>

    <div class="container mt-5">
        <div class="card shadow p-4">

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