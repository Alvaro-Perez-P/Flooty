<?php
session_start();

error_reporting(E_ALL);
ini_set("display_errors", 1);

require "sesion/conexion.php";

if (!isset($_SESSION["usuario"])) {
    header("location: sesion/login.php");
    exit();
}

$mensaje_exito = "";
$mensaje_error = "";

$err_titulo = "";
$err_descripcion = "";
$err_categoria = "";
$err_precio = "";
$err_fianza = "";
$err_ciudad = "";
$err_imagen = "";

$titulo = "";
$descripcion = "";
$id_categoria = "";
$precio = "";
$fianza = "";
$ciudad = "";

/* SACAR ID DEL USUARIO LOGUEADO */
$usuario_sesion = $_SESSION["usuario"];
$usuario_sesion_seguro = $_conexion->real_escape_string($usuario_sesion);

$consulta_usuario = "SELECT id, usuario FROM usuarios WHERE usuario = '$usuario_sesion_seguro' LIMIT 1";
$resultado_usuario = $_conexion->query($consulta_usuario);

if (!$resultado_usuario || $resultado_usuario->num_rows == 0) {
    die("Error: no se encontró el usuario en la base de datos.");
}

$datos_usuario = $resultado_usuario->fetch_assoc();
$id_usuario = (int)$datos_usuario["id"];

/* CARGAR CATEGORÍAS */
$categorias = [];
$consulta_categorias = "SELECT id, nombre FROM categorias ORDER BY nombre ASC";
$resultado_categorias = $_conexion->query($consulta_categorias);

if ($resultado_categorias && $resultado_categorias->num_rows > 0) {
    while ($fila = $resultado_categorias->fetch_assoc()) {
        $categorias[] = $fila;
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $titulo = trim($_POST["titulo"] ?? "");
    $descripcion = trim($_POST["descripcion"] ?? "");
    $id_categoria = trim($_POST["id_categoria"] ?? "");
    $precio = trim($_POST["precio"] ?? "");
    $fianza = trim($_POST["fianza"] ?? "");
    $ciudad = trim($_POST["ciudad"] ?? "");

    $errores = false;

    if ($titulo == "") {
        $err_titulo = "El título es obligatorio";
        $errores = true;
    }

    if ($descripcion == "") {
        $err_descripcion = "La descripción es obligatoria";
        $errores = true;
    }

    if ($id_categoria == "") {
        $err_categoria = "La categoría es obligatoria";
        $errores = true;
    }

    if ($precio == "") {
        $err_precio = "El precio por día es obligatorio";
        $errores = true;
    } elseif (!is_numeric($precio) || $precio <= 0) {
        $err_precio = "Introduce un precio válido";
        $errores = true;
    }

    if ($fianza == "") {
        $err_fianza = "La fianza es obligatoria";
        $errores = true;
    } elseif (!is_numeric($fianza) || $fianza < 0) {
        $err_fianza = "Introduce una fianza válida";
        $errores = true;
    }

    if ($ciudad == "") {
        $err_ciudad = "La ciudad es obligatoria";
        $errores = true;
    }

    /* VALIDAR IMÁGENES */
    if (
        !isset($_FILES["imagenes"]) ||
        !isset($_FILES["imagenes"]["name"]) ||
        empty(array_filter($_FILES["imagenes"]["name"]))
    ) {
        $err_imagen = "Debes subir al menos una imagen";
        $errores = true;
    } else {
        $total_imagenes = count(array_filter($_FILES["imagenes"]["name"]));

        if ($total_imagenes > 5) {
            $err_imagen = "Solo puedes subir hasta 5 imágenes";
            $errores = true;
        }
    }

    $rutas_imagenes = [];

if (!$errores) {
    $carpeta_destino = __DIR__ . "/imagenes/productos/";

    if (!is_dir($carpeta_destino)) {
        if (!mkdir($carpeta_destino, 0777, true)) {
            $err_imagen = "No se pudo crear la carpeta de imágenes";
            $errores = true;
        }
    }

    if (!$errores && !is_writable($carpeta_destino)) {
        $err_imagen = "La carpeta de imágenes no tiene permisos de escritura";
        $errores = true;
    }

    $permitidos = ["image/jpeg", "image/png", "image/webp", "image/jpg"];

    if (!$errores) {
        for ($i = 0; $i < count($_FILES["imagenes"]["name"]); $i++) {

            if ($_FILES["imagenes"]["name"][$i] == "") {
                continue;
            }

            if ($_FILES["imagenes"]["error"][$i] !== 0) {
                $err_imagen = "Error al subir una de las imágenes. Código: " . $_FILES["imagenes"]["error"][$i];
                $errores = true;
                break;
            }

            $tmp_imagen = $_FILES["imagenes"]["tmp_name"][$i];
            $nombre_original = $_FILES["imagenes"]["name"][$i];

            if (!is_uploaded_file($tmp_imagen)) {
                $err_imagen = "Uno de los archivos subidos no es válido";
                $errores = true;
                break;
            }

            $tipo_imagen = mime_content_type($tmp_imagen);

            if (!in_array($tipo_imagen, $permitidos)) {
                $err_imagen = "Solo se permiten imágenes JPG, JPEG, PNG o WEBP";
                $errores = true;
                break;
            }

            $extension = strtolower(pathinfo($nombre_original, PATHINFO_EXTENSION));
            $nombre_unico = time() . "_" . $i . "_" . uniqid() . "." . $extension;
            $ruta_destino = $carpeta_destino . $nombre_unico;

            if (!move_uploaded_file($tmp_imagen, $ruta_destino)) {
                $err_imagen = "No se pudo mover la imagen a la carpeta destino";
                $errores = true;
                break;
            }

            $rutas_imagenes[] = "imagenes/productos/" . $nombre_unico;
        }
    }
}

    if (!$errores) {
        $titulo_seguro = $_conexion->real_escape_string($titulo);
        $descripcion_segura = $_conexion->real_escape_string($descripcion);
        $ciudad_segura = $_conexion->real_escape_string($ciudad);
        $id_categoria = (int)$id_categoria;
        $precio = (float)$precio;
        $fianza = (float)$fianza;

        $imagenes_json = $_conexion->real_escape_string(json_encode($rutas_imagenes, JSON_UNESCAPED_UNICODE));

        $consulta = "INSERT INTO productos 
            (titulo, descripcion, id_categoria, precio_dia, fianza, ciudad, imagenes, fecha_creacion, id_usuario)
            VALUES 
            ('$titulo_seguro', '$descripcion_segura', $id_categoria, $precio, $fianza, '$ciudad_segura', '$imagenes_json', NOW(), $id_usuario)";

        if ($_conexion->query($consulta)) {
            $mensaje_exito = "Producto creado con éxito";

            $titulo = "";
            $descripcion = "";
            $id_categoria = "";
            $precio = "";
            $fianza = "";
            $ciudad = "";
        } else {
            $mensaje_error = "Error al crear el producto: " . $_conexion->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear producto</title>

    <style>
    *{
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: Arial, Helvetica, sans-serif;
    }

    body{
        min-height: 100vh;
        background-color: #f5f1e6;
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 10px;
    }

    .contenedor-principal{
        width: 100%;
        max-width: 620px;
    }

    .caja-formulario{
        width: 100%;
        background: rgba(245, 241, 230, 0.88);
        border: 1px solid rgba(0,0,0,0.10);
        border-radius: 20px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        backdrop-filter: blur(4px);
        padding: 22px 24px;
    }

    .caja-formulario h2{
        text-align: center;
        margin-bottom: 14px;
        color: #97B770;
        font-size: 28px;
        line-height: 1.1;
    }

    .grupo-input{
        margin-bottom: 10px;
    }

    .grupo-input label{
        display: block;
        margin-bottom: 5px;
        color: #97B770;
        font-weight: bold;
        font-size: 14px;
    }

    .grupo-input input,
    .grupo-input textarea,
    .grupo-input select{
        width: 100%;
        padding: 9px 12px;
        border: 1px solid rgba(0,0,0,0.15);
        border-radius: 8px;
        font-size: 14px;
        outline: none;
        background: rgba(255,255,255,0.7);
        color: #333;
    }

    .grupo-input textarea{
        resize: vertical;
        min-height: 72px;
        max-height: 90px;
    }

    .grupo-input input:focus,
    .grupo-input textarea:focus,
    .grupo-input select:focus{
        border: 1px solid #97B770;
    }

    .texto-ayuda{
        color: #666;
        font-size: 12px;
        margin-bottom: 6px;
    }

    .boton{
        width: 100%;
        padding: 10px 12px;
        border: none;
        border-radius: 8px;
        font-size: 15px;
        font-weight: bold;
        cursor: pointer;
        transition: 0.3s;
        text-decoration: none;
        display: block;
        text-align: center;
    }

    .boton-guardar{
        background-color: #97B770;
        color: white;
        margin-top: 6px;
    }

    .boton-guardar:hover{
        background-color: #7fa45a;
    }

    .boton-volver{
        background-color: #8c8c8c;
        color: white;
        margin-top: 8px;
    }

    .boton-volver:hover{
        background-color: #737373;
    }

    .separador{
        border: none;
        border-top: 1px solid rgba(0,0,0,0.1);
        margin: 10px 0;
    }

    .mensaje-error{
        background-color: rgba(220, 53, 69, 0.92);
        color: white;
        padding: 8px 10px;
        border-radius: 8px;
        margin-top: 6px;
        font-size: 13px;
    }

    .mensaje-exito{
        background-color: rgba(40, 167, 69, 0.92);
        color: white;
        padding: 10px 12px;
        border-radius: 8px;
        margin-bottom: 12px;
        font-size: 13px;
        text-align: center;
    }

    @media (max-height: 900px){
        body{
            padding: 8px;
        }

        .contenedor-principal{
            max-width: 600px;
        }

        .caja-formulario{
            padding: 18px 20px;
        }

        .caja-formulario h2{
            font-size: 24px;
            margin-bottom: 10px;
        }

        .grupo-input{
            margin-bottom: 8px;
        }

        .grupo-input label{
            font-size: 13px;
            margin-bottom: 4px;
        }

        .grupo-input input,
        .grupo-input textarea,
        .grupo-input select{
            padding: 8px 10px;
            font-size: 13px;
        }

        .grupo-input textarea{
            min-height: 60px;
            max-height: 75px;
        }

        .boton{
            padding: 9px 10px;
            font-size: 14px;
        }

        .separador{
            margin: 8px 0;
        }
    }

    @media (max-height: 760px){
        .contenedor-principal{
            max-width: 560px;
        }

        .caja-formulario{
            padding: 16px 18px;
        }

        .caja-formulario h2{
            font-size: 22px;
        }

        .grupo-input{
            margin-bottom: 7px;
        }

        .grupo-input textarea{
            min-height: 52px;
            max-height: 65px;
        }

        .texto-ayuda{
            font-size: 11px;
        }

        .mensaje-error,
        .mensaje-exito{
            font-size: 12px;
            padding: 7px 9px;
        }
    }

    @media(max-width: 600px){
        .contenedor-principal{
            max-width: 100%;
        }

        .caja-formulario{
            padding: 18px 16px;
        }

        .caja-formulario h2{
            font-size: 24px;
        }
    }
</style>
</head>
<body>

    <div class="contenedor-principal">
        <div class="caja-formulario">

            <form method="post" action="" enctype="multipart/form-data">

                <h2>Crear producto</h2>

                <?php if ($mensaje_exito != ""): ?>
                    <div class="mensaje-exito"><?= $mensaje_exito ?></div>
                <?php endif; ?>

                <?php if ($mensaje_error != ""): ?>
                    <div class="mensaje-error"><?= $mensaje_error ?></div>
                <?php endif; ?>

                <div class="grupo-input">
                    <label for="titulo">Título</label>
                    <input type="text" name="titulo" id="titulo" placeholder="Título del producto" value="<?= htmlspecialchars($titulo) ?>">
                    <?php if ($err_titulo != ""): ?>
                        <div class="mensaje-error"><?= $err_titulo ?></div>
                    <?php endif; ?>
                </div>

                <div class="grupo-input">
                    <label for="descripcion">Descripción</label>
                    <textarea name="descripcion" id="descripcion" placeholder="Descripción del producto"><?= htmlspecialchars($descripcion) ?></textarea>
                    <?php if ($err_descripcion != ""): ?>
                        <div class="mensaje-error"><?= $err_descripcion ?></div>
                    <?php endif; ?>
                </div>

                <div class="grupo-input">
                    <label for="id_categoria">Categoría</label>
                    <select name="id_categoria" id="id_categoria">
                        <option value="">--- ELIGE LA CATEGORÍA ---</option>
                        <?php foreach ($categorias as $cat): ?>
                            <option value="<?= $cat["id"] ?>" <?= $id_categoria == $cat["id"] ? "selected" : "" ?>>
                                <?= htmlspecialchars($cat["nombre"]) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if ($err_categoria != ""): ?>
                        <div class="mensaje-error"><?= $err_categoria ?></div>
                    <?php endif; ?>
                </div>

                <div class="grupo-input">
                    <label for="precio">Precio por día</label>
                    <input type="number" step="0.01" name="precio" id="precio" placeholder="Precio por día" value="<?= htmlspecialchars($precio) ?>">
                    <?php if ($err_precio != ""): ?>
                        <div class="mensaje-error"><?= $err_precio ?></div>
                    <?php endif; ?>
                </div>

                <div class="grupo-input">
                    <label for="fianza">Fianza</label>
                    <input type="number" step="0.01" name="fianza" id="fianza" placeholder="Fianza" value="<?= htmlspecialchars($fianza) ?>">
                    <?php if ($err_fianza != ""): ?>
                        <div class="mensaje-error"><?= $err_fianza ?></div>
                    <?php endif; ?>
                </div>

                <div class="grupo-input">
                    <label for="ciudad">Ciudad</label>
                    <input type="text" name="ciudad" id="ciudad" placeholder="Ciudad" value="<?= htmlspecialchars($ciudad) ?>">
                    <?php if ($err_ciudad != ""): ?>
                        <div class="mensaje-error"><?= $err_ciudad ?></div>
                    <?php endif; ?>
                </div>

                <div class="grupo-input">
                    <label for="imagenes">Imágenes</label>
                    <p class="texto-ayuda">Selecciona entre 1 y 5 imágenes</p>
                    <input type="file" name="imagenes[]" id="imagenes" accept="image/*" multiple>
                    <?php if ($err_imagen != ""): ?>
                        <div class="mensaje-error"><?= $err_imagen ?></div>
                    <?php endif; ?>
                </div>

                <button type="submit" name="enviar" class="boton boton-guardar">Crear producto</button>

                <hr class="separador">

                <a href="index.php" class="boton boton-volver">Volver al inicio</a>

            </form>

        </div>
    </div>

</body>
</html>