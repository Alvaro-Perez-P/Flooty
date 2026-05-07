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

$consulta = "SELECT * FROM usuarios";
$resultado = $_conexion->query($consulta);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listado de usuarios</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<h1 class="text-center mt-4">Bienvenido a FLOOTY</h1>

<div class="container mt-5">
    <h2 class="text-center mb-4">Listado de usuarios</h2>

    <table class="table table-bordered table-striped text-center">
        <thead>
            <tr>
                <th>ID</th>
                <th>Email</th>
                <th>Usuario</th>
                <th>Rol</th>
                <th>Editar</th>
            </tr>
        </thead>
        <tbody>
            <?php while($fila = $resultado->fetch_assoc()) { ?>
                <tr>
                    <td><?= $fila["id"] ?></td>
                    <td><?= htmlspecialchars($fila["email"]) ?></td>
                    <td><?= htmlspecialchars($fila["usuario"]) ?></td>
                    <td><?= htmlspecialchars($fila["rol"]) ?></td>
                    <td>
                        <a href="editar_usuario.php?id=<?= $fila["id"] ?>" class="btn btn-warning btn-sm">Editar</a>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>

    <div class="text-center">
        <a href="index.php" class="btn btn-secondary">Volver</a>
    </div>
</div>

</body>
</html>