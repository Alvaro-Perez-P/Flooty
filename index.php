<?php
session_start();

error_reporting(E_ALL);
ini_set("display_errors", 1);

require 'sesion/conexion.php';


$esta_logueado = false;
$user_name = "";
$user_rol = "";
$id_usuario = 0;
$user_info = [];
$favoritos_usuario = [];

/* COMPROBAR SI ESTÁ LOGUEADO */
if (
    isset($_SESSION["usuario"]) &&
    isset($_SESSION["rol"]) &&
    ($_SESSION["rol"] == "usuario" || $_SESSION["rol"] == "admin")
) {
    $usuario_sesion_seguro = $_conexion->real_escape_string($_SESSION["usuario"]);

    $consulta_usuario = "SELECT * FROM usuarios WHERE usuario = '$usuario_sesion_seguro' LIMIT 1";
    $resultado_usuario = $_conexion->query($consulta_usuario);

    if ($resultado_usuario && $resultado_usuario->num_rows > 0) {
        $user_info = $resultado_usuario->fetch_assoc();

        if (!isset($user_info["activo"]) || (int)$user_info["activo"] === 1) {
            $esta_logueado = true;
            $user_name = $user_info["usuario"];
            $user_rol = $user_info["rol"];
            $id_usuario = (int)$user_info["id"];
        }
    }
}

/* AÑADIR / QUITAR FAVORITO */
if (isset($_GET["favorito"])) {

    if (!$esta_logueado) {
        header("location: sesion/index.php");
        exit();
    }

    $id_producto_favorito = (int)$_GET["favorito"];

    $consulta_favorito = "
        SELECT id 
        FROM favoritos 
        WHERE id_usuario = $id_usuario 
        AND id_producto = $id_producto_favorito
        LIMIT 1
    ";
    $resultado_favorito = $_conexion->query($consulta_favorito);

    if ($resultado_favorito && $resultado_favorito->num_rows > 0) {
        $_conexion->query("
            DELETE FROM favoritos 
            WHERE id_usuario = $id_usuario 
            AND id_producto = $id_producto_favorito
        ");
    } else {
        $_conexion->query("
            INSERT INTO favoritos (id_usuario, id_producto)
            VALUES ($id_usuario, $id_producto_favorito)
        ");
    }

    $params = $_GET;
    unset($params["favorito"]);
    $query = http_build_query($params);

    header("location: index.php" . ($query ? "?" . $query : ""));
    exit();
}

/* FILTROS */
$limite = isset($_GET["limite"]) ? (int)$_GET["limite"] : 6;

if ($limite < 6) {
    $limite = 6;
}

$siguiente_limite = $limite * 2;

$categoria = isset($_GET["categoria"]) ? (int)$_GET["categoria"] : 0;
$precio_orden = $_GET["precio"] ?? "";
$disponible_hoy = isset($_GET["disponible_hoy"]) ? 1 : 0;

$where = [];
$where[] = "p.estado = 'activo'";

if ($categoria > 0) {
    $where[] = "p.id_categoria = $categoria";
}

if ($disponible_hoy == 1) {
    $hoy = date("Y-m-d");

    $where[] = "
        p.id NOT IN (
            SELECT r.id_producto
            FROM reservas r
            WHERE r.estado IN ('pendiente', 'aceptada')
            AND r.fecha_inicio <= '$hoy'
            AND r.fecha_fin >= '$hoy'
        )
    ";
}

$where_sql = "WHERE " . implode(" AND ", $where);

$order_sql = "ORDER BY p.fecha_creacion DESC, p.id DESC";

if ($precio_orden == "menor") {
    $order_sql = "ORDER BY p.precio_dia ASC";
}

if ($precio_orden == "mayor") {
    $order_sql = "ORDER BY p.precio_dia DESC";
}

/* CATEGORÍAS */
$categorias = [];
$consulta_categorias = "SELECT id, nombre FROM categorias ORDER BY nombre ASC";
$resultado_categorias = $_conexion->query($consulta_categorias);

if ($resultado_categorias && $resultado_categorias->num_rows > 0) {
    while ($fila_categoria = $resultado_categorias->fetch_assoc()) {
        $categorias[] = $fila_categoria;
    }
}

/* TOTAL PRODUCTOS FILTRADOS */
$consulta_total = "
    SELECT COUNT(*) AS total 
    FROM productos p
    LEFT JOIN categorias c ON p.id_categoria = c.id
    $where_sql
";
$resultado_total = $_conexion->query($consulta_total);
$fila_total = $resultado_total->fetch_assoc();
$total_productos = (int)$fila_total["total"];

/* PRODUCTOS FILTRADOS */
$consulta_productos = "
    SELECT p.*, c.nombre AS nombre_categoria
    FROM productos p
    LEFT JOIN categorias c ON p.id_categoria = c.id
    $where_sql
    $order_sql
    LIMIT $limite
";
$resultado_productos = $_conexion->query($consulta_productos);

/* FAVORITOS DEL USUARIO */
if ($esta_logueado) {
    $consulta_favoritos_usuario = "
        SELECT id_producto 
        FROM favoritos 
        WHERE id_usuario = $id_usuario
    ";
    $resultado_favoritos_usuario = $_conexion->query($consulta_favoritos_usuario);

    if ($resultado_favoritos_usuario && $resultado_favoritos_usuario->num_rows > 0) {
        while ($fila_favorito = $resultado_favoritos_usuario->fetch_assoc()) {
            $favoritos_usuario[] = (int)$fila_favorito["id_producto"];
        }
    }
}

$params_mas = $_GET;
$params_mas["limite"] = $siguiente_limite;
$url_mas = "index.php?" . http_build_query($params_mas);

$avatar_usuario = "imagenes/avatar.jpg";

if ($esta_logueado && !empty($user_info["imagen"])) {
    $avatar_usuario = $user_info["imagen"];
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio - Flooty</title>
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
        .categorias-container,
        .filtros-container,
        .productos-container,
        .publicidad-container,
        .footer-container {
            background: rgba(245, 241, 230, 0.9);
            border-radius: 20px;
            border: 1px solid rgba(0, 0, 0, 0.1);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
        }

        .profile-info,
        .menu-container,
        .categorias-container,
        .filtros-container,
        .productos-container {
            padding: 20px;
        }

        .img-avatar {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            object-fit: cover;
            margin-right: 10px;
            background: white;
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
        }

        .btn {
            width: 100%;
            padding: 12px;
            font-size: 16px;
            font-weight: bold;
            border-radius: 6px;
            margin-top: 15px;
        }

        .btn-warning {
            background-color: #d8b24c;
            color: white;
            border: none;
        }

        .btn-warning:hover {
            background-color: #c49d35;
            color: white;
        }

        .titulo-bloque {
            font-size: 18px;
            color: #97B770;
            font-weight: bold;
            margin-bottom: 15px;
        }

        .categorias-select,
        .filtro-select {
            width: 100%;
            padding: 10px 12px;
            border-radius: 10px;
            border: 1px solid rgba(0, 0, 0, 0.15);
            background: #fff;
            color: #333;
            outline: none;
            margin-bottom: 10px;
        }

        .filtros-lista {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .filtro-item {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 14px;
            color: #333;
        }

        .filtro-item input[type="checkbox"] {
            accent-color: #97B770;
        }

        .btn-filtrar {
            width: 100%;
            background: #97B770;
            color: white;
            border: none;
            padding: 10px;
            border-radius: 10px;
            font-weight: bold;
            cursor: pointer;
        }

        .btn-limpiar {
            display: block;
            width: 100%;
            background: #8c8c8c;
            color: white;
            text-decoration: none;
            padding: 10px;
            border-radius: 10px;
            font-weight: bold;
            text-align: center;
        }

        .productos-container {
            flex: 1;
            min-height: 500px;
        }

        .titulo-seccion {
            font-size: 24px;
            color: #97B770;
            font-weight: bold;
            margin-bottom: 20px;
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
            cursor: pointer;
        }

        .tarjeta-producto:hover {
            transform: translateY(-4px);
        }

        .imagen-producto {
            width: 100%;
            height: 180px;
            background-color: #e8e3d8;
            overflow: hidden;
        }

        .imagen-producto img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .img-placeholder {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #e8e3d8;
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
            background: rgba(245, 241, 230, 0.95);
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.12);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 20px;
            transition: 0.2s;
            color: #666;
            text-decoration: none;
        }

        .btn-like:hover {
            transform: scale(1.08);
        }

        .btn-like.activo {
            background-color: #97B770;
            color: white;
        }

        .contenedor-boton-mas {
            display: flex;
            justify-content: center;
            margin-top: 25px;
        }

        .btn-mostrar-mas {
            min-width: 180px;
            padding: 12px 24px;
            border: none;
            border-radius: 10px;
            background-color: #97B770;
            color: white;
            font-size: 15px;
            font-weight: bold;
            cursor: pointer;
            transition: 0.3s;
            text-decoration: none;
            text-align: center;
        }

        .btn-mostrar-mas:hover {
            background-color: #7fa45a;
            color: white;
        }

        .mensaje-vacio {
            grid-column: 1 / -1;
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

        .footer-container {
            margin: 20px 10px 10px 10px;
            padding: 18px 25px;
        }

        .footer-contenido {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 20px;
            flex-wrap: wrap;
        }

        .footer-logo {
            font-size: 22px;
            font-weight: bold;
            color: #97B770;
        }

        .footer-texto {
            font-size: 14px;
            color: #666;
        }

        .footer-links {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }

        .footer-links a {
            text-decoration: none;
            color: #666;
            font-size: 14px;
            transition: 0.2s;
        }

        .footer-links a:hover {
            color: #97B770;
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
            }

            .footer-contenido {
                flex-direction: column;
                align-items: flex-start;
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
<?php include __DIR__ . "/chatbot_flooty_animado.php"; ?>

<div class="contenedor-main">

    <div class="columna-izquierda">

        <?php if ($esta_logueado): ?>
            <div class="profile-info">
                <div class="titulo-usuario">
                    <img class="img-avatar"
                         src="<?= htmlspecialchars($avatar_usuario) ?>"
                         alt="avatar"
                         onerror="this.src='imagenes/avatar.jpg'">
                    <?= htmlspecialchars($user_name) ?>
                </div>

                <div class="stars">
                    ★ ★ ★ ★ ★ <span>(35)</span>
                </div>

                <small>En Flooty desde 2026</small>
            </div>

            <div class="menu-container">
                <div class="list-group">
                    <a href="mis_reservas.php" class="list-group-item">Mis reservas</a>
                    <a href="reservas_recibidas.php" class="list-group-item">Reservas recibidas</a>
                    <a href="mis_productos.php" class="list-group-item">Mis anuncios</a>
                    <a href="mis_datos.php" class="list-group-item">Mis datos</a>
                    <a href="favoritos.php" class="list-group-item">Favoritos</a>
                </div>

                <?php if ($user_rol == "admin"): ?>
                    <a href="panel_admin.php" class="btn btn-warning">Panel Administrador</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <div class="categorias-container">
            <div class="titulo-bloque">Categorías</div>

            <form method="GET" action="index.php">
                <select name="categoria" class="categorias-select">
                    <option value="0">Todas las categorías</option>

                    <?php foreach ($categorias as $cat): ?>
                        <option value="<?= (int)$cat["id"] ?>" <?= $categoria == (int)$cat["id"] ? "selected" : "" ?>>
                            <?= htmlspecialchars($cat["nombre"]) ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <input type="hidden" name="precio" value="<?= htmlspecialchars($precio_orden) ?>">

                <?php if ($disponible_hoy): ?>
                    <input type="hidden" name="disponible_hoy" value="1">
                <?php endif; ?>

                <button type="submit" class="btn-filtrar">Filtrar categoría</button>
            </form>
        </div>

        <div class="filtros-container">
            <div class="titulo-bloque">Filtros</div>

            <form method="GET" action="index.php">
                <input type="hidden" name="categoria" value="<?= (int)$categoria ?>">

                <div class="filtros-lista">
                    <label class="filtro-item">
                        <input type="checkbox" name="disponible_hoy" value="1" <?= $disponible_hoy ? "checked" : "" ?>>
                        Disponible hoy
                    </label>

                    <select name="precio" class="filtro-select">
                        <option value="" <?= $precio_orden == "" ? "selected" : "" ?>>Precio</option>
                        <option value="menor" <?= $precio_orden == "menor" ? "selected" : "" ?>>Menor a mayor</option>
                        <option value="mayor" <?= $precio_orden == "mayor" ? "selected" : "" ?>>Mayor a menor</option>
                    </select>

                    <button type="submit" class="btn-filtrar">Aplicar filtros</button>
                    <a href="index.php" class="btn-limpiar">Limpiar filtros</a>
                </div>
            </form>
        </div>

    </div>

    <div class="productos-container">
        <div class="titulo-seccion">Últimos productos publicados</div>

        <div class="productos-grid">
            <?php if ($resultado_productos && $resultado_productos->num_rows > 0): ?>
                <?php while ($producto = $resultado_productos->fetch_assoc()): ?>
                    <?php
                    $imagenes = json_decode($producto["imagenes"], true);
                    $primera_imagen = "";

                    if (is_array($imagenes) && count($imagenes) > 0 && !empty($imagenes[0])) {
                        $primera_imagen = $imagenes[0];
                    }

                    $es_favorito = $esta_logueado && in_array((int)$producto["id"], $favoritos_usuario);

                    $params_fav = $_GET;
                    $params_fav["favorito"] = (int)$producto["id"];
                    $url_favorito = "index.php?" . http_build_query($params_fav);
                    ?>

                    <div class="tarjeta-producto" onclick="window.location='producto.php?id=<?= (int)$producto["id"] ?>'">
                        <div class="imagen-producto">
                            <?php if ($primera_imagen): ?>
                                <img src="<?= htmlspecialchars($primera_imagen) ?>"
                                     alt="<?= htmlspecialchars($producto["titulo"]) ?>"
                                     onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">

                                <div class="img-placeholder" style="display:none;">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#bbb" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                        <rect x="3" y="3" width="18" height="18" rx="2"/>
                                        <circle cx="8.5" cy="8.5" r="1.5"/>
                                        <polyline points="21 15 16 10 5 21"/>
                                    </svg>
                                </div>
                            <?php else: ?>
                                <div class="img-placeholder">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#bbb" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                        <rect x="3" y="3" width="18" height="18" rx="2"/>
                                        <circle cx="8.5" cy="8.5" r="1.5"/>
                                        <polyline points="21 15 16 10 5 21"/>
                                    </svg>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="contenido-producto">
                            <span class="categoria-producto">
                                <?= htmlspecialchars($producto["nombre_categoria"] ?? "Sin categoría") ?>
                            </span>

                            <h3><?= htmlspecialchars($producto["titulo"]) ?></h3>

                            <p><?= htmlspecialchars($producto["descripcion"]) ?></p>

                            <div class="precio">
                                <?= number_format((float)$producto["precio_dia"], 2, ",", ".") ?> €/día
                            </div>

                            <div class="acciones-producto">
                                <?php if ($esta_logueado): ?>
                                    <a href="<?= htmlspecialchars($url_favorito) ?>"
                                       class="btn-like <?= $es_favorito ? 'activo' : '' ?>"
                                       onclick="event.stopPropagation()">
                                        <?= $es_favorito ? '❤️' : '🤍' ?>
                                    </a>
                                <?php else: ?>
                                    <a href="sesion/login.php" class="btn-like" onclick="event.stopPropagation()">🤍</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                <?php endwhile; ?>
            <?php else: ?>
                <div class="mensaje-vacio">No hay productos con esos filtros.</div>
            <?php endif; ?>
        </div>

        <?php if ($total_productos > $limite): ?>
            <div class="contenedor-boton-mas">
                <a href="<?= htmlspecialchars($url_mas) ?>" class="btn-mostrar-mas">Cargar más</a>
            </div>
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

<footer class="footer-container">
    <div class="footer-contenido">
        <div class="footer-logo">FLOOTY</div>

        <div class="footer-texto">
            © 2026 Flooty. Plataforma de alquiler de objetos.
        </div>

        <div class="footer-links">
            <a href="#">Aviso legal</a>
            <a href="#">Privacidad</a>
            <a href="#">Contacto</a>
            <a href="#">Ayuda</a>
        </div>
    </div>
</footer>

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
