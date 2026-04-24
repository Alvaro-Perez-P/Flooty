<?php
session_start();

error_reporting(E_ALL);
ini_set("display_errors", 1);

if (!isset($_SESSION["usuario"])) {
    header("location: sesion/login.php");
    exit();
}

if (!isset($_SESSION["rol"]) || ($_SESSION["rol"] != "usuario" && $_SESSION["rol"] != "admin")) {
    header("location: sesion/login.php");
    exit();
}

require 'sesion/conexion.php';

/* USUARIO */
$usuario_sesion = $_SESSION["usuario"];
$usuario_sesion_seguro = $_conexion->real_escape_string($usuario_sesion);

$consulta_usuario = "SELECT * FROM usuarios WHERE usuario = '$usuario_sesion_seguro' LIMIT 1";
$resultado_usuario = $_conexion->query($consulta_usuario);
$user_info = $resultado_usuario->fetch_assoc();
$user_name = $user_info["usuario"];
$user_rol = $user_info["rol"];
$id_usuario = (int)$user_info["id"];

/* QUITAR / PONER FAVORITO */
if (isset($_GET["favorito"])) {
    $id_producto_favorito = (int)$_GET["favorito"];

    $consulta_favorito = "SELECT * FROM favoritos 
                          WHERE id_usuario = $id_usuario 
                          AND id_producto = $id_producto_favorito";
    $resultado_favorito = $_conexion->query($consulta_favorito);

    if ($resultado_favorito && $resultado_favorito->num_rows > 0) {
        $consulta_borrar_favorito = "DELETE FROM favoritos 
                                     WHERE id_usuario = $id_usuario 
                                     AND id_producto = $id_producto_favorito";
        $_conexion->query($consulta_borrar_favorito);
    } else {
        $consulta_insertar_favorito = "INSERT INTO favoritos (id_usuario, id_producto)
                                      VALUES ($id_usuario, $id_producto_favorito)";
        $_conexion->query($consulta_insertar_favorito);
    }

    header("location: favoritos.php");
    exit();
}

/* FAVORITOS DEL USUARIO */
$favoritos_usuario = [];
$consulta_ids_favoritos = "SELECT id_producto FROM favoritos WHERE id_usuario = $id_usuario";
$resultado_ids_favoritos = $_conexion->query($consulta_ids_favoritos);

if ($resultado_ids_favoritos && $resultado_ids_favoritos->num_rows > 0) {
    while ($fila_favorito = $resultado_ids_favoritos->fetch_assoc()) {
        $favoritos_usuario[] = (int)$fila_favorito["id_producto"];
    }
}

/* PRODUCTOS FAVORITOS */
$productos_favoritos = false;

if (!empty($favoritos_usuario)) {
    $ids = implode(",", $favoritos_usuario);

    $consulta_productos = "
        SELECT p.*, c.nombre AS nombre_categoria
        FROM productos p
        LEFT JOIN categorias c ON p.id_categoria = c.id
        WHERE p.id IN ($ids)
        ORDER BY p.fecha_creacion DESC, p.id DESC
    ";

    $productos_favoritos = $_conexion->query($consulta_productos);
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Favoritos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, Helvetica, sans-serif;
        }

        body {
            min-height: 100vh;
            background-color: #f5f1e6;
            display: flex;
            flex-direction: column;
        }

        .contenedor-main {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 20px;
            padding: 20px 10px;
            flex: 1;
        }

        .columna-izquierda {
            width: 100%;
            max-width: 260px;
            display: flex;
            flex-direction: column;
            gap: 14px;
        }

        .profile-info,
        .menu-container,
        .productos-container,
        .publicidad-container {
            background: rgba(245, 241, 230, 0.9);
            border-radius: 20px;
            border: 1px solid rgba(0, 0, 0, 0.1);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
        }

        .profile-info,
        .menu-container,
        .productos-container {
            padding: 20px;
        }

        .img-avatar {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            object-fit: cover;
            margin-right: 10px;
            vertical-align: middle;
        }

        .titulo-usuario {
            color: black;
            font-weight: bold;
            font-size: 28px;
            margin-bottom: 6px;
            display: flex;
            align-items: center;
        }

        .stars {
            font-size: 12px;
            color: #ffb400;
            margin-bottom: 6px;
        }

        .stars span {
            color: #888;
        }

        .profile-info small {
            font-size: 12px;
            color: #777;
        }

        .list-group-item {
            display: block;
            text-decoration: none;
            background: transparent;
            border: none;
            border-bottom: 1px solid rgba(0, 0, 0, 0.1);
            padding: 14px 12px;
            font-size: 16px;
            color: #333;
            transition: 0.2s;
        }

        .list-group-item:hover {
            background: rgba(151, 183, 112, 0.15);
            color: #97B770;
            cursor: pointer;
        }

        .titulo-seccion {
            font-size: 24px;
            color: #97B770;
            font-weight: bold;
            margin-bottom: 20px;
        }

        .productos-container {
            flex: 1;
            min-height: 500px;
        }

        .productos-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
        }

        .tarjeta-producto {
            background: #fff;
            border-radius: 16px;
            border: 1px solid rgba(0, 0, 0, 0.08);
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            transition: 0.3s;
        }

        .tarjeta-producto:hover {
            transform: translateY(-4px);
        }

        .imagen-producto {
            width: 100%;
            height: 180px;
            background-color: #ddd;
            display: flex;
            justify-content: center;
            align-items: center;
            color: #777;
            font-size: 14px;
            font-weight: bold;
            overflow: hidden;
        }

        .imagen-producto img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .contenido-producto {
            padding: 15px;
        }

        .categoria-producto {
            display: inline-block;
            font-size: 12px;
            background-color: rgba(151, 183, 112, 0.18);
            color: #6f8f4e;
            padding: 4px 10px;
            border-radius: 20px;
            margin-bottom: 10px;
        }

        .contenido-producto h3 {
            font-size: 18px;
            margin-bottom: 8px;
            color: #333;
        }

        .contenido-producto p {
            font-size: 14px;
            color: #666;
            margin-bottom: 8px;
            min-height: 42px;
        }

        .precio {
            font-size: 18px;
            font-weight: bold;
            color: #97B770;
            margin-bottom: 14px;
        }

        .acciones-producto {
            display: flex;
            justify-content: flex-end;
            align-items: center;
        }

        .btn-like {
            width: 42px;
            height: 42px;
            border: none;
            border-radius: 50%;
            background-color: #97B770;
            color: white;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.12);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 20px;
            text-decoration: none;
        }

        .mensaje-vacio {
            background: #fff;
            border-radius: 16px;
            padding: 30px;
            text-align: center;
            color: #666;
        }

        .publicidad-container {
            width: 100%;
            max-width: 250px;
            height: 500px;
            position: relative;
            overflow: hidden;
            padding: 0;
            border-radius: 20px;
            flex-shrink: 0;
        }

        .slide {
            position: absolute;
            width: 100%;
            height: 100%;
            opacity: 0;
            transition: opacity 0.5s ease;
        }

        .slide img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .slide.active {
            opacity: 1;
        }

        @media (max-width: 992px) {
            .contenedor-main {
                flex-direction: column;
            }

            .columna-izquierda,
            .productos-container,
            .publicidad-container {
                max-width: 100%;
                width: 100%;
            }

            .publicidad-container {
                min-height: 200px;
                height: 200px;
            }
        }

        @media (max-width: 768px) {
            .productos-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 480px) {
            .productos-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>

    <?php include __DIR__ . '/nav_basico.php'; ?>

    <div class="contenedor-main">

        <div class="columna-izquierda">
            <div class="profile-info">
                <div class="titulo-usuario">
                    <img class="img-avatar" src="imagenes/avatar.jpg" alt="foto perfil">
                    <?= htmlspecialchars($_SESSION["usuario"]) ?>
                </div>

                <div class="stars">
                    ★ ★ ★ ★ ★ <span>(35)</span>
                </div>

                <small>En Flooty desde 2026</small>
            </div>

            <div class="menu-container">
                <div class="list-group">
                    <a href="index.php" class="list-group-item">Mis reservas</a>
                    <a href="index.php" class="list-group-item">Reservas recibidas</a>
                    <a href="index.php" class="list-group-item">Mis anuncios</a>
                    <a href="index.php" class="list-group-item">Mis datos</a>
                    <a href="favoritos.php" class="list-group-item">Favoritos</a>
                </div>
            </div>
        </div>

        <div class="productos-container">
            <div class="titulo-seccion">Mis favoritos</div>

            <?php if ($productos_favoritos && $productos_favoritos->num_rows > 0): ?>
                <div class="productos-grid">
                    <?php while ($producto = $productos_favoritos->fetch_assoc()): ?>
                        <?php
                        $imagenes = json_decode($producto["imagenes"], true);
                        $primera_imagen = "imagenes/default.jpg";

                        if (is_array($imagenes) && count($imagenes) > 0 && !empty($imagenes[0])) {
                            $primera_imagen = $imagenes[0];
                        }
                        ?>
                        <div class="tarjeta-producto">
                            <div class="imagen-producto">
                                <img src="<?= htmlspecialchars($primera_imagen) ?>" alt="<?= htmlspecialchars($producto["titulo"]) ?>">
                            </div>
                            <div class="contenido-producto">
                                <span class="categoria-producto">
                                    <?= htmlspecialchars($producto["nombre_categoria"] ?? "Sin categoría") ?>
                                </span>
                                <h3><?= htmlspecialchars($producto["titulo"]) ?></h3>
                                <p><?= htmlspecialchars($producto["descripcion"]) ?></p>
                                <div class="precio"><?= number_format((float)$producto["precio_dia"], 2, ",", ".") ?> €/día</div>
                                <div class="acciones-producto">
                                    <a href="favoritos.php?favorito=<?= $producto["id"] ?>" class="btn-like">👍</a>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <div class="mensaje-vacio">Todavía no tienes productos en favoritos.</div>
            <?php endif; ?>
        </div>

        <div class="publicidad-container">
            <div class="slide active">
                <img src="imagenes/banner1.png" alt="banner 1">
            </div>
            <div class="slide">
                <img src="imagenes/banner2.png" alt="banner 2">
            </div>
            <div class="slide">
                <img src="imagenes/banner3.png" alt="banner 3">
            </div>
        </div>

    </div>

    <script>
        let slides = document.querySelectorAll(".slide");
        let index = 0;

        setInterval(() => {
            slides[index].classList.remove("active");
            index = (index + 1) % slides.length;
            slides[index].classList.add("active");
        }, 2000);
    </script>

</body>
</html>