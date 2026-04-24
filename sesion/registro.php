<?php
session_start();
error_reporting(E_ALL);
ini_set("display_errors", 1);
require "conexion.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $tmp_email = trim($_POST["email"]);
    $tmp_usuario = trim($_POST["usuario"]);
    $tmp_contrasena = trim($_POST["passw"]);
    $tmp_contrasena_conf = trim($_POST["passwConf"]);

    $errores = false;

   //validacion de email
    if ($tmp_email == "") {
        $err_email = "Introduce un email";
        $errores = true;
    } elseif (!filter_var($tmp_email, FILTER_VALIDATE_EMAIL)) {
        $err_email = "El email no tiene un formato válido";
        $errores = true;
    } else {
        $email = $tmp_email;
    }

  //validacion de usuario
    if ($tmp_usuario == "") {
        $err_usuario = "Introduce un usuario";
        $errores = true;
    } else {
        $usuario = $tmp_usuario;
    }

   //validar contraseña
    if ($tmp_contrasena == "") {
        $err_contrasena = "Introduce una contraseña";
        $errores = true;
    } elseif (strlen($tmp_contrasena) < 6) {
        $err_contrasena = "La contraseña debe tener al menos 6 caracteres";
        $errores = true;
    } else {
        $contrasena = $tmp_contrasena;
    }

   
    // validar confirmar contraseña
    
    if ($tmp_contrasena_conf == "") {
        $err_contrasena_conf = "Confirma la contraseña";
        $errores = true;
    } elseif ($tmp_contrasena !== $tmp_contrasena_conf) {
        $err_contrasena_conf = "Las contraseñas no coinciden";
        $errores = true;
    }

   
    // Comprobar se existe 
    
    if (!$errores) {

        $consulta = "SELECT * FROM usuarios WHERE email = ? OR usuario = ?";
        $stmt = $_conexion->prepare($consulta);
        $stmt->bind_param("ss", $email, $usuario);
        $stmt->execute();
        $resultado = $stmt->get_result();

        if ($resultado->num_rows > 0) {
            $usuario_existente = $resultado->fetch_assoc();

            if ($usuario_existente["email"] == $email) {
                $err_email = "Ese email ya está registrado";
            }

            if ($usuario_existente["usuario"] == $usuario) {
                $err_usuario = "Ese usuario ya existe";
            }
        } else {
            // Encriptar contraseña
            $clave_hash = password_hash($contrasena, PASSWORD_DEFAULT);

            // Rol por defecto
            $rol = "usuario";

            // Insertar usuario
            $insertar = "INSERT INTO usuarios (email, usuario, clave, rol) VALUES (?, ?, ?, ?)";
            $stmt_insert = $_conexion->prepare($insertar);
            $stmt_insert->bind_param("ssss", $email, $usuario, $clave_hash, $rol);

            if ($stmt_insert->execute()) {
                echo "
       <script>
        alert('Usuario registrado correctamente');
        window.location.href = 'login.php';
       </script>";
            } else {
                echo "
       <script>
        alert('Error al registrar el usuario');
       </script>";
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
    <title>Registro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #F5F5DC;
        }
    </style>
</head>

<body>
    <div class="container d-flex justify-content-center align-items-center vh-100">
        <div class="bg-success p-4 rounded shadow w-100" style="max-width: 400px;">
            <form action="" method="post">
                <h2 class="text-center mb-4 text-white">Regístrate en FLOOTY</h2>

                <div class="mb-3">
                    <label class="form-label text-white">Email</label>
                    <input type="email" name="email" class="form-control" placeholder="Introduce tu email"
                        value="<?php echo isset($tmp_email) ? htmlspecialchars($tmp_email) : ''; ?>">
                    <?php
                    if (isset($err_email)) {
                        echo "<div class='alert alert-danger mt-2'>$err_email</div>";
                    }
                    ?>
                </div>

                <div class="mb-3">
                    <label class="form-label text-white">Nombre de usuario</label>
                    <input type="text" name="usuario" class="form-control" placeholder="Tu usuario"
                        value="<?php echo isset($tmp_usuario) ? htmlspecialchars($tmp_usuario) : ''; ?>">
                    <?php
                    if (isset($err_usuario)) {
                        echo "<div class='alert alert-danger mt-2'>$err_usuario</div>";
                    }
                    ?>
                </div>

                <div class="mb-3">
                    <label class="form-label text-white">Contraseña</label>
                    <input type="password" name="passw" class="form-control" placeholder="********">
                    <?php
                    if (isset($err_contrasena)) {
                        echo "<div class='alert alert-danger mt-2'>$err_contrasena</div>";
                    }
                    ?>
                </div>

                <div class="mb-3">
                    <label class="form-label text-white">Confirmar contraseña</label>
                    <input type="password" name="passwConf" class="form-control" placeholder="********">
                    <?php
                    if (isset($err_contrasena_conf)) {
                        echo "<div class='alert alert-danger mt-2'>$err_contrasena_conf</div>";
                    }
                    ?>
                </div>

                <button type="submit" class="btn w-100" style="background-color: #8B4513; color: white;">
                    Registrarte
                </button>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>