<?php
session_start();
error_reporting(E_ALL);
ini_set("display_errors", 1);

if (!isset($_SESSION["usuario"])) {
    header("location: sesion/login.php");
    exit();
}

require 'sesion/conexion.php';

// 1. Recopilamos la información actual del usuario
$user_session = $_SESSION["usuario"];
$consulta = "SELECT nombre, telefono, direccion, imagen, usuario FROM usuarios WHERE usuario = '$user_session'";
$resultado = $_conexion->query($consulta);
$user_data = $resultado->fetch_assoc();

// Lógica para la inicial del Avatar
$inicial = strtoupper(substr($user_data['usuario'], 0, 1));
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Mis Datos - Flooty</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --verde-hielo: #F7FEEF;
            --verde-suave: #CBDDB5;
            --verde-natural: #A8CA7E;
            --verde-organico: #97B770;
            --verde-profundo: rgb(96, 131, 52);
        }

        body {
            background-color: #f5f1e6;
            padding: 40px;
            font-family: sans-serif;
        }

        .form-container {
            max-width: 800px;
            margin: auto;
            background: rgba(255, 255, 255, 0.9);
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        h1,
        h2 {
            color: var(--verde-profundo);
            font-weight: bold;
        }

        h1 {
            border-bottom: 2px solid var(--verde-natural);
            padding-bottom: 10px;
            margin-bottom: 30px;
        }

        /* Estilo Avatar */
        .avatar-wrapper {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background-color: var(--verde-natural);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px auto;
            color: white;
            font-size: 50px;
            font-weight: bold;
            overflow: hidden;
            border: 4px solid white;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .avatar-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .btn-flooty {
            background-color: var(--verde-natural);
            color: white;
            font-weight: bold;
            border: none;
            padding: 10px 25px;
        }

        .btn-flooty:hover {
            background-color: var(--verde-organico);
            color: white;
        }

        .btn-baja {
            color: #dc3545;
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: bold;
        }
    </style>
</head>

<body>

    <div class="form-container">
        <h1>Mis Datos</h1>

        <form action="actualizar_perfil.php" method="POST" enctype="multipart/form-data">
            <div class="text-center mb-4">
                <div class="avatar-wrapper">
                    <?php if (!empty($user_data['imagen'])): ?>
                        <img src="img/avatares/<?= $user_data['imagen'] ?>" class="avatar-img">
                    <?php else: ?>
                        <?= $inicial ?>
                    <?php endif; ?>
                </div>
                <input type="file" name="avatar" class="form-control form-control-sm mx-auto" style="max-width: 250px;">
            </div>

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Nombre</label>
                    <input type="text" name="nombre" class="form-control" value="<?= htmlspecialchars($user_data['nombre'] ?? '') ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Teléfono</label>
                    <input type="text" name="telefono" class="form-control" value="<?= htmlspecialchars($user_data['telefono'] ?? '') ?>">
                </div>
                <div class="col-12">
                    <label class="form-label">Dirección</label>
                    <input type="text" name="direccion" class="form-control" value="<?= htmlspecialchars($user_data['direccion'] ?? '') ?>">
                </div>
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-flooty w-100">Guardar Cambios</button>
            </div>
        </form>

        <hr class="my-5">

        <h2>Cambiar Contraseña</h2>
        <form action="actualizar_password.php" method="POST" class="mt-3">
            <div class="mb-3">
                <label class="form-label">Contraseña actual</label>
                <input type="password" name="pass_actual" class="form-control">
            </div>
            <div class="row mb-3">
                <div class="col">
                    <label class="form-label">Nueva contraseña</label>
                    <input type="password" name="pass_nueva" class="form-control">
                </div>
                <div class="col">
                    <label class="form-label">Confirmación</label>
                    <input type="password" name="pass_confirm" class="form-control">
                </div>
            </div>
            <button type="submit" class="btn btn-flooty">Actualizar Contraseña</button>
        </form>

        <div class="text-center mt-5">
            <a href="baja_usuario.php" class="btn-baja" onclick="return confirm('¿Seguro que deseas darte de baja?')">
                <i class="fas fa-user-slash"></i> Darme de baja
            </a>
        </div>
    </div>

</body>

</html>