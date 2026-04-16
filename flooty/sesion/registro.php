<?php
session_start();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro</title>

    <?php
    error_reporting(E_ALL);
    ini_set("display_errors", 1);
    require "conexion.php";
    ?>

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

        .grupo-input input, .grupo-input select{
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

        .grupo-input input:focus{
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

<?php
if($_SERVER["REQUEST_METHOD"] == "POST"){

    $email = $_POST["email"];
    $usuario = $_POST["usuario"];
    $passw = $_POST["passw"];
    $rol = $_POST["rol"];

    $clave = password_hash($passw, PASSWORD_DEFAULT);

    $consulta = "INSERT INTO users (email, usuario, clave, rol)
                 VALUES ('$email','$usuario','$clave','$rol')";

    $_conexion->query($consulta);

    echo "<div class='mensaje-error'>Usuario registrado correctamente</div>";
}
?>

<div class="contenedor-principal">

    <div class="lado-izquierdo">
        <img src="../img/logo.png" class="logo">
        <p>
            Regístrate para crear una cuenta y acceder a todas las funcionalidades
            de la plataforma.
        </p>
    </div>

    <div class="lado-derecho">
        <div class="caja-formulario">

            <h2>Registro</h2>

            <form method="post">

                <div class="grupo-input">
                    <label>Email</label>
                    <input type="text" name="email">
                </div>

                <div class="grupo-input">
                    <label>Usuario</label>
                    <input type="text" name="usuario">
                </div>

                <div class="grupo-input">
                    <label>Contraseña</label>
                    <input type="password" name="passw">
                </div>

                <div class="grupo-input">
                    <label>Rol</label>
                    <select name="rol">
                        <option value="usuario">usuario</option>
                        <option value="admin">admin</option>
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