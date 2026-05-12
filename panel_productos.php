<?php
session_start();

error_reporting(E_ALL);
ini_set("display_errors", 1);

require_once __DIR__ . "/sesion/conexion.php";

/* Pausar / activar producto */
if (isset($_GET["cambiar_estado"])) {
    $id_producto = (int)$_GET["cambiar_estado"];

    $consulta_estado = "SELECT estado FROM productos WHERE id = $id_producto LIMIT 1";
    $resultado_estado = $_conexion->query($consulta_estado);

    if ($resultado_estado && $resultado_estado->num_rows > 0) {
        $producto_estado = $resultado_estado->fetch_assoc();
        $estado_actual = $producto_estado["estado"] ?? "activo";
        $nuevo_estado = ($estado_actual == "activo") ? "pausado" : "activo";

        $_conexion->query("UPDATE productos SET estado = '$nuevo_estado' WHERE id = $id_producto");
    }

    header("location: panel_productos.php");
    exit();
}

/* Eliminar producto */
if (isset($_GET["eliminar"])) {
    $id_producto = (int)$_GET["eliminar"];
    $_conexion->query("DELETE FROM productos WHERE id = $id_producto");
    header("location: panel_productos.php");
    exit();
}

/* Productos reales */
$sql = "
SELECT 
    p.*,
    c.nombre AS nombre_categoria,
    u.usuario AS propietario
FROM productos p
LEFT JOIN categorias c ON p.id_categoria = c.id
LEFT JOIN usuarios u ON p.id_usuario = u.id
ORDER BY p.id DESC
";

$resultado = $_conexion->query($sql);
?>

<!doctype html>
<html lang="es">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Panel productos — Flooty</title>
<link rel="stylesheet" href="css/admin.css">

<style>
:root {
    --verde-hielo:#F7FEEF;
    --verde-natural:#A8CA7E;
    --verde-profundo:rgb(96,131,52);
}

* {
    margin:0;
    padding:0;
    box-sizing:border-box;
}

body {
    background:var(--verde-hielo);
    font-family:Arial, Helvetica, sans-serif;
}

.container {
    width:95%;
    max-width:1500px;
    margin:40px auto;
}

.top {
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:25px;
}

h2 {
    color:var(--verde-natural);
    font-size:32px;
}

.btn {
    display:inline-block;
    padding:9px 14px;
    border-radius:8px;
    text-decoration:none;
    background:var(--verde-natural);
    color:white;
    font-size:14px;
    transition:0.3s;
    border:none;
}

.btn:hover {
    background:var(--verde-profundo);
    color:white;
}

.btn-pausar {
    background:#d8b24c;
}

.btn-pausar:hover {
    background:#c49d35;
}

.btn-danger {
    background:#c0392b;
}

.btn-danger:hover {
    background:#e74c3c;
}

.tabla-wrapper {
    width:100%;
    overflow-x:auto;
    background:white;
    border-radius:12px;
    box-shadow:0 5px 15px rgba(0,0,0,0.08);
}

.tabla {
    width:100%;
    border-collapse:collapse;
    min-width:1300px;
}

.tabla th {
    background:var(--verde-natural);
    color:white;
    padding:14px;
    text-align:left;
    font-size:14px;
    white-space:nowrap;
}

.tabla td {
    padding:14px;
    border-bottom:1px solid #e5e7eb;
    color:#333;
    vertical-align:middle;
    font-size:14px;
    max-width:260px;
    word-break:break-word;
}

.tabla tr:hover {
    background:var(--verde-hielo);
}

.imagen {
    width:90px;
    height:70px;
    object-fit:cover;
    border-radius:10px;
    background:#eee;
}

.sin-imagen {
    width:90px;
    height:70px;
    border-radius:10px;
    background:#eee;
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:12px;
    color:#777;
}

.estado {
    padding:6px 12px;
    border-radius:20px;
    color:white;
    font-size:13px;
    display:inline-block;
}

.activo {
    background:#27ae60;
}

.pausado {
    background:#d8b24c;
}

.inactivo {
    background:#c0392b;
}

.acciones {
    display:flex;
    gap:8px;
    flex-wrap:wrap;
}

.sin-productos {
    text-align:center;
    padding:25px;
}

@media(max-width:768px) {
    .top {
        flex-direction:column;
        align-items:flex-start;
        gap:15px;
    }
}
</style>
</head>

<body>

<?php include __DIR__ . "/nav_basico.php"; ?>

<main class="container">

    <div class="top">
        <h2>Productos publicados</h2>
        <a href="crear_producto.php" class="btn">+ Crear producto</a>
    </div>

    <div class="tabla-wrapper">
        <table class="tabla">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Imagen</th>
                    <th>Título</th>
                    <th>Descripción</th>
                    <th>Categoría</th>
                    <th>Propietario</th>
                    <th>Precio/día</th>
                    <th>Fianza</th>
                    <th>Ciudad</th>
                    <th>Estado</th>
                    <th>Fecha creación</th>
                    <th>Acciones</th>
                </tr>
            </thead>

            <tbody>
            <?php if($resultado && $resultado->num_rows > 0): ?>
                <?php while($producto = $resultado->fetch_assoc()): ?>
                    <?php
                        $id = (int)($producto["id"] ?? 0);
                        $titulo = $producto["titulo"] ?? "";
                        $descripcion = $producto["descripcion"] ?? "";
                        $categoria = $producto["nombre_categoria"] ?? "Sin categoría";
                        $propietario = $producto["propietario"] ?? "Sin usuario";
                        $precio_dia = $producto["precio_dia"] ?? "";
                        $fianza = $producto["fianza"] ?? "";
                        $ciudad = $producto["ciudad"] ?? "";
                        $estado = strtolower($producto["estado"] ?? "activo");
                        $fecha_creacion = $producto["fecha_creacion"] ?? "";

                        $imagenes = json_decode($producto["imagenes"] ?? "[]", true);
                        $imagen_portada = "";

                        if (is_array($imagenes) && count($imagenes) > 0 && !empty($imagenes[0])) {
                            $imagen_portada = $imagenes[0];
                        }
                    ?>

                    <tr>
                        <td><?= htmlspecialchars((string)$id) ?></td>

                        <td>
                            <?php if($imagen_portada != ""): ?>
                                <img 
                                    src="<?= htmlspecialchars($imagen_portada) ?>" 
                                    class="imagen"
                                    alt="<?= htmlspecialchars($titulo) ?>"
                                    onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';"
                                >
                                <div class="sin-imagen" style="display:none;">Sin imagen</div>
                            <?php else: ?>
                                <div class="sin-imagen">Sin imagen</div>
                            <?php endif; ?>
                        </td>

                        <td><?= htmlspecialchars($titulo) ?></td>
                        <td><?= htmlspecialchars($descripcion) ?></td>
                        <td><?= htmlspecialchars($categoria) ?></td>
                        <td><?= htmlspecialchars($propietario) ?></td>

                        <td>
                            <?php if($precio_dia !== ""): ?>
                                <?= number_format((float)$precio_dia, 2, ",", ".") ?> €
                            <?php endif; ?>
                        </td>

                        <td>
                            <?php if($fianza !== ""): ?>
                                <?= number_format((float)$fianza, 2, ",", ".") ?> €
                            <?php endif; ?>
                        </td>

                        <td><?= htmlspecialchars($ciudad) ?></td>

                        <td>
                            <span class="estado <?= htmlspecialchars($estado) ?>">
                                <?= htmlspecialchars(ucfirst($estado)) ?>
                            </span>
                        </td>

                        <td><?= htmlspecialchars($fecha_creacion) ?></td>

                        <td>
                            <div class="acciones">
                                <a href="editar_producto_admin.php?id=<?= $id ?>" class="btn">Editar</a>

                                <a href="panel_productos.php?cambiar_estado=<?= $id ?>" class="btn btn-pausar">
                                    <?= $estado == "activo" ? "Pausar" : "Activar" ?>
                                </a>

                                <a 
                                    href="panel_productos.php?eliminar=<?= $id ?>" 
                                    class="btn btn-danger"
                                    onclick="return confirm('¿Seguro que deseas eliminar este producto?')"
                                >
                                    Eliminar
                                </a>
                            </div>
                        </td>
                    </tr>

                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="12" class="sin-productos">No hay productos publicados</td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>

</main>
</body>
</html>
