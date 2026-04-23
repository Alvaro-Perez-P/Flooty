<?php
session_start();

error_reporting(E_ALL);
ini_set("display_errors", 1);

require "sesion/conexion.php";

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
$categoria = "";
$precio = "";
$fianza = "";
$ciudad = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $titulo = trim($_POST["titulo"] ?? "");
    $descripcion = trim($_POST["descripcion"] ?? "");
    $categoria = trim($_POST["categoria"] ?? "");
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

    if ($categoria == "") {
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

    if (!isset($_FILES["imagen"]) || $_FILES["imagen"]["name"] == "") {
        $err_imagen = "La imagen es obligatoria";
        $errores = true;
    }

    $nombre_imagen = "";

    if (!$errores) {
        $carpeta_destino = __DIR__ . "/imagenes/productos/";

        if (!is_dir($carpeta_destino)) {
            mkdir($carpeta_destino, 0777, true);
        }

        $nombre_original = $_FILES["imagen"]["name"];
        $tmp_imagen = $_FILES["imagen"]["tmp_name"];
        $nombre_imagen = time() . "_" . basename($nombre_original);
        $ruta_destino = $carpeta_destino . $nombre_imagen;

        if (!move_uploaded_file($tmp_imagen, $ruta_destino)) {
            $mensaje_error = "Error al subir la imagen";
            $errores = true;
        }
    }

    if (!$errores) {
        $imagen_bd = "imagenes/productos/" . $nombre_imagen;

        $consulta = "INSERT INTO productos (titulo, descripcion, categoria, precio_dia, fianza, ciudad, imagenes, fecha_creacion)
        VALUES ('$titulo', '$descripcion', '$categoria', '$precio', '$fianza', '$ciudad', '$imagen_bd', NOW())";

        if ($_conexion->query($consulta)) {
            $mensaje_exito = "Producto creado con éxito";

            $titulo = "";
            $descripcion = "";
            $categoria = "";
            $precio = "";
            $fianza = "";
            $ciudad = "";
        } else {
            $mensaje_error = "Error al crear el producto";
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
            padding: 20px;
        }

        .contenedor-principal{
            width: 100%;
            max-width: 500px;
        }

        .caja-formulario{
            width: 100%;
            background: rgba(245, 241, 230, 0.88);
            border: 1px solid rgba(0,0,0,0.10);
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
            backdrop-filter: blur(4px);
            padding: 35px;
        }

        .caja-formulario h2{
            text-align: center;
            margin-bottom: 25px;
            color: #97B770;
            font-size: 34px;
        }

        .grupo-input{
            margin-bottom: 18px;
        }

        .grupo-input label{
            display: block;
            margin-bottom: 8px;
            color: #97B770;
            font-weight: bold;
            font-size: 15px;
        }

        .grupo-input input,
        .grupo-input textarea,
        .grupo-input select{
            width: 100%;
            padding: 12px 14px;
            border: 1px solid rgba(0,0,0,0.15);
            border-radius: 8px;
            font-size: 15px;
            outline: none;
            background: rgba(255,255,255,0.7);
            color: #333;
        }

        .grupo-input textarea{
            resize: vertical;
            min-height: 100px;
        }

        .grupo-input input:focus,
        .grupo-input textarea:focus,
        .grupo-input select:focus{
            border: 1px solid #97B770;
        }

        .texto-ayuda{
            color: #666;
            font-size: 14px;
            margin-bottom: 8px;
        }

        .boton{
            width: 100%;
            padding: 14px;
            border: none;
            border-radius: 8px;
            font-size: 18px;
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
            margin-top: 10px;
        }

        .boton-guardar:hover{
            background-color: #7fa45a;
        }

        .boton-volver{
            background-color: #8c8c8c;
            color: white;
            margin-top: 12px;
        }

        .boton-volver:hover{
            background-color: #737373;
        }

        .separador{
            border: none;
            border-top: 1px solid rgba(0,0,0,0.1);
            margin: 18px 0;
        }

        .mensaje-error{
            background-color: rgba(220, 53, 69, 0.92);
            color: white;
            padding: 10px 12px;
            border-radius: 8px;
            margin-top: 8px;
            font-size: 14px;
        }

        .mensaje-exito{
            background-color: rgba(40, 167, 69, 0.92);
            color: white;
            padding: 12px 14px;
            border-radius: 8px;
            margin-bottom: 18px;
            font-size: 14px;
            text-align: center;
        }

        @media(max-width: 600px){
            .caja-formulario{
                padding: 25px 20px;
            }

            .caja-formulario h2{
                font-size: 28px;
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
                    <label for="categoria">Categoría</label>
                    <select name="categoria" id="categoria">
                        <option value="">--- ELIGE LA CATEGORÍA ---</option>
                        <option value="electronica" <?= $categoria == "electronica" ? "selected" : "" ?>>Electrónica</option>
                        <option value="ropa" <?= $categoria == "ropa" ? "selected" : "" ?>>Ropa</option>
                        <option value="hogar" <?= $categoria == "hogar" ? "selected" : "" ?>>Hogar</option>
                        <option value="deporte" <?= $categoria == "deporte" ? "selected" : "" ?>>Deporte</option>
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
                    <label for="imagen">Imagen</label>
                    <p class="texto-ayuda">Selecciona una imagen del producto</p>
                    <input type="file" name="imagen" id="imagen">
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