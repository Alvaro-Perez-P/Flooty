<?php
session_start();

error_reporting(E_ALL);
ini_set("display_errors", 1);

if (!isset($_SESSION["usuario"])) {
    header("location: sesion/login.php");
    exit();
}

require "sesion/conexion.php";

/* Obtener usuario */
$usuario = $_conexion->real_escape_string($_SESSION["usuario"]);

$consulta_usuario = "SELECT id, rol FROM usuarios WHERE usuario = '$usuario' LIMIT 1";
$resultado_usuario = $_conexion->query($consulta_usuario);

if (!$resultado_usuario || $resultado_usuario->num_rows == 0) {
    die("Error: usuario no encontrado.");
}

$user = $resultado_usuario->fetch_assoc();
$id_usuario = (int)$user["id"];

/* Eliminar producto */
if (isset($_GET["eliminar"])) {
    $id_producto = (int)$_GET["eliminar"];

    $_conexion->query("DELETE FROM productos WHERE id = $id_producto AND id_usuario = $id_usuario");

    header("location: mis_productos.php");
    exit();
}

/* Pausar / activar producto */
if (isset($_GET["cambiar_estado"])) {
    $id_producto = (int)$_GET["cambiar_estado"];

    $consulta_estado = "SELECT estado FROM productos WHERE id = $id_producto AND id_usuario = $id_usuario LIMIT 1";
    $resultado_estado = $_conexion->query($consulta_estado);

    if ($resultado_estado && $resultado_estado->num_rows > 0) {
        $producto_estado = $resultado_estado->fetch_assoc();

        $estado_actual = $producto_estado["estado"] ?? "activo";
        $nuevo_estado = ($estado_actual == "activo") ? "pausado" : "activo";

        $_conexion->query("UPDATE productos SET estado = '$nuevo_estado' WHERE id = $id_producto AND id_usuario = $id_usuario");
    }

    header("location: mis_productos.php");
    exit();
}

/* Listar productos del usuario */
$consulta_productos = "
    SELECT p.*, c.nombre AS nombre_categoria
    FROM productos p
    LEFT JOIN categorias c ON p.id_categoria = c.id
    WHERE p.id_usuario = $id_usuario
    ORDER BY p.fecha_creacion DESC
";

$resultado_productos = $_conexion->query($consulta_productos);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mis productos</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, Helvetica, sans-serif;
        }

        body {
            background-color: #f5f1e6;
            min-height: 100vh;
        }

        .contenedor {
            width: 95%;
            max-width: 1100px;
            margin: 30px auto;
            background: rgba(245, 241, 230, 0.9);
            border-radius: 20px;
            border: 1px solid rgba(0,0,0,0.1);
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
            padding: 25px;
        }

        h1 {
            color: #97B770;
            margin-bottom: 25px;
            text-align: center;
        }

        .producto {
            display: grid;
            grid-template-columns: 120px 1fr auto;
            gap: 20px;
            align-items: center;
            background: white;
            border-radius: 16px;
            padding: 15px;
            margin-bottom: 15px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        }

        .producto img {
            width: 120px;
            height: 90px;
            object-fit: cover;
            border-radius: 12px;
            background: #ddd;
        }

        .producto-info h3 {
            color: #333;
            margin-bottom: 6px;
        }

        .producto-info p {
            color: #666;
            font-size: 14px;
            margin-bottom: 5px;
        }

        .categoria {
            display: inline-block;
            background: rgba(151, 183, 112, 0.18);
            color: #6f8f4e;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 12px;
            margin-bottom: 6px;
        }

        .precio {
            font-weight: bold;
            color: #97B770;
            font-size: 18px;
        }

        .estado {
            font-size: 13px;
            font-weight: bold;
            margin-top: 5px;
        }

        .estado.activo {
            color: green;
        }

        .estado.pausado {
            color: orange;
        }

        .acciones {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .btn-accion {
            text-decoration: none;
            padding: 9px 14px;
            border-radius: 8px;
            color: white;
            font-size: 14px;
            text-align: center;
            font-weight: bold;
            border: none;
            cursor: pointer;
            display: block;
        }

        .btn-editar {
            background: #97B770;
        }

        .btn-pausar {
            background: #d8b24c;
        }

        .btn-eliminar {
            background: #dc3545;
        }

        .btn-volver {
            display: inline-block;
            margin-top: 20px;
            background: #8c8c8c;
            color: white;
            padding: 12px 18px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: bold;
        }

        .mensaje-vacio {
            background: white;
            padding: 25px;
            border-radius: 14px;
            text-align: center;
            color: #666;
        }

        @media(max-width: 768px) {
            .producto {
                grid-template-columns: 1fr;
            }

            .producto img {
                width: 100%;
                height: 180px;
            }

            .acciones {
                flex-direction: row;
                flex-wrap: wrap;
            }
        }
    </style>
</head>

<body>

<?php include __DIR__ . "/nav_basico.php"; ?>

<div class="contenedor">
    <h1>Mis anuncios</h1>

    <?php if ($resultado_productos && $resultado_productos->num_rows > 0): ?>

        <?php while ($producto = $resultado_productos->fetch_assoc()): ?>
            <?php
                $imagenes = json_decode($producto["imagenes"] ?? "[]", true);
                $imagen = "imagenes/default.jpg";

                if (is_array($imagenes) && count($imagenes) > 0 && !empty($imagenes[0])) {
                    $imagen = $imagenes[0];
                }

                $estado = $producto["estado"] ?? "activo";
            ?>

            <div class="producto">
                <img src="<?= htmlspecialchars($imagen) ?>" alt="<?= htmlspecialchars($producto["titulo"]) ?>">

                <div class="producto-info">
                    <span class="categoria">
                        <?= htmlspecialchars($producto["nombre_categoria"] ?? "Sin categoría") ?>
                    </span>

                    <h3><?= htmlspecialchars($producto["titulo"]) ?></h3>

                    <p><?= htmlspecialchars($producto["descripcion"]) ?></p>

                    <div class="precio">
                        <?= number_format((float)$producto["precio_dia"], 2, ",", ".") ?> €/día
                    </div>

                    <div class="estado <?= htmlspecialchars($estado) ?>">
                        Estado: <?= htmlspecialchars($estado) ?>
                    </div>
                </div>

                <div class="acciones">
                    <a class="btn-accion btn-editar" href="editar_producto.php?id=<?= (int)$producto["id"] ?>">
                        Editar
                    </a>

                    <a class="btn-accion btn-pausar" href="mis_productos.php?cambiar_estado=<?= (int)$producto["id"] ?>">
                        <?= $estado == "activo" ? "Pausar" : "Activar" ?>
                    </a>

                    <a class="btn-accion btn-eliminar"
                       href="mis_productos.php?eliminar=<?= (int)$producto["id"] ?>"
                       onclick="return confirm('¿Seguro que quieres eliminar este producto?');">
                        Eliminar
                    </a>
                </div>
            </div>

        <?php endwhile; ?>

    <?php else: ?>
        <div class="mensaje-vacio">
            Todavía no tienes productos publicados.
        </div>
    <?php endif; ?>

    <a href="index.php" class="btn-volver">Volver al inicio</a>
</div>

</body>
</html>
