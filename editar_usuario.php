<?php
session_start();

error_reporting(E_ALL);
ini_set("display_errors", 1);

if (!isset($_SESSION["usuario"])) {
    header("location: sesion/login.php");
    exit();
}

if (!isset($_SESSION["rol"]) || $_SESSION["rol"] != "admin") {
    header("location: index.php");
    exit();
}

require "sesion/conexion.php";

if (!isset($_GET["id"])) {
    header("location: listado_usuarios.php");
    exit();
}

$id = $_GET["id"];

$consulta = "SELECT * FROM usuarios WHERE id = '$id'";
$resultado = $_conexion->query($consulta);

if ($resultado->num_rows === 0) {
    header("location: listado_usuarios.php");
    exit();
}

$usuario_editar = $resultado->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $tmp_email = $_POST["email"];
    $tmp_usuario = $_POST["usuario"];
    $tmp_clave = $_POST["clave"];
    $tmp_rol = $_POST["rol"];

    $errores = false;

    // VALIDACIÓN EMAIL
    $tmp_email = trim($tmp_email);

    if ($tmp_email == "") {
        $err_email = "Introduce un email";
        $errores = true;
    } elseif (!filter_var($tmp_email, FILTER_VALIDATE_EMAIL)) {
        $err_email = "El email debe tener un formato válido y contener @";
        $errores = true;
    } else {
        $email = $tmp_email;
    }

    // VALIDACIÓN USUARIO
    $tmp_usuario = trim($tmp_usuario);

    if ($tmp_usuario == "") {
        $err_usuario = "Introduce un usuario";
        $errores = true;
    } else {
        $usuario = $tmp_usuario;
    }

    // VALIDACIÓN ROL
    $tmp_rol = trim($tmp_rol);

    if ($tmp_rol == "") {
        $err_rol = "Selecciona un rol";
        $errores = true;
    } elseif ($tmp_rol != "usuario" && $tmp_rol != "admin") {
        $err_rol = "Rol no válido";
        $errores = true;
    } else {
        $rol = $tmp_rol;
    }

    // VALIDACIÓN CLAVE
    $tmp_clave = trim($tmp_clave);

    if ($tmp_clave == "") {
        $clave_final = $usuario_editar["clave"];
    } else {
        $clave_final = password_hash($tmp_clave, PASSWORD_DEFAULT);
    }

    if (!$errores) {
        $consulta = "UPDATE usuarios 
                     SET email = '$email',
                         usuario = '$usuario',
                         clave = '$clave_final',
                         rol = '$rol'
                     WHERE id = '$id'";

        if ($_conexion->query($consulta)) {
            header("location: listado_usuarios.php");
            exit();
        } else {
            echo "<div class='alert alert-danger text-center'>Error al editar el usuario</div>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar usuario</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body style="background-color: #f5f5dc;">

<h1 class="text-center mt-4">Bienvenido a FLOOTY</h1>

<div class="container d-flex justify-content-center align-items-center vh-100">
    <div class="p-4 rounded shadow w-100" style="max-width: 450px; background-color: #198754;">

        <h2 class="text-center mb-4 text-white">Editar usuario</h2>

        <form action="" method="post">

            <div class="mb-3">
                <label class="form-label text-white">Email</label>
                <input type="text" name="email" class="form-control" value="<?= htmlspecialchars($usuario_editar["email"]) ?>">
                <?php
                    if (isset($err_email)) {
                        echo "<div class='alert alert-danger mt-2'>$err_email</div>";
                    }
                ?>
            </div>

            <div class="mb-3">
                <label class="form-label text-white">Usuario</label>
                <input type="text" name="usuario" class="form-control" value="<?= htmlspecialchars($usuario_editar["usuario"]) ?>">
                <?php
                    if (isset($err_usuario)) {
                        echo "<div class='alert alert-danger mt-2'>$err_usuario</div>";
                    }
                ?>
            </div>

            <div class="mb-3">
                <label class="form-label text-white">Nueva contraseña</label>
                <input type="password" name="clave" class="form-control" placeholder="Solo rellena si la quieres cambiar">
            </div>

            <div class="mb-3">
                <label class="form-label text-white">Rol</label>
                <select name="rol" class="form-select">
                    <option value="">-- Selecciona un rol --</option>
                    <option value="usuario" <?php if($usuario_editar["rol"] == "usuario") echo "selected"; ?>>usuario</option>
                    <option value="admin" <?php if($usuario_editar["rol"] == "admin") echo "selected"; ?>>admin</option>
                </select>
                <?php
                    if (isset($err_rol)) {
                        echo "<div class='alert alert-danger mt-2'>$err_rol</div>";
                    }
                ?>
            </div>

            <div class="mb-3">
                <input type="submit" value="Editar usuario" class="btn w-100" style="background-color: #8b4513; color: white;">
            </div>
        </form>

        <a href="listado_usuarios.php" class="btn w-100" style="background-color: #6c757d; color: white;">Volver</a>
    </div>
</div>

</body>
</html>