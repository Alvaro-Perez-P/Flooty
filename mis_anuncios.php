PHP
<?php
session_start();

// Control de errores
error_reporting(E_ALL);
ini_set("display_errors", 1);

if (!isset($_SESSION["usuario"])) {
    header("location: sesion/login.php");
    exit();
}

require 'sesion/conexion.php';

// 1. Buscamos el ID del usuario actual usando su nombre de sesión
$user_session = $_SESSION["usuario"];
$consulta_user = "SELECT id FROM usuarios WHERE usuario = '$user_session'";
$res_user = $_conexion->query($consulta_user);
$user_data = $res_user->fetch_assoc();
$id_logueado = $user_data['id'];

// 2. Ahora filtramos los productos que pertenezcan a ese ID
// IMPORTANTE: Asegúrate de haber ejecutado el ALTER TABLE del paso anterior
$sql_productos = "SELECT id, titulo, descripcion, categoria, precio_dia, fianza, ciudad, imagenes, fecha_creacion 
                  FROM productos 
                  WHERE id_usuario = '$id_logueado' 
                  ORDER BY fecha_creacion DESC";

$resultado_productos = $_conexion->query($sql_productos);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Anuncios - Flooty</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, Helvetica, sans-serif;
        }

        body {
            min-height: 100vh;
            background-color: #f5f1e6; /* Tu beige clarito */
            padding: 20px;
        }

        .main-card {
            width: 100%;
            max-width: 1200px;
            margin: 60px auto 0 auto;
            background: rgba(245, 241, 230, 0.85); /* Beige transparente */
            padding: 40px;
            border-radius: 20px;
            border: 1px solid rgba(0,0,0,0.1);
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
            backdrop-filter: blur(4px);
        }

        h2 {
            font-size: 36px;
            margin-bottom: 30px;
            font-weight: bold;
            color: #97B770; /* Tu verde orgánico */
            text-align: center;
        }

        /* Tabla Estilizada */
        .table-container {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            border: 1px solid rgba(0,0,0,0.05);
        }

        .table {
            margin-bottom: 0;
        }

        .table thead {
            background-color: #A8CA7E; /* Tu verde natural */
            color: white;
        }

        .table th {
            padding: 15px;
            border: none;
            text-transform: uppercase;
            font-size: 13px;
        }

        .table td {
            padding: 15px;
            vertical-align: middle;
            border-bottom: 1px solid #f0f0f0;
        }

        /* Botones de acción compactos */
        .acciones-flex {
            display: flex;
            gap: 8px;
            justify-content: center;
        }

        .btn-action {
            width: 38px;
            height: 38px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            color: white;
            text-decoration: none;
            transition: 0.3s;
            border: none;
        }

        .btn-ver      { background-color: #CBDDB5; color: #608334; }
        .btn-editar   { background-color: #A8CA7E; }
        .btn-imagenes { background-color: #97B770; }
        .btn-pausar   { background-color: #d8b24c; } /* Tu color warning */
        .btn-eliminar { background-color: rgba(220, 53, 69, 0.9); }

        .btn-action:hover {
            transform: translateY(-3px);
            filter: brightness(90%);
            color: white;
        }

        .badge-cat {
            background-color: #f7feef;
            color: #608334;
            padding: 5px 10px;
            border-radius: 6px;
            font-weight: bold;
            font-size: 12px;
        }

        .price-text {
            font-weight: bold;
            color: #333;
        }

        .text-truncate-custom {
            max-width: 150px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
    </style>
</head>
<body>
    <?php include __DIR__.'/nav_basico.php'; ?>

    <div class="main-card">
        <h2>Mis Anuncios Publicados</h2>

        <div class="table-container shadow-sm">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Título</th>
                            <th>Descripción</th>
                            <th>Categoría</th>
                            <th>Precio/Día</th>
                            <th>Fianza</th>
                            <th>Ciudad</th>
                            <th>Estado</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($resultado_productos->num_rows > 0): ?>
                            <?php while($p = $resultado_productos->fetch_assoc()): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($p['titulo']); ?></strong></td>
                                <td class="text-truncate-custom" title="<?php echo htmlspecialchars($p['descripcion']); ?>">
                                    <?php echo htmlspecialchars($p['descripcion']); ?>
                                </td>
                                <td><span class="badge-cat"><?php echo htmlspecialchars($p['categoria']); ?></span></td>
                                <td class="price-text"><?php echo number_format($p['precio_dia'], 2); ?>€</td>
                                <td><?php echo number_format($p['fianza'], 2); ?>€</td>
                                <td><?php echo htmlspecialchars($p['ciudad']); ?></td>
                                <td><span style="color: #97B770; font-weight: bold;">● Activo</span></td>
                                <td>
                                    <div class="acciones-flex">
                                        <a href="ver.php?id=<?php echo $p['id']; ?>" class="btn-action btn-ver" title="Ver"><i class="fas fa-eye"></i></a>
                                        <a href="editar.php?id=<?php echo $p['id']; ?>" class="btn-action btn-editar" title="Editar"><i class="fas fa-edit"></i></a>
                                        <a href="imagenes.php?id=<?php echo $p['id']; ?>" class="btn-action btn-imagenes" title="Imágenes"><i class="fas fa-images"></i></a>
                                        <a href="pausar.php?id=<?php echo $p['id']; ?>" class="btn-action btn-pausar" title="Pausar"><i class="fas fa-pause"></i></a>
                                        <a href="eliminar.php?id=<?php echo $p['id']; ?>" class="btn-action btn-eliminar" title="Eliminar" onclick="return confirm('¿Borrar anuncio?')">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted">Aún no has publicado ningún anuncio.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>