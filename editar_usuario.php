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
    header("location: panel_usuarios.php");
    exit();
}

$id = $_GET["id"];

$consulta = "SELECT * FROM usuarios WHERE id = '$id'";
$resultado = $_conexion->query($consulta);

if ($resultado->num_rows === 0) {
    header("location: panel_usuarios.php");
    exit();
}

$usuario_editar = $resultado->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $tmp_email = $_POST["email"];
    $tmp_usuario = $_POST["usuario"];
    $tmp_clave = $_POST["clave"];
    $tmp_rol = $_POST["rol"];

    $errores = false;

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

    $tmp_usuario = trim($tmp_usuario);

    if ($tmp_usuario == "") {
        $err_usuario = "Introduce un usuario";
        $errores = true;
    } else {
        $usuario = $tmp_usuario;
    }

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
            header("location: panel_usuarios.php");
            exit();
        } else {
            $error_general = "Error al editar el usuario";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Editar usuario — Footy</title>

<style>

:root {
    --verde-hielo: #F7FEEF;
    --verde-suave: #CBDDB5;
    --verde-natural: #A8CA7E;
    --verde-organico: #97B770;
    --verde-profundo: rgb(96, 131, 52);
}

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
}

body{
    background:var(--verde-hielo);
    font-family:Arial, Helvetica, sans-serif;
}

.container{
    width:95%;
    max-width:520px;
    margin:50px auto;
}

.card{
    background:white;
    padding:30px;
    border-radius:16px;
    box-shadow:0 5px 15px rgba(0,0,0,0.08);
}

h1{
    text-align:center;
    color:var(--verde-natural);
    margin-bottom:25px;
    font-size:32px;
}

.form-group{
    margin-bottom:18px;
}

label{
    display:block;
    margin-bottom:8px;
    color:var(--verde-profundo);
    font-weight:bold;
}

input,
select{
    width:100%;
    padding:12px;
    border:1px solid var(--verde-suave);
    border-radius:8px;
    font-size:15px;
    outline:none;
}

input:focus,
select:focus{
    border-color:var(--verde-natural);
}

.btn{
    width:100%;
    display:block;
    padding:12px 16px;
    border-radius:8px;
    text-decoration:none;
    background:var(--verde-natural);
    color:white;
    font-size:15px;
    border:none;
    cursor:pointer;
    text-align:center;
    transition:0.3s;
}

.btn:hover{
    background:var(--verde-profundo);
}

.btn-secondary{
    background:var(--verde-organico);
    margin-top:12px;
}

.btn-secondary:hover{
    background:var(--verde-profundo);
}

.error{
    background:#f8d7da;
    color:#842029;
    padding:10px;
    border-radius:8px;
    margin-top:8px;
    font-size:14px;
}

</style>

</head>

<body>

<?php include __DIR__ . "/nav_basico.php"; ?>

<main class="container">

    <div class="card">

        <h1>Editar usuario</h1>

        <?php if(isset($error_general)): ?>
            <div class="error">
                <?php echo $error_general; ?>
            </div>
        <?php endif; ?>

        <form action="" method="post">

            <div class="form-group">
                <label>Email</label>
                <input 
                    type="text" 
                    name="email" 
                    value="<?php echo htmlspecialchars($usuario_editar["email"]); ?>"
                >

                <?php if(isset($err_email)): ?>
                    <div class="error">
                        <?php echo $err_email; ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label>Usuario</label>
                <input 
                    type="text" 
                    name="usuario" 
                    value="<?php echo htmlspecialchars($usuario_editar["usuario"]); ?>"
                >

                <?php if(isset($err_usuario)): ?>
                    <div class="error">
                        <?php echo $err_usuario; ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label>Nueva contraseña</label>
                <input 
                    type="password" 
                    name="clave" 
                    placeholder="Solo rellena si la quieres cambiar"
                >
            </div>

            <div class="form-group">
                <label>Rol</label>
                <select name="rol">
                    <option value="">-- Selecciona un rol --</option>
                    <option value="usuario" <?php if($usuario_editar["rol"] == "usuario") echo "selected"; ?>>
                        usuario
                    </option>
                    <option value="admin" <?php if($usuario_editar["rol"] == "admin") echo "selected"; ?>>
                        admin
                    </option>
                </select>

                <?php if(isset($err_rol)): ?>
                    <div class="error">
                        <?php echo $err_rol; ?>
                    </div>
                <?php endif; ?>
            </div>

            <button type="submit" class="btn">
                Guardar cambios
            </button>

        </form>

        <a href="panel_usuarios.php" class="btn btn-secondary">
            Volver
        </a>

    </div>

</main>

</body>
</html>