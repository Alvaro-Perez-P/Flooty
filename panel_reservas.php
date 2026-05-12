<?php
session_start();

error_reporting(E_ALL);
ini_set("display_errors", 1);

require_once __DIR__ . "/sesion/conexion.php";

/* RESERVAS */
$sql = "
SELECT 
    r.*,
    p.titulo AS producto,
    us.usuario AS solicitante,
    up.usuario AS propietario
FROM reservas r
LEFT JOIN productos p ON r.id_producto = p.id
LEFT JOIN usuarios us ON r.id_solicitante = us.id
LEFT JOIN usuarios up ON r.id_propietario = up.id
ORDER BY r.id DESC
";

$resultado = $_conexion->query($sql);

?>

<!doctype html>
<html lang="es">

<head>

<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Panel reservas — Flooty</title>

<link rel="stylesheet" href="css/admin.css">

<style>

:root {
    --verde-hielo: #F7FEEF;
    --verde-suave: #CBDDB5;
    --verde-natural: #A8CA7E;
    --verde-organico: #97B770;
    --verde-profundo: rgb(96, 131, 52);
}

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
}

body{
    background:var(--verde-hielo);
    font-family:Arial, Helvetica, sans-serif;
}

.container{
    width:95%;
    max-width:1600px;
    margin:40px auto;
}

.top{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:25px;
}

h2{
    color:var(--verde-natural);
    font-size:32px;
}

.tabla-wrapper{
    width:100%;
    overflow-x:auto;
    background:white;
    border-radius:12px;
    box-shadow:0 5px 15px rgba(0,0,0,0.08);
}

.tabla{
    width:100%;
    min-width:1500px;
    border-collapse:collapse;
}

.tabla th{
    background:var(--verde-natural);
    color:white;
    padding:16px;
    text-align:left;
    font-size:14px;
    white-space:nowrap;
}

.tabla td{
    padding:14px;
    border-bottom:1px solid #e5e7eb;
    color:#333;
    vertical-align:middle;
    font-size:14px;
}

.tabla tr:hover{
    background:var(--verde-hielo);
}

.estado{
    padding:6px 10px;
    border-radius:20px;
    font-size:13px;
    color:white;
    display:inline-block;
}

.pendiente{
    background:#f39c12;
}

.aceptada{
    background:#27ae60;
}

.rechazada{
    background:#c0392b;
}

.cancelada{
    background:#7f8c8d;
}

.finalizada{
    background:#2980b9;
}

.sin-reservas{
    text-align:center;
    padding:25px;
}

.btn{
    display:inline-block;
    padding:8px 14px;
    border-radius:8px;
    text-decoration:none;
    background:var(--verde-natural);
    color:white;
    font-size:14px;
    transition:0.3s;
}

.btn:hover{
    background:var(--verde-profundo);
    color:white;
}

.btn-danger{
    background:#c0392b;
}

.btn-danger:hover{
    background:#e74c3c;
}

.acciones{
    display:flex;
    gap:10px;
    flex-wrap:wrap;
}

.mensaje{
    max-width:250px;
    word-break:break-word;
}

@media(max-width:768px){

    .top{
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
        <h2>Reservas</h2>
         <a href="panel_admin.php" class="btn">
                <-Volver 
            </a>
    </div>

    <div class="tabla-wrapper">

        <table class="tabla">

            <thead>
                <tr>
                    <th>ID</th>
                    <th>Producto</th>
                    <th>Solicitante</th>
                    <th>Propietario</th>
                    <th>Fecha inicio</th>
                    <th>Fecha fin</th>
                    <th>Días</th>
                    <th>Precio/día</th>
                    <th>Fianza</th>
                    <th>Total</th>
                    <th>Estado</th>
                    <th>Mensaje</th>
                    <th>Fecha creación</th>
                    <th>Acciones</th>
                </tr>
            </thead>

            <tbody>

            <?php if($resultado && $resultado->num_rows > 0): ?>

                <?php while($reserva = $resultado->fetch_assoc()): ?>

                    <?php
                    $estado = strtolower($reserva["estado"] ?? "pendiente");
                    ?>

                    <tr>

                        <td>
                            <?= htmlspecialchars($reserva["id"] ?? "") ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($reserva["producto"] ?? "") ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($reserva["solicitante"] ?? "") ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($reserva["propietario"] ?? "") ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($reserva["fecha_inicio"] ?? "") ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($reserva["fecha_fin"] ?? "") ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($reserva["dias"] ?? "") ?>
                        </td>

                        <td>
                            <?php if(isset($reserva["precio_dia"])): ?>
                                <?= number_format((float)$reserva["precio_dia"], 2, ",", ".") ?> €
                            <?php endif; ?>
                        </td>

                        <td>
                            <?php if(isset($reserva["fianza"])): ?>
                                <?= number_format((float)$reserva["fianza"], 2, ",", ".") ?> €
                            <?php endif; ?>
                        </td>

                        <td>
                            <?php if(isset($reserva["total"])): ?>
                                <?= number_format((float)$reserva["total"], 2, ",", ".") ?> €
                            <?php endif; ?>
                        </td>

                        <td>

                            <span class="estado <?= htmlspecialchars($estado) ?>">
                                <?= htmlspecialchars(ucfirst($estado)) ?>
                            </span>

                        </td>

                        <td class="mensaje">
                            <?= htmlspecialchars($reserva["mensaje"] ?? "") ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($reserva["fecha_creacion"] ?? "") ?>
                        </td>

                        <td>

                            <div class="acciones">

                                <a 
                                    href="editar_reserva.php?id=<?= (int)$reserva["id"] ?>" 
                                    class="btn"
                                >
                                    Editar
                                </a>

                                <a 
                                    href="eliminar_reserva.php?id=<?= (int)$reserva["id"] ?>" 
                                    class="btn btn-danger"
                                    onclick="return confirm('¿Seguro que deseas eliminar esta reserva?')"
                                >
                                    Eliminar
                                </a>

                            </div>

                        </td>

                    </tr>

                <?php endwhile; ?>

            <?php else: ?>

                <tr>
                    <td colspan="14" class="sin-reservas">
                        No hay reservas registradas
                    </td>
                </tr>

            <?php endif; ?>

            </tbody>

        </table>

    </div>

</main>

</body>
</html>