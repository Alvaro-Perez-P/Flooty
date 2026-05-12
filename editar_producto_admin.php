<?php
session_start();

error_reporting(E_ALL);
ini_set("display_errors", 1);

require_once __DIR__ . "/sesion/conexion.php";

$id_producto = isset($_GET["id"]) ? (int)$_GET["id"] : (int)($_POST["id"] ?? 0);

if ($id_producto <= 0) {
    die("Producto no válido.");
}

$mensaje_error = "";

/* Categorías */
$categorias = [];
$resultado_categorias = $_conexion->query("SELECT id, nombre FROM categorias ORDER BY nombre ASC");

if ($resultado_categorias && $resultado_categorias->num_rows > 0) {
    while ($cat = $resultado_categorias->fetch_assoc()) {
        $categorias[] = $cat;
    }
}

/* Cargar producto */
$consulta_producto = "SELECT * FROM productos WHERE id = $id_producto LIMIT 1";
$resultado_producto = $_conexion->query($consulta_producto);

if (!$resultado_producto || $resultado_producto->num_rows == 0) {
    die("Producto no encontrado.");
}

$producto = $resultado_producto->fetch_assoc();

$titulo = $producto["titulo"] ?? "";
$descripcion = $producto["descripcion"] ?? "";
$id_categoria = $producto["id_categoria"] ?? "";
$precio_dia = $producto["precio_dia"] ?? "";
$fianza = $producto["fianza"] ?? "";
$ciudad = $producto["ciudad"] ?? "";
$estado = $producto["estado"] ?? "activo";
$imagenes_actuales = json_decode($producto["imagenes"] ?? "[]", true);

if (!is_array($imagenes_actuales)) {
    $imagenes_actuales = [];
}

/* Guardar */
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $titulo = trim($_POST["titulo"] ?? "");
    $descripcion = trim($_POST["descripcion"] ?? "");
    $id_categoria = (int)($_POST["id_categoria"] ?? 0);
    $precio_dia = trim($_POST["precio_dia"] ?? "");
    $fianza = trim($_POST["fianza"] ?? "");
    $ciudad = trim($_POST["ciudad"] ?? "");
    $estado = $_POST["estado"] ?? "activo";

    if ($titulo == "" || $descripcion == "" || $id_categoria <= 0 || $precio_dia == "" || $fianza == "" || $ciudad == "") {
        $mensaje_error = "Completa todos los campos obligatorios.";
    } else {

        $imagenes_para_guardar = $imagenes_actuales;

        if (isset($_FILES["imagenes"]) && !empty(array_filter($_FILES["imagenes"]["name"]))) {

            $nuevas_imagenes = [];
            $carpeta = __DIR__ . "/imagenes/productos/";

            if (!is_dir($carpeta)) {
                mkdir($carpeta, 0777, true);
            }

            $permitidos = ["image/jpeg", "image/png", "image/webp", "image/jpg"];

            for ($i = 0; $i < count($_FILES["imagenes"]["name"]); $i++) {

                if ($_FILES["imagenes"]["name"][$i] == "") {
                    continue;
                }

                $tmp = $_FILES["imagenes"]["tmp_name"][$i];
                $tipo = mime_content_type($tmp);

                if (!in_array($tipo, $permitidos)) {
                    $mensaje_error = "Solo se permiten imágenes JPG, PNG o WEBP.";
                    break;
                }

                $extension = strtolower(pathinfo($_FILES["imagenes"]["name"][$i], PATHINFO_EXTENSION));
                $nombre = time() . "_" . $i . "_" . uniqid() . "." . $extension;
                $destino = $carpeta . $nombre;

                if (move_uploaded_file($tmp, $destino)) {
                    $nuevas_imagenes[] = "imagenes/productos/" . $nombre;
                }
            }

            if ($mensaje_error == "" && count($nuevas_imagenes) > 0) {
                $imagenes_para_guardar = $nuevas_imagenes;
            }
        }

        if ($mensaje_error == "") {
            $titulo_seguro = $_conexion->real_escape_string($titulo);
            $descripcion_segura = $_conexion->real_escape_string($descripcion);
            $ciudad_segura = $_conexion->real_escape_string($ciudad);
            $estado_seguro = $_conexion->real_escape_string($estado);
            $precio_dia = (float)$precio_dia;
            $fianza = (float)$fianza;
            $imagenes_json = $_conexion->real_escape_string(json_encode($imagenes_para_guardar, JSON_UNESCAPED_UNICODE));

            $update = "
                UPDATE productos SET
                    titulo = '$titulo_seguro',
                    descripcion = '$descripcion_segura',
                    id_categoria = $id_categoria,
                    precio_dia = $precio_dia,
                    fianza = $fianza,
                    ciudad = '$ciudad_segura',
                    estado = '$estado_seguro',
                    imagenes = '$imagenes_json'
                WHERE id = $id_producto
            ";

            if ($_conexion->query($update)) {
                header("location: panel_productos.php");
                exit();
            } else {
                $mensaje_error = "Error al actualizar: " . $_conexion->error;
            }
        }
    }
}
?>

<!doctype html>
<html lang="es">
<head>
<meta charset="utf-8">
<title>Editar producto — Flooty</title>

<style>
body {
    background:#f5f1e6;
    font-family:Arial, Helvetica, sans-serif;
}

.contenedor {
    width:95%;
    max-width:800px;
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

input, textarea, select {
    width:100%;
    padding:12px;
    border:1px solid #ccc;
    border-radius:10px;
}

textarea {
    min-height:120px;
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

.imagenes {
    display:flex;
    gap:10px;
    flex-wrap:wrap;
    margin-bottom:15px;
}

.imagenes img {
    width:100px;
    height:80px;
    object-fit:cover;
    border-radius:10px;
}
</style>
</head>

<body>

<?php include __DIR__ . "/nav_basico.php"; ?>

<div class="contenedor">

    <h1>Editar producto</h1>

    <?php if($mensaje_error != ""): ?>
        <div class="error"><?= htmlspecialchars($mensaje_error) ?></div>
    <?php endif; ?>

    <?php if(count($imagenes_actuales) > 0): ?>
        <div class="imagenes">
            <?php foreach($imagenes_actuales as $img): ?>
                <img src="<?= htmlspecialchars($img) ?>" onerror="this.style.display='none';">
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">

        <input type="hidden" name="id" value="<?= (int)$id_producto ?>">

        <div class="grupo">
            <label>Título</label>
            <input type="text" name="titulo" value="<?= htmlspecialchars($titulo) ?>" required>
        </div>

        <div class="grupo">
            <label>Descripción</label>
            <textarea name="descripcion" required><?= htmlspecialchars($descripcion) ?></textarea>
        </div>

        <div class="grupo">
            <label>Categoría</label>
            <select name="id_categoria" required>
                <option value="">Selecciona categoría</option>

                <?php foreach($categorias as $cat): ?>
                    <option value="<?= (int)$cat["id"] ?>" <?= (int)$id_categoria == (int)$cat["id"] ? "selected" : "" ?>>
                        <?= htmlspecialchars($cat["nombre"]) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="grupo">
            <label>Precio por día</label>
            <input type="number" step="0.01" name="precio_dia" value="<?= htmlspecialchars($precio_dia) ?>" required>
        </div>

        <div class="grupo">
            <label>Fianza</label>
            <input type="number" step="0.01" name="fianza" value="<?= htmlspecialchars($fianza) ?>" required>
        </div>

        <div class="grupo">
            <label>Ciudad</label>
            <input type="text" name="ciudad" value="<?= htmlspecialchars($ciudad) ?>" required>
        </div>

        <div class="grupo">
            <label>Estado</label>
            <select name="estado">
                <option value="activo" <?= $estado == "activo" ? "selected" : "" ?>>Activo</option>
                <option value="pausado" <?= $estado == "pausado" ? "selected" : "" ?>>Pausado</option>
            </select>
        </div>

        <div class="grupo">
            <label>Cambiar imágenes</label>
            <input type="file" name="imagenes[]" multiple accept="image/*">
            <small>Si no subes imágenes nuevas, se mantienen las actuales.</small>
        </div>

        <button type="submit" class="btn">Guardar cambios</button>
        <a href="panel_productos.php" class="btn btn-volver">Volver</a>

    </form>

</div>

</body>
</html>
