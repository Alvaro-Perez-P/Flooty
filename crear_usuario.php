<?php
session_start();
require 'sesion/conexion.php';

// 1. SEGURIDAD: Solo admins
if (!isset($_SESSION["rol"]) || $_SESSION["rol"] != "admin") {
    header("location: sesion/login.php");
    exit();
}

$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = mysqli_real_escape_string($_conexion, $_POST["email"]);
    $usuario = mysqli_real_escape_string($_conexion, $_POST["usuario"]);
    $clave = $_POST["clave"];
    $rol = $_POST["rol"];

    if (!empty($email) && !empty($usuario) && !empty($clave)) {
        $pass_hash = password_hash($clave, PASSWORD_BCRYPT);
        
        $sql = "INSERT INTO usuarios (email, usuario, clave, rol, activo) 
                VALUES ('$email', '$usuario', '$pass_hash', '$rol', 1)";

        if ($_conexion->query($sql)) {
            $mensaje = "<p style='color: #97B770; font-weight: bold;'>¡Usuario creado correctamente!</p>";
        } else {
            $mensaje = "<p style='color: red;'>Error: " . $_conexion->error . "</p>";
        }
    } else {
        $mensaje = "<p style='color: orange;'>Por favor, rellena todos los campos.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Crear Usuario — Admin</title>
    <link rel="stylesheet" href="css/admin.css">
    <style>
        /* Ajustes extra para el formulario dentro de tu diseño */
        .form-crear {
            max-width: 500px;
            margin: 40px auto;
            background: rgba(255,255,255,0.9);
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .form-crear input, .form-crear select {
            width: 100%;
            padding: 10px;
            margin: 10px 0 20px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .btn-submit {
            background-color: #d8b24c; /* Tu color de botones admin */
            color: white;
            border: none;
            padding: 12px;
            width: 100%;
            font-weight: bold;
            cursor: pointer;
            border-radius: 5px;
        }
        .btn-submit:hover { opacity: 0.9; }
        .volver { display: block; text-align: center; margin-top: 15px; color: #666; text-decoration: none; }
    </style>
</head>

<body>
    <?php include __DIR__.'/nav_basico.php'; ?>

    <main class="container">
        <h2>Gestión de Usuarios > Crear Nuevo</h2>

        <div class="form-crear">
            <?php echo $mensaje; ?>
            
            <form action="" method="post">
                <label>Email electrónico</label>
                <input type="email" name="email" placeholder="ejemplo@flooty.com" required>

                <label>Nombre de Usuario</label>
                <input type="text" name="usuario" placeholder="Nombre de acceso" required>

                <label>Contraseña</label>
                <input type="password" name="clave" placeholder="Contraseña provisional" required>

                <label>Rol asignado</label>
                <select name="rol">
                    <option value="usuario">Usuario Estándar</option>
                    <option value="admin">Administrador del Sistema</option>
                </select>

                <button type="submit" class="btn-submit">Registrar en la Base de Datos</button>
                <a href="panel_admin.php" class="volver">← Volver al Panel</a>
            </form>
        </div>
    </main>
</body>
</html>