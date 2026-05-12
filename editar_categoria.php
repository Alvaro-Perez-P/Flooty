<?php
session_start();

error_reporting(E_ALL);
ini_set("display_errors", 1);

require_once __DIR__ . "/sesion/conexion.php";

$error = "";

$id_categoria = isset($_GET["id"]) ? (int)$_GET["id"] : (int)($_POST["id"] ?? 0);

if ($id_categoria <= 0) {
    die("Categoría no válida.");
}

$consulta = "SELECT * FROM categorias WHERE id = $id_categoria LIMIT 1";
$resultado = $_conexion->query($consulta);

if (!$resultado || $resultado->num_rows == 0) {
    die("Categoría no encontrada.");
}

$categoria = $resultado->fetch_assoc();
$nombre = $categoria["nombre"] ?? "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = trim($_POST["nombre"] ?? "");

    if ($nombre == "") {
        $error = "El nombre de la categoría es obligatorio.";
    } else {
        $nombre_seguro = $_conexion->real_escape_string($nombre);

        $consulta_existe = "
            SELECT id 
            FROM categorias 
            WHERE nombre = '$nombre_seguro' 
            AND id != $id_categoria
            LIMIT 1
        ";
        $resultado_existe = $_conexion->query($consulta_existe);

        if ($resultado_existe && $resultado_existe->num_rows > 0) {
            $error = "Ya existe otra categoría con ese nombre.";
        } else {
            $update = "
                UPDATE categorias 
                SET nombre = '$nombre_seguro'
                WHERE id = $id_categoria
            ";

            if ($_conexion->query($update)) {
                header("location: panel_categorias.php");
                exit();
            } else {
                $error = "Error al actualizar la categoría: " . $_conexion->error;
            }
        }
    }
}
?>

<!doctype html>
<html lang="es">
<head>
<meta charset="utf-8">
<title>Editar categoría — Flooty</title>

<style>
body {
    background:#f5f1e6;
    font-family:Arial, Helvetica, sans-serif;
}

.contenedor {
    width:95%;
    max-width:600px;
    margin:40px auto;
    background:white;
    padding:30px;
    border-radius:18px;
    box-shadow:0 8px 25px rgba(0,0,0,0.12);
}

h1 {
    color:#97B770;
    margin-bottom:20px;
}

.grupo {
    margin-bottom:15px;
}

label {
    display:block;
    font-weight:bold;
    margin-bottom:6px;
}

input {
    width:100%;
    padding:12px;
    border:1px solid #ccc;
    border-radius:10px;
}

.btn {
    display:inline-block;
    background:#97B770;
    color:white;
    padding:12px 18px;
    border:none;
    border-radius:10px;
    text-decoration:none;
    font-weight:bold;
    cursor:pointer;
}

.btn-volver {
    background:#777;
}

.error {
    background:#f8d7da;
    color:#721c24;
    padding:12px;
    border-radius:10px;
    margin-bottom:15px;
}
</style>
</head>

<body>

<?php include __DIR__ . "/nav_basico.php"; ?>

<div class="contenedor">

    <h1>Editar categoría</h1>

    <?php if($error != ""): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST">

        <input type="hidden" name="id" value="<?= (int)$id_categoria ?>">

        <div class="grupo">
            <label>Nombre de la categoría</label>
            <input type="text" name="nombre" value="<?= htmlspecialchars($nombre) ?>" required>
        </div>

        <button type="submit" class="btn">Guardar cambios</button>
        <a href="panel_categorias.php" class="btn btn-volver">Volver</a>

    </form>

</div>

</body>
</html>
