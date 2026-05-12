<?php
session_start();

error_reporting(E_ALL);
ini_set("display_errors", 1);

if (!isset($_SESSION["usuario"])) {
    header("location: sesion/login.php");
    exit();
}

require "sesion/conexion.php";

$usuario = $_conexion->real_escape_string($_SESSION["usuario"]);

$consulta_usuario = "SELECT id FROM usuarios WHERE usuario = '$usuario' LIMIT 1";
$resultado_usuario = $_conexion->query($consulta_usuario);

if (!$resultado_usuario || $resultado_usuario->num_rows == 0) {
    die("Error: usuario no encontrado.");
}

$user = $resultado_usuario->fetch_assoc();
$id_usuario = (int)$user["id"];

$id_producto = isset($_GET["id"]) ? (int)$_GET["id"] : 0;

if ($id_producto <= 0) {
    die("Producto no válido.");
}

$consulta_producto = "
    SELECT *
    FROM productos
    WHERE id = $id_producto AND id_usuario = $id_usuario
    LIMIT 1
";

$resultado_producto = $_conexion->query($consulta_producto);

if (!$resultado_producto || $resultado_producto->num_rows == 0) {
    die("Producto no encontrado o no tienes permiso para editarlo.");
}

$producto = $resultado_producto->fetch_assoc();

$consulta_categorias = "SELECT id, nombre FROM categorias ORDER BY nombre ASC";
$resultado_categorias = $_conexion->query($consulta_categorias);

$mensaje_error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $titulo = $_conexion->real_escape_string(trim($_POST["titulo"] ?? ""));
    $descripcion = $_conexion->real_escape_string(trim($_POST["descripcion"] ?? ""));
    $id_categoria = (int)($_POST["id_categoria"] ?? 0);
    $precio_dia = (float)($_POST["precio_dia"] ?? 0);
    $fianza = (float)($_POST["fianza"] ?? 0);
    $ciudad = $_conexion->real_escape_string(trim($_POST["ciudad"] ?? ""));

    if ($titulo == "" || $descripcion == "" || $id_categoria <= 0 || $precio_dia <= 0 || $ciudad == "") {
        $mensaje_error = "Completa todos los campos obligatorios.";
    } else {
        $imagenes_actuales = $producto["imagenes"];

        if (isset($_FILES["imagenes"]) && !empty($_FILES["imagenes"]["name"][0])) {
            $imagenes = [];
            $carpeta = "imagenes/productos/";

            if (!is_dir($carpeta)) {
                mkdir($carpeta, 0777, true);
            }

            for ($i = 0; $i < count($_FILES["imagenes"]["name"]); $i++) {
                if ($_FILES["imagenes"]["error"][$i] === 0) {
                    $nombre_tmp = $_FILES["imagenes"]["tmp_name"][$i];
                    $nombre_original = basename($_FILES["imagenes"]["name"][$i]);
                    $extension = strtolower(pathinfo($nombre_original, PATHINFO_EXTENSION));

                    $extensiones_permitidas = ["jpg", "jpeg", "png", "webp"];

                    if (in_array($extension, $extensiones_permitidas)) {
                        $nombre_final = uniqid("producto_") . "." . $extension;
                        $ruta_final = $carpeta . $nombre_final;

                        if (move_uploaded_file($nombre_tmp, $ruta_final)) {
                            $imagenes[] = $ruta_final;
                        }
                    }
                }
            }

            if (count($imagenes) > 0) {
                $imagenes_actuales = json_encode($imagenes, JSON_UNESCAPED_UNICODE);
            }
        }

        $imagenes_actuales = $_conexion->real_escape_string($imagenes_actuales);

        $consulta_update = "
            UPDATE productos SET
                titulo = '$titulo',
                descripcion = '$descripcion',
                id_categoria = $id_categoria,
                precio_dia = $precio_dia,
                fianza = $fianza,
                ciudad = '$ciudad',
                imagenes = '$imagenes_actuales'
            WHERE id = $id_producto AND id_usuario = $id_usuario
        ";

        if ($_conexion->query($consulta_update)) {
            header("location: mis_productos.php");
            exit();
        } else {
            $mensaje_error = "Error al actualizar el producto: " . $_conexion->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar producto</title>

    <style>
        * {
            box-sizing: border-box;
            font-family: Arial, Helvetica, sans-serif;
        }

        body {
            background: #f5f1e6;
            margin: 0;
        }

        .contenedor {
            width: 95%;
            max-width: 700px;
            margin: 30px auto;
            background: white;
            padding: 25px;
            border-radius: 18px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        }

        h1 {
            text-align: center;
            color: #97B770;
            margin-bottom: 25px;
        }

        label {
            display: block;
            margin-top: 14px;
            font-weight: bold;
            color: #333;
        }

        input,
        textarea,
        select {
            width: 100%;
            padding: 11px;
            margin-top: 6px;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 15px;
        }

        textarea {
            min-height: 110px;
            resize: vertical;
        }

        .btn {
            margin-top: 20px;
            padding: 12px 18px;
            background: #97B770;
            color: white;
            border: none;
            border-radius: 9px;
            font-weight: bold;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
        }

        .btn-volver {
            background: #777;
        }

        .error {
            background: #ffd6d6;
            color: #900;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 15px;
        }

        .imagenes-actuales {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-top: 10px;
        }

        .imagenes-actuales img {
            width: 110px;
            height: 85px;
            object-fit: cover;
            border-radius: 10px;
            background: #ddd;
        }

        small {
            color: #666;
        }
    </style>
</head>
<body>

<?php include __DIR__ . "/nav_basico.php"; ?>

<div class="contenedor">
    <h1>Editar producto</h1>

    <?php if ($mensaje_error != ""): ?>
        <div class="error"><?= htmlspecialchars($mensaje_error) ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <label>Título</label>
        <input type="text" name="titulo" value="<?= htmlspecialchars($producto["titulo"]) ?>" required>

        <label>Descripción</label>
        <textarea name="descripcion" required><?= htmlspecialchars($producto["descripcion"]) ?></textarea>

        <label>Categoría</label>
        <select name="id_categoria" required>
            <option value="">Selecciona una categoría</option>

            <?php if ($resultado_categorias && $resultado_categorias->num_rows > 0): ?>
                <?php while ($categoria = $resultado_categorias->fetch_assoc()): ?>
                    <option value="<?= (int)$categoria["id"] ?>"
                        <?= ((int)$producto["id_categoria"] === (int)$categoria["id"]) ? "selected" : "" ?>>
                        <?= htmlspecialchars($categoria["nombre"]) ?>
                    </option>
                <?php endwhile; ?>
            <?php endif; ?>
        </select>

        <label>Precio por día</label>
        <input type="number" step="0.01" name="precio_dia" value="<?= htmlspecialchars($producto["precio_dia"]) ?>" required>

        <label>Fianza</label>
        <input type="number" step="0.01" name="fianza" value="<?= htmlspecialchars($producto["fianza"]) ?>">

        <label>Ciudad</label>
        <input type="text" name="ciudad" value="<?= htmlspecialchars($producto["ciudad"]) ?>" required>

        <label>Imágenes actuales</label>
        <div class="imagenes-actuales">
            <?php
                $imagenes = json_decode($producto["imagenes"] ?? "[]", true);

                if (is_array($imagenes) && count($imagenes) > 0):
                    foreach ($imagenes as $img):
            ?>
                        <img src="<?= htmlspecialchars($img) ?>" alt="Imagen producto">
            <?php
                    endforeach;
                else:
            ?>
                    <small>No hay imágenes cargadas.</small>
            <?php endif; ?>
        </div>

        <label>Cambiar imágenes</label>
        <input type="file" name="imagenes[]" multiple accept="image/*">
        <small>Si no seleccionas imágenes nuevas, se conservan las actuales.</small>

        <br>

        <button type="submit" class="btn">Guardar cambios</button>
        <a href="mis_productos.php" class="btn btn-volver">Cancelar</a>
    </form>
</div>

</body>
</html>
