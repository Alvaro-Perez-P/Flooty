<?php
session_start();

error_reporting(E_ALL);
ini_set("display_errors", 1);

require_once __DIR__ . "/sesion/conexion.php";

$sql = "
SELECT 
    id,
    usuario,
    nombre,
    email,
    telefono,
    direccion,
    imagen,
    rol
FROM usuarios
ORDER BY id DESC
";

$resultado = $_conexion->query($sql);

?>

<!doctype html>
<html lang="es">

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Panel usuarios — Flooty</title>

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
            max-width: 1400px;
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
        }

        .btn-pausar {
            background: var(--verde-organico);
        }

        .btn-pausar:hover {
            background: var(--verde-profundo);
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

        .sin-usuarios {
            text-align: center;
            padding: 25px;
        }

        .acciones {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
        }

        .avatar-letra {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: #97B770;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 18px;
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

            <h2>Usuarios registrados</h2>
            <div>
             <a href="panel_admin.php" class="btn">
                <-Volver 
            </a>

        
            
</div>
        </div>

        <table class="tabla">

            <thead>

                <tr>
                    <th>ID</th>
                    <th>Avatar</th>
                    <th>Usuario</th>
                    <th>Nombre</th>
                    <th>Email</th>
                    <th>Teléfono</th>
                    <th>Dirección</th>
                    <th>Rol</th>
                    <th>Acciones</th>
                </tr>

            </thead>

            <tbody>

                <?php if ($resultado && $resultado->num_rows > 0): ?>

                    <?php while ($usuario = $resultado->fetch_assoc()): ?>

                        <tr>

                            <td>
                                <?php echo htmlspecialchars($usuario["id"]); ?>
                            </td>

                            <td>

                                <?php if (!empty($usuario["imagen"])): ?>

                                    <img src="<?php echo htmlspecialchars($usuario["imagen"] ?? ""); ?>" class="avatar">

                                <?php else: ?>

                                    <div class="avatar-letra">
                                        <?php echo strtoupper(substr($usuario["usuario"], 0, 1)); ?>
                                    </div>

                                <?php endif; ?>

                            </td>

                            <td>
                                <?php echo htmlspecialchars($usuario["usuario"]); ?>
                            </td>

                            <td>
                                <?php echo htmlspecialchars($usuario["nombre"]); ?>
                            </td>

                            <td>
                                <?php echo htmlspecialchars($usuario["email"]); ?>
                            </td>

                            <td>
                                <?php echo htmlspecialchars($usuario["telefono"] ?? ""); ?>
                            </td>

                            <td>
                                <?php echo htmlspecialchars($usuario["direccion"] ?? ""); ?>
                            </td>

                            <td>
                                <?php echo htmlspecialchars($usuario["rol"] ?? ""); ?>
                            </td>

                            <td>

                                <div class="acciones">

                                    <a href="editar_usuario.php?id=<?php echo $usuario["id"]; ?>" class="btn">
                                        Editar
                                    </a>

                                    <a href="pausar_usuario.php?id=<?php echo $usuario["id"]; ?>" class="btn btn-pausar">
                                        Pausar
                                    </a>

                                    <a href="eliminar_usuario.php?id=<?php echo $usuario["id"]; ?>" class="btn btn-danger"
                                        onclick="return confirm('¿Seguro que deseas eliminar este usuario?')">
                                        Eliminar
                                    </a>

                                </div>

                            </td>

                        </tr>

                    <?php endwhile; ?>

                <?php else: ?>

                    <tr>

                        <td colspan="9" class="sin-usuarios">
                            No hay usuarios registrados
                        </td>

                    </tr>

                <?php endif; ?>

            </tbody>

        </table>

    </main>

</body>

</html>