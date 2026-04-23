<?php
session_start();

error_reporting(E_ALL);
ini_set("display_errors", 1);

if (!isset($_SESSION["usuario"])) {
    header("location: sesion/login.php");
    exit();
}

if (!isset($_SESSION["rol"]) || ($_SESSION["rol"] != "usuario" && $_SESSION["rol"] != "admin")) {
    header("location: sesion/login.php");
    exit();
}

require 'sesion/conexion.php';

$consulta = "SELECT * FROM usuarios WHERE usuario = '".$_SESSION["usuario"]."'";
$resultado = $_conexion->query($consulta);
$user_info = $resultado->fetch_assoc();
$user_name = $user_info["usuario"];
$user_rol = $user_info["rol"];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio</title>
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

    .container.text-center.mt-5{
        width: 100%;
        max-width: 700px;
        margin: 60px auto 0 auto !important;

        background: rgba(245, 241, 230, 0.85); /* beige transparente */
        color: #333;

        padding: 50px 40px;
        border-radius: 20px;
        border: 1px solid rgba(0,0,0,0.1);
        box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        backdrop-filter: blur(4px);
    }

    .container.text-center.mt-5 h2{
        font-size: 42px;
        margin-bottom: 30px;
        font-weight: bold;
        color: #97B770;
    }

    .d-grid.gap-3.col-6.mx-auto.mt-4{
        width: 100% !important;
        max-width: 320px;
        margin: 0 auto;
    }

    .btn{
        width: 100%;
        padding: 14px;
        border: none;
        font-size: 20px;
        font-weight: bold;
        border-radius: 6px;
        cursor: pointer;
        transition: 0.3s;
        text-decoration: none;
    }

    .btn-primary{
        background-color: #A8CA7E;
        color: white;
    }

    .btn-primary:hover{
        background-color: #97B770;
        color: white;
    }

    .btn-warning{
        background-color: #d8b24c;
        color: white;
    }

    .btn-warning:hover{
        background-color: #c49d35;
        color: white;
    }

    .btn-danger{
        background-color: rgba(220, 53, 69, 0.92);
        color: white;
    }

    .btn-danger:hover{
        background-color: rgba(200, 35, 51, 0.95);
        color: white;
    }

    @media(max-width: 768px){
        .container.text-center.mt-5{
            padding: 35px 20px;
            margin-top: 30px !important;
        }

        .container.text-center.mt-5 h2{
            font-size: 30px;
        }

        .btn{
            font-size: 18px;
            padding: 12px;
        }
    }
</style>
<body>
    <?php include __DIR__.'/nav_basico.php'; ?>

    <div class="container text-center mt-5">
        <div class="d-grid gap-3 col-6 mx-auto mt-4">

            <?php if($user_rol == "admin"): ?>
                <a href="panel_admin.php" class="btn btn-warning">Panel Administrador</a>
            <?php endif; ?>

            <a href="panel_usuario.php" class="btn btn-primary">Panel Usuario</a>

        </div>
    </div>

</body>
</html>