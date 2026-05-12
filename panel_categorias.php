<?php
session_start();

error_reporting(E_ALL);
ini_set("display_errors", 1);

require_once __DIR__ . "/sesion/conexion.php";

/* Eliminar categoría */
if (isset($_GET["eliminar"])) {
    $id_categoria = (int) $_GET["eliminar"];

    $_conexion->query("DELETE FROM categorias WHERE id = $id_categoria");

    header("location: panel_categorias.php");
    exit();
}

$sql = "SELECT * FROM categorias ORDER BY id DESC";
$resultado = $_conexion->query($sql);
?>

<!doctype html>
<html lang="es">

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Panel categorías — Flooty</title>

    <link rel="stylesheet" href="css/admin.css">

    <style>
        :root {
            --verde-hielo: #F7FEEF;
            --verde-suave: #CBDDB5;
            --verde-natural: #A8CA7E;
            --verde-organico: #97B770;
            --verde-profundo: rgb(96, 131, 52);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: var(--verde-hielo);
            font-family: Arial, Helvetica, sans-serif;
        }

        .container {
            width: 95%;
            max-width: 1000px;
            margin: 40px auto;
        }

        .top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }

        h2 {
            color: var(--verde-natural);
            font-size: 32px;
        }

        .btn {
            display: inline-block;
            padding: 10px 16px;
            border-radius: 8px;
            text-decoration: none;
            background: var(--verde-natural);
            color: white;
            font-size: 14px;
            transition: 0.3s;
            border: none;
        }

        .btn:hover {
            background: var(--verde-profundo);
            color: white;
        }

        .btn-danger {
            background: #c0392b;
        }

        .btn-danger:hover {
            background: #e74c3c;
        }

        .tabla {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
        }

        .tabla th {
            background: var(--verde-natural);
            color: white;
            padding: 16px;
            text-align: left;
            font-size: 15px;
        }

        .tabla td {
            padding: 16px;
            border-bottom: 1px solid #e5e7eb;
            color: #333;
            vertical-align: middle;
        }

        .tabla tr:hover {
            background: var(--verde-hielo);
        }

        .sin-categorias {
            text-align: center;
            padding: 25px;
        }

        .acciones {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        @media(max-width:768px) {
            .top {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }

            .tabla {
                display: block;
                overflow-x: auto;
            }
        }
    </style>

</head>

<body>

    <?php include __DIR__ . "/nav_basico.php"; ?>

    <main class="container">

        <div class="top">
            <h2>Categorías</h2>
            <div>
                <a href="crear_categoria.php" class="btn">
                    + Crear categoría
                </a>
                <a href="panel_admin.php" class="btn">
                    <-Volver </a>
            </div>
        </div>

        <table class="tabla">

            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Acciones</th>
                </tr>
            </thead>

            <tbody>

                <?php if ($resultado && $resultado->num_rows > 0): ?>

                    <?php while ($categoria = $resultado->fetch_assoc()): ?>

                        <tr>

                            <td>
                                <?= htmlspecialchars($categoria["id"] ?? "") ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($categoria["nombre"] ?? "") ?>
                            </td>

                            <td>
                                <div class="acciones">

                                    <a href="editar_categoria.php?id=<?= (int) $categoria["id"] ?>" class="btn">
                                        Editar
                                    </a>

                                    <a href="panel_categorias.php?eliminar=<?= (int) $categoria["id"] ?>" class="btn btn-danger"
                                        onclick="return confirm('¿Seguro que deseas eliminar esta categoría?')">
                                        Eliminar
                                    </a>

                                </div>
                            </td>

                        </tr>

                    <?php endwhile; ?>

                <?php else: ?>

                    <tr>
                        <td colspan="3" class="sin-categorias">
                            No hay categorías registradas
                        </td>
                    </tr>

                <?php endif; ?>

            </tbody>

        </table>

    </main>

</body>

</html>