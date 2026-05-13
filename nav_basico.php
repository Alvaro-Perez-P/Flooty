<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$notificaciones_pendientes = 0;

if (isset($_SESSION["usuario"])) {

    $usuario_sesion_seguro = $_conexion->real_escape_string($_SESSION["usuario"]);

    $consulta_notificaciones = "
        SELECT COUNT(*) AS total
        FROM notificaciones n
        INNER JOIN usuarios u ON n.id_usuario = u.id
        WHERE u.usuario = '$usuario_sesion_seguro'
        AND n.leida = 0
    ";

    $resultado_notificaciones = $_conexion->query($consulta_notificaciones);

    if ($resultado_notificaciones) {
        $fila_notificaciones = $resultado_notificaciones->fetch_assoc();
        $notificaciones_pendientes = (int)$fila_notificaciones["total"];
    }
}
?>

<style>

.navbar {
    width: 100%;
    background: white;
    padding: 14px 20px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    box-sizing: border-box;
    border-bottom: 1px solid #97B770;
    border-radius: 5px;
}

.nav-left,
.nav-right {
    display: flex;
    gap: 14px;
    align-items: center;
}

.nav-right a {
    color: rgb(96, 131, 52);
    text-decoration: none;
    font-size: 15px;
    padding: 8px 12px;
    border: solid 1px transparent;
    border-radius: 8px;
    transition: 0.23s;
}

.nav-left a {
    color: #97B770;
}

.nav-right a:hover {
    background: #97B770;
    color: white;
    border: solid 1px #97B770;
}

.navbar-logo {
    font-weight: bold;
    font-size: 24px;
    color: #97B770;
    text-decoration: none;
}

.logo {
    width: 50px;
    height: 50px;
    object-fit: contain;
}

.usuario-nav {
    color: #97B770;
    font-weight: bold;
    font-size: 15px;
}

.nav-notificaciones {
    position: relative;
    font-size: 20px !important;
    display: flex;
    align-items: center;
    justify-content: center;
}

.nav-notificaciones span {
    position: absolute;
    top: -5px;
    right: -5px;
    background: #ff4d4d;
    color: white;
    font-size: 11px;
    font-weight: bold;
    min-width: 18px;
    height: 18px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 2px;
    box-shadow: 0 2px 6px rgba(0,0,0,0.2);
}

@media (max-width: 768px) {

    .navbar {
        flex-direction: column;
        gap: 12px;
        padding: 14px;
    }

    .nav-left,
    .nav-right {
        width: 100%;
        justify-content: center;
        flex-wrap: wrap;
    }

    .usuario-nav {
        width: 100%;
        text-align: center;
    }
}

</style>

<nav class="navbar">

    <div class="nav-left">
        <img class="logo" src="imagenes/logo-blanco.png" alt="logo flooty">

        <a href="index.php" class="navbar-logo">
            FLOOTY
        </a>
    </div>

    <div class="nav-right">

        <?php if (isset($_SESSION["usuario"])): ?>

            <span class="usuario-nav">
                Hola <?= htmlspecialchars($_SESSION["usuario"]) ?>
            </span>

            <a href="notificaciones.php" class="nav-notificaciones">
                🔔

                <?php if ($notificaciones_pendientes > 0): ?>
                    <span><?= $notificaciones_pendientes ?></span>
                <?php endif; ?>
            </a>

            <a href="crearProducto.php">
                Crear producto
            </a>

            <a href="sesion/logout.php">
                Cerrar sesión
            </a>

        <?php else: ?>

            <a href="sesion/login.php">
                Iniciar sesión
            </a>

            <a href="sesion/registro.php">
                Registrarse
            </a>

        <?php endif; ?>

    </div>

</nav>