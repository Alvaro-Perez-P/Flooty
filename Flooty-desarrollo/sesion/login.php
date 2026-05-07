<?php
session_start();

error_reporting(E_ALL);
ini_set("display_errors", 1);

require "conexion.php";

$mensaje = "";
$err_email = "";
$err_contrasena = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $tmp_email = trim($_POST["email"] ?? "");
    $tmp_contrasena = trim($_POST["passw"] ?? "");

    $errores = false;

    if ($tmp_email == "") {
        $err_email = "Introduce un email";
        $errores = true;
    } elseif (!filter_var($tmp_email, FILTER_VALIDATE_EMAIL)) {
        $err_email = "El email debe tener un formato válido y contener @";
        $errores = true;
    } else {
        $email = $tmp_email;
    }

    if ($tmp_contrasena == "") {
        $err_contrasena = "Introduce una contraseña";
        $errores = true;
    } else {
        $contrasena = $tmp_contrasena;
    }

    if (!$errores) {

    //aqui vemos que el usuario tenga email y este en activo
        $consulta = "SELECT * FROM usuarios WHERE email = '$email' AND activo = 1";
        $resultado = $_conexion->query($consulta);

        if ($resultado->num_rows === 0) {
            $mensaje = "El email no existe en la base de datos";
        } else {
            $user_info = $resultado->fetch_assoc();

            $acceso_concedido = password_verify($contrasena, $user_info["clave"]);

            if (!$acceso_concedido) {
                $mensaje = "Contraseña incorrecta";
            } else {
                if ($user_info["rol"] != "usuario" && $user_info["rol"] != "admin") {
                    $mensaje = "Acceso denegado. Solo pueden entrar usuarios o administradores";
                } else {
                    $_SESSION["usuario"] = $user_info["usuario"];
                    $_SESSION["rol"] = $user_info["rol"];

                    header("Location: ../index.php");
                    exit();
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, Helvetica, sans-serif;
        }

        body {
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

        .contenedor-principal {
            width: 100%;
            max-width: 1100px;
            min-height: 650px;
            display: flex;
            border-radius: 20px;
            overflow: hidden;
            border: 2px solid rgba(255, 255, 255, 0.25);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.35);
            backdrop-filter: blur(3px);
        }

        .lado-izquierdo {
            width: 50%;
            padding: 60px 50px;
            color: white;
            display: flex;
            flex-direction: column;
            justify-content: center;
            background: rgba(0, 0, 0, 0.35);
        }

        .lado-izquierdo p {
            font-size: 20px;
            line-height: 1.6;
            max-width: 450px;
        }

        .lado-derecho {
            width: 50%;
            background: rgba(0, 0, 0, 0.65);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px;
        }

        .caja-formulario {
            width: 100%;
            max-width: 360px;
        }

        .caja-formulario h2 {
            font-size: 48px;
            margin-bottom: 35px;
            font-weight: bold;
        }

        .grupo-input {
            margin-bottom: 28px;
        }

        .grupo-input label {
            display: block;
            margin-bottom: 10px;
            font-size: 15px;
            color: #d9d9d9;
        }

        .grupo-input input {
            width: 100%;
            background: transparent;
            border: none;
            border-bottom: 2px solid rgba(255, 255, 255, 0.35);
            padding: 10px 2px;
            color: white;
            font-size: 16px;
            outline: none;
        }

        .grupo-input input::placeholder {
            color: rgba(255, 255, 255, 0.7);
        }

        .grupo-input input:focus {
            border-bottom: 2px solid #18a85b;
        }

        .boton-login {
            width: 100%;
            padding: 14px;
            border: none;
            background-color: #A8CA7E;
            color: white;
            font-size: 22px;
            font-weight: bold;
            border-radius: 4px;
            cursor: pointer;
            transition: 0.3s;
            margin-top: 15px;
        }

        .boton-login:hover {
            background-color: #A8CA7E;
        }

        .texto-abajo {
            text-align: center;
            margin-top: 25px;
            font-size: 15px;
            color: #d6d6d6;
        }

        .texto-abajo a {
            color: #A8CA7E;
            text-decoration: none;
            font-weight: bold;
            margin-left: 8px;
        }

        .texto-abajo a:hover {
            text-decoration: underline;
        }

        .mensaje-error {
            background-color: rgba(220, 53, 69, 0.92);
            color: white;
            padding: 10px 14px;
            border-radius: 6px;
            margin-top: 10px;
            font-size: 14px;
        }

        .mensaje-general {
            background-color: rgba(220, 53, 69, 0.92);
            color: white;
            padding: 12px 15px;
            border-radius: 6px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .logo {
            width: 120px;
            height: auto;
            margin-bottom: 20px;
        }

        @media(max-width: 900px) {
            .contenedor-principal {
                flex-direction: column;
                max-width: 500px;
            }

            .lado-izquierdo,
            .lado-derecho {
                width: 100%;
            }

            .lado-izquierdo {
                min-height: 220px;
            }

            .caja-formulario h2 {
                font-size: 36px;
            }
        }
    </style>
</head>

<body>

    <div class="contenedor-principal">
        <div class="lado-izquierdo">
            <img src="../img/logo.png" alt="logoFlooty" class="logo">
            <p>
                Aquí puedes iniciar sesión para acceder a tu cuenta, gestionar tus datos
                y entrar en la plataforma.
            </p>
        </div>

        <div class="lado-derecho">
            <div class="caja-formulario">
                <h2>Login</h2>

                <?php if ($mensaje != "") { ?>
                    <div class="mensaje-general"><?= $mensaje ?></div>
                <?php } ?>

                <form action="" method="post">

                    <div class="grupo-input">
                        <label>Email</label>
                        <input type="text" name="email" placeholder="Introduce tu email" value="<?= htmlspecialchars($_POST["email"] ?? "") ?>">
                        <?php if ($err_email != "") { ?>
                            <div class="mensaje-error"><?= $err_email ?></div>
                        <?php } ?>
                    </div>

                    <div class="grupo-input">
                        <label>Contraseña</label>
                        <input type="password" name="passw" placeholder="Introduce tu contraseña">
                        <?php if ($err_contrasena != "") { ?>
                            <div class="mensaje-error"><?= $err_contrasena ?></div>
                        <?php } ?>
                    </div>

                    <input type="submit" value="Iniciar sesión" class="boton-login">
                </form>

                <div class="texto-abajo">
                    ¿No tienes cuenta?
                    <a href="registro.php">Regístrate aquí</a>
                </div>
            </div>
        </div>
    </div>

</body>

</html>