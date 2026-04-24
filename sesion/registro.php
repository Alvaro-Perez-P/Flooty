<?php
session_start();

error_reporting(E_ALL);
ini_set("display_errors", 1);
require "conexion.php";

$err_nombre = "";
$err_direc = "";
$err_telf = "";
$err_email = "";
$err_passw = "";
$err_passwConf = "";
$mensaje = "";

$nombre = "";
$direc = "";
$telf = "";
$email = "";
$rol = "usuario";

if($_SERVER["REQUEST_METHOD"] == "POST"){

    $nombre = trim($_POST["nombre"] ?? "");
    $direc = trim($_POST["direccion"] ?? "");
    $telf = trim($_POST["telefono"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $passw = $_POST["passw"] ?? "";
    $passwConf = $_POST["passwConf"] ?? "";
    $rol = $_POST["rol"] ?? "usuario";

    $errores = false;

    if($nombre == ""){
        $err_nombre = "Introduce tu nombre";
        $errores = true;
    }

    if($direc == ""){
        $err_direc = "Introduce una dirección";
        $errores = true;
    }

    if($telf == ""){
        $err_telf = "Introduce un teléfono";
        $errores = true;
    }

    if($email == ""){
        $err_email = "Introduce un email";
        $errores = true;
    }elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)){
        $err_email = "El email no tiene un formato válido";
        $errores = true;
    }

    if($passw == ""){
        $err_passw = "Introduce una contraseña";
        $errores = true;
    }elseif(strlen($passw) < 6){
        $err_passw = "La contraseña debe tener al menos 6 caracteres";
        $errores = true;
    }

    if($passwConf == ""){
        $err_passwConf = "Confirma tu contraseña";
        $errores = true;
    }elseif($passwConf !== $passw){
        $err_passwConf = "Las contraseñas no coinciden";
        $errores = true;
    }

    if(!$errores){
        $usuario = $nombre;
        $clave = password_hash($passw, PASSWORD_DEFAULT);

        $consulta = "INSERT INTO usuarios (email, usuario, clave, rol, nombre, telefono, direccion)
                     VALUES ('$email','$usuario','$clave','$rol','$nombre','$telf','$direc')";

        if($_conexion->query($consulta)){
            $mensaje = "Usuario registrado correctamente";
            $nombre = "";
            $direc = "";
            $telf = "";
            $email = "";
            $rol = "usuario";
        }else{
            $mensaje = "Error al registrar el usuario";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro</title>

    <style>
        *{
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, Helvetica, sans-serif;
        }

        body{
            min-height: 100vh;
            background-image: url("../img/fondo2.jpg");
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .contenedor-principal{
            width: 100%;
            max-width: 1100px;
            min-height: 650px;
            display: flex;
            border-radius: 20px;
            overflow: hidden;
            border: 2px solid rgba(255,255,255,0.25);
            box-shadow: 0 10px 30px rgba(0,0,0,0.35);
            backdrop-filter: blur(3px);
        }

        .lado-izquierdo{
            width: 50%;
            padding: 60px 50px;
            color: white;
            display: flex;
            flex-direction: column;
            justify-content: center;
            background: rgba(0, 0, 0, 0.35);
        }

        .lado-izquierdo p{
            font-size: 20px;
            line-height: 1.6;
            max-width: 450px;
        }

        .lado-derecho{
            width: 50%;
            background: rgba(0, 0, 0, 0.65);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px;
        }

        .caja-formulario{
            width: 100%;
            max-width: 360px;
        }

        .caja-formulario h2{
            font-size: 42px;
            margin-bottom: 30px;
            font-weight: bold;
        }

        .grupo-input{
            margin-bottom: 25px;
        }

        .grupo-input label{
            display: block;
            margin-bottom: 8px;
            font-size: 14px;
            color: #d9d9d9;
        }

        .grupo-input input,
        .grupo-input select{
            width: 100%;
            background: transparent;
            border: none;
            border-bottom: 2px solid rgba(255,255,255,0.35);
            padding: 8px 2px;
            color: white;
            font-size: 15px;
            outline: none;
        }

        .grupo-input input::placeholder{
            color: rgba(255,255,255,0.7);
        }

        .grupo-input input:focus,
        .grupo-input select:focus{
            border-bottom: 2px solid #18a85b;
        }

        .boton{
            width: 100%;
            padding: 12px;
            border: none;
            background-color: #A8CA7E;
            color: white;
            font-size: 18px;
            font-weight: bold;
            border-radius: 4px;
            cursor: pointer;
            margin-top: 10px;
        }

        .texto-abajo{
            text-align: center;
            margin-top: 20px;
            font-size: 14px;
            color: #d6d6d6;
        }

        .texto-abajo a{
            color: #A8CA7E;
            text-decoration: none;
            font-weight: bold;
            margin-left: 5px;
        }

        .mensaje-error{
            background-color: rgba(220, 53, 69, 0.92);
            color: white;
            padding: 8px;
            border-radius: 5px;
            margin-top: 8px;
            font-size: 13px;
        }

        .mensaje-ok{
            background-color: rgba(40, 167, 69, 0.92);
            color: white;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
            font-size: 14px;
        }

        .logo{
            width: 120px;
            margin-bottom: 20px;
        }

        @media(max-width: 900px){
            .contenedor-principal{
                flex-direction: column;
                max-width: 500px;
            }

            .lado-izquierdo,
            .lado-derecho{
                width: 100%;
            }
        }
    </style>
</head>
<body>

<div class="contenedor-principal">

    <div class="lado-izquierdo">
        <img src="../img/logo.png" class="logo" alt="logo">
        <p>
            Regístrate para crear una cuenta y acceder a todas las funcionalidades
            de la plataforma.
        </p>
    </div>

    <div class="lado-derecho">
        <div class="caja-formulario">

            <h2>Registro</h2>

            <?php if($mensaje != ""): ?>
                <div class="mensaje-ok"><?= $mensaje ?></div>
            <?php endif; ?>

            <form method="post">

                <div class="grupo-input">
                    <label>Nombre</label>
                    <input type="text" name="nombre" value="<?= htmlspecialchars($nombre) ?>">
                    <?php if($err_nombre != "") echo "<div class='mensaje-error'>$err_nombre</div>"; ?>
                </div>

                <div class="grupo-input">
                    <label>Dirección</label>
                    <input type="text" name="direccion" value="<?= htmlspecialchars($direc) ?>">
                    <?php if($err_direc != "") echo "<div class='mensaje-error'>$err_direc</div>"; ?>
                </div>

                <div class="grupo-input">
                    <label>Teléfono</label>
                    <input type="text" name="telefono" value="<?= htmlspecialchars($telf) ?>">
                    <?php if($err_telf != "") echo "<div class='mensaje-error'>$err_telf</div>"; ?>
                </div>

                <div class="grupo-input">
                    <label>Email</label>
                    <input type="text" name="email" value="<?= htmlspecialchars($email) ?>">
                    <?php if($err_email != "") echo "<div class='mensaje-error'>$err_email</div>"; ?>
                </div>

                <div class="grupo-input">
                    <label>Contraseña</label>
                    <input type="password" name="passw">
                    <?php if($err_passw != "") echo "<div class='mensaje-error'>$err_passw</div>"; ?>
                </div>

                <div class="grupo-input">
                    <label>Confirmar contraseña</label>
                    <input type="password" name="passwConf">
                    <?php if($err_passwConf != "") echo "<div class='mensaje-error'>$err_passwConf</div>"; ?>
                </div>

                <div class="grupo-input">
                    <label>Rol</label>
                    <select name="rol">
                        <option value="usuario" <?= $rol == "usuario" ? "selected" : "" ?>>usuario</option>
                        <option value="admin" <?= $rol == "admin" ? "selected" : "" ?>>admin</option>
                    </select>
                </div>

                <input type="submit" value="Registrarse" class="boton">
            </form>

            <div class="texto-abajo">
                ¿Ya tienes cuenta?
                <a href="login.php">Inicia sesión</a>
            </div>

        </div>
    </div>

</div>

</body>
</html>