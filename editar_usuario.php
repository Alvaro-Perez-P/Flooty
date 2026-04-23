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

    $tmp_email    = trim($_POST["email"]);
    $tmp_usuario  = trim($_POST["usuario"]);
    $tmp_clave    = trim($_POST["clave"]);
    $tmp_rol      = trim($_POST["rol"]);

    $errores = false;

    if ($tmp_email == "") {
        $err_email = "Introduce un email";
        $errores = true;
    } elseif (!filter_var($tmp_email, FILTER_VALIDATE_EMAIL)) {
        $err_email = "El email debe tener un formato valido y contener @";
        $errores = true;
    } else {
        $email = $tmp_email;
    }

    if ($tmp_usuario == "") {
        $err_usuario = "Introduce un usuario";
        $errores = true;
    } else {
        $usuario = $tmp_usuario;
    }

    if ($tmp_rol == "" || ($tmp_rol != "usuario" && $tmp_rol != "admin")) {
        $err_rol = "Selecciona un rol valido";
        $errores = true;
    } else {
        $rol = $tmp_rol;
    }

    if ($tmp_clave == "") {
        $clave_final = $usuario_editar["clave"];
    } else {
        $clave_final = password_hash($tmp_clave, PASSWORD_DEFAULT);
    }

    if (!$errores) {
        $consulta_update = "UPDATE usuarios
                            SET email = '$email',
                                usuario = '$usuario',
                                clave = '$clave_final',
                                rol = '$rol'
                            WHERE id = '$id'";

        if ($_conexion->query($consulta_update)) {
            header("location: listado_usuarios.php");
            exit();
        } else {
            $mensaje_error = "Error al editar el usuario. Intentalo de nuevo.";
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

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, Helvetica, sans-serif;
        }

        body {
            min-height: 100vh;
            background-color: #f5f1e6;
            padding: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .contenedor {
            width: 100%;
            max-width: 500px;
            margin: 40px auto;
            background: rgba(245, 241, 230, 0.95);
            border-radius: 20px;
            border: 1px solid rgba(0,0,0,0.1);
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
            padding: 40px;
        }

        .contenedor h2 {
            font-size: 36px;
            font-weight: bold;
            color: #97B770;
            margin-bottom: 30px;
            text-align: center;
        }

        .grupo-input {
            margin-bottom: 25px;
        }

        .grupo-input label {
            display: block;
            margin-bottom: 8px;
            font-size: 14px;
            color: #555;
        }

        .grupo-input input,
        .grupo-input select {
            width: 100%;
            background: white;
            border: none;
            border-bottom: 2px solid rgba(0,0,0,0.2);
            padding: 10px 6px;
            color: #333;
            font-size: 15px;
            outline: none;
            border-radius: 4px 4px 0 0;
        }

        .grupo-input input:focus,
        .grupo-input select:focus {
            border-bottom: 2px solid #18a85b;
        }

        .boton {
            width: 100%;
            padding: 12px;
            border: none;
            font-size: 17px;
            font-weight: bold;
            border-radius: 6px;
            cursor: pointer;
            transition: 0.3s;
            text-decoration: none;
            display: inline-block;
            text-align: center;
            margin-top: 8px;
        }

        .boton-guardar {
            background-color: #A8CA7E;
            color: white;
        }

        .boton-guardar:hover {
            background-color: #97B770;
        }

        .boton-volver {
            background-color: #6c757d;
            color: white;
            margin-top: 12px;
        }

        .boton-volver:hover {
            background-color: #5a6268;
            color: white;
        }

        .mensaje-error {
            background-color: rgba(220, 53, 69, 0.92);
            color: white;
            padding: 8px;
            border-radius: 5px;
            margin-top: 8px;
            font-size: 13px;
        }

        .mensaje-general {
            background-color: rgba(220, 53, 69, 0.92);
            color: white;
            padding: 12px 15px;
            border-radius: 6px;
            margin-bottom: 20px;
            font-size: 14px;
            text-align: center;
        }

        @media(max-width: 600px) {
            .contenedor {
                padding: 25px 18px;
            }
        }
    </style>
</head>
<body>

<?php include __DIR__ . '/nav_basico.php'; ?>

<div class="contenedor">

    <h2>Editar usuario</h2>

    <?php if (isset($mensaje_error)) { ?>
        <div class="mensaje-general"><?= $mensaje_error ?></div>
    <?php } ?>

    <form action="" method="post">

        <div class="grupo-input">
            <label>Email</label>
            <input type="text" name="email" value="<?= htmlspecialchars($usuario_editar["email"]) ?>">
            <?php if (isset($err_email)) echo "<div class='mensaje-error'>$err_email</div>"; ?>
        </div>

        <div class="grupo-input">
            <label>Usuario</label>
            <input type="text" name="usuario" value="<?= htmlspecialchars($usuario_editar["usuario"]) ?>">
            <?php if (isset($err_usuario)) echo "<div class='mensaje-error'>$err_usuario</div>"; ?>
        </div>

        <div class="grupo-input">
            <label>Nueva contraseña <small style="color:#888">(dejar vacío para no cambiarla)</small></label>
            <input type="password" name="clave" placeholder="********">
        </div>

        <div class="grupo-input">
            <label>Rol</label>
            <select name="rol">
                <option value="usuario" <?= $usuario_editar["rol"] == "usuario" ? "selected" : "" ?>>usuario</option>
                <option value="admin"   <?= $usuario_editar["rol"] == "admin"   ? "selected" : "" ?>>admin</option>
            </select>
            <?php if (isset($err_rol)) echo "<div class='mensaje-error'>$err_rol</div>"; ?>
        </div>

        <input type="submit" value="Guardar cambios" class="boton boton-guardar">
    </form>

    <a href="listado_usuarios.php" class="boton boton-volver">Volver al listado</a>

</div>

</body>
</html>
