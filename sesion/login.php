<?php
session_start();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>

    <?php
    error_reporting(E_ALL);
    ini_set("display_errors", 1);
    require "conexion.php";
    ?>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body style="background-color: #f5f5dc;">

<?php
if($_SERVER["REQUEST_METHOD"] == "POST"){

    $tmp_email = $_POST["email"];
    $tmp_contrasena = $_POST["passw"];

    $errores = false;

  
    // VALIDACIÓN DEL EMAIL

    $tmp_email = trim($tmp_email);

    if($tmp_email == ""){
        $err_email = "Introduce un email";
        $errores = true;
    }elseif(!filter_var($tmp_email, FILTER_VALIDATE_EMAIL)){
        $err_email = "El email debe tener un formato válido y contener @";
        $errores = true;
    }else{
        $email = $tmp_email;
    }

 
    // VALIDACIÓN DE CONTRASEÑA

    $tmp_contrasena = trim($tmp_contrasena);

    if($tmp_contrasena == ""){
        $err_contrasena = "Introduce una contraseña";
        $errores = true;
    }else{
        $contrasena = $tmp_contrasena;
    }

    // CONSULTA A LA BASE DE DATOS
  
    if(!$errores){

        $consulta = "SELECT * FROM usuarios WHERE email = '$email'";
        $resultado = $_conexion->query($consulta);

        if($resultado->num_rows === 0){
            echo "<div class='alert alert-danger text-center'>El email no existe en la base de datos</div>";
        }else{
            $user_info = $resultado->fetch_assoc();

            $acceso_concedido = password_verify($contrasena, $user_info["clave"]);

            if(!$acceso_concedido){
                echo "<div class='alert alert-danger text-center'>Contraseña incorrecta</div>";
            }else{

                // Validamos que solo entre el rol usuario o admin
                if($user_info["rol"] != "usuario" && $user_info["rol"] != "admin"){
                    echo "<div class='alert alert-danger text-center'>Acceso denegado. Solo pueden entrar usuarios o administradores</div>";
                }else{
                    $_SESSION["usuario"] = $user_info["usuario"];
                    $_SESSION["rol"] = $user_info["rol"];

                    header("location: ../index.php");
                    exit();
                }
            }
        }
    }
}
?>

<div class="container d-flex justify-content-center align-items-center vh-100">
    <div class="p-4 rounded shadow w-100" style="max-width: 400px; background-color: #97B770;">

        <h2 class="text-center mb-4 text-white">Inicia sesión en FLOOTY</h2>

        <form action="" method="post">

            <div class="mb-3">
                <label class="form-label text-white">Email</label>
                <input type="text" name="email" class="form-control" placeholder="Introduce tu email">
                <?php
                    if(isset($err_email)){
                        echo "<div class='alert alert-danger mt-2'>$err_email</div>";
                    }
                ?>
            </div>

            <div class="mb-3">
                <label class="form-label text-white">Contraseña</label>
                <input type="password" name="passw" class="form-control" placeholder="Introduce tu contraseña">
                <?php
                    if(isset($err_contrasena)){
                        echo "<div class='alert alert-danger mt-2'>$err_contrasena</div>";
                    }
                ?>
            </div>

            <div class="mb-3">
                <input type="submit" value="Iniciar sesión" class="btn w-100" style="background-color: rgb(96, 131, 52); color: white;">
            </div>
        </form>

        <h5 class="text-center mt-4 mb-3 text-white">Si no tienes cuenta, regístrate aquí</h5>
        <a href="registro.php" class="btn w-100" style="background-color: rgb(96, 131, 52); color: white;">Registrarse</a>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>