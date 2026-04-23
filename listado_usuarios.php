<?php
session_start();

error_reporting(E_ALL);
ini_set("display_errors", 1);

if (!isset($_SESSION["usuario"])) {
    header("location: sesion/login.php");
    exit();
}

if (!isset($_SESSION["rol"]) || $_SESSION["rol"] != "admin") {
    header("location: index.php");
    exit();
}

require "sesion/conexion.php";

// --- ELIMINAR USUARIO ---
if (isset($_GET["eliminar"])) {
    $id_eliminar = $_GET["eliminar"];
    $consulta_eliminar = "DELETE FROM usuarios WHERE id = '$id_eliminar'";
    $_conexion->query($consulta_eliminar);
    header("location: listado_usuarios.php");
    exit();
}

$consulta = "SELECT * FROM usuarios";
$resultado = $_conexion->query($consulta);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listado de usuarios</title>

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
            padding: 20px;
        }

        .contenedor {
            width: 100%;
            max-width: 1100px;
            margin: 40px auto;
            background: rgba(245, 241, 230, 0.95);
            border-radius: 20px;
            border: 1px solid rgba(0,0,0,0.1);
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
            padding: 40px;
        }

        .cabecera {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .cabecera h2 {
            font-size: 36px;
            font-weight: bold;
            color: #97B770;
        }

        .boton {
            padding: 10px 22px;
            border: none;
            font-size: 15px;
            font-weight: bold;
            border-radius: 6px;
            cursor: pointer;
            transition: 0.3s;
            text-decoration: none;
            display: inline-block;
        }

        .boton-crear {
            background-color: #A8CA7E;
            color: white;
        }

        .boton-crear:hover {
            background-color: #97B770;
            color: white;
        }

        .boton-editar {
            background-color: #d8b24c;
            color: white;
        }

        .boton-editar:hover {
            background-color: #c49d35;
            color: white;
        }

        .boton-eliminar {
            background-color: rgba(220, 53, 69, 0.92);
            color: white;
        }

        .boton-eliminar:hover {
            background-color: rgba(200, 35, 51, 0.95);
            color: white;
        }

        .boton-volver {
            background-color: #6c757d;
            color: white;
        }

        .boton-volver:hover {
            background-color: #5a6268;
            color: white;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        thead {
            background-color: #97B770;
            color: white;
        }

        thead th {
            padding: 14px 16px;
            text-align: center;
            font-size: 15px;
        }

        tbody tr {
            border-bottom: 1px solid rgba(0,0,0,0.08);
            transition: 0.2s;
        }

        tbody tr:hover {
            background-color: rgba(168, 202, 126, 0.12);
        }

        tbody td {
            padding: 12px 16px;
            text-align: center;
            font-size: 14px;
            color: #333;
        }

        .acciones {
            display: flex;
            gap: 8px;
            justify-content: center;
        }

        .pie {
            text-align: right;
        }

        @media(max-width: 768px) {
            .cabecera {
                flex-direction: column;
                gap: 15px;
                align-items: flex-start;
            }

            .cabecera h2 {
                font-size: 26px;
            }

            tbody td, thead th {
                padding: 10px 8px;
                font-size: 13px;
            }
        }
    </style>
</head>
<body>

<?php include __DIR__ . '/nav_basico.php'; ?>

<div class="contenedor">

    <div class="cabecera">
        <h2>Listado de usuarios</h2>
        <a href="sesion/registro_admin.php" class="boton boton-crear">+ Nuevo usuario</a>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Email</th>
                <th>Usuario</th>
                <th>Nombre</th>
                <th>Teléfono</th>
                <th>Dirección</th>
                <th>Rol</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php while($fila = $resultado->fetch_assoc()) { ?>
                <tr>
                    <td><?= $fila["id"] ?></td>
                    <td><?= htmlspecialchars($fila["email"]) ?></td>
                    <td><?= htmlspecialchars($fila["usuario"]) ?></td>
                    <td><?= htmlspecialchars($fila["nombre"] ?? "-") ?></td>
                    <td><?= htmlspecialchars($fila["telefono"] ?? "-") ?></td>
                    <td><?= htmlspecialchars($fila["direccion"] ?? "-") ?></td>
                    <td><?= htmlspecialchars($fila["rol"]) ?></td>
                    <td>
                        <div class="acciones">
                            <a href="editar_usuario.php?id=<?= $fila["id"] ?>" class="boton boton-editar">Editar</a>
                            <a href="listado_usuarios.php?eliminar=<?= $fila["id"] ?>"
                               class="boton boton-eliminar"
                               onclick="return confirm('¿Seguro que quieres eliminar este usuario?')">Eliminar</a>
                        </div>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>

    <div class="pie">
        <a href="index.php" class="boton boton-volver">Volver</a>
    </div>

</div>

</body>
</html>
