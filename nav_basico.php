<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
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
    padding: 6px 10px;
    border: solid 1px white;
  }

  .nav-left a {
    color: #97B770;
  }

  .nav-right a:hover {
    background: #97B770;
    color: white;
    border-radius: 6px;
    border: solid 1px #97B770;
    transition: 0.23s;
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
  }
</style>

<nav class="navbar">

  <div class="nav-left">
    <img class="logo" src="imagenes/logo-blanco.png" alt="logo flooty">
    <a href="index.php" class="navbar-logo">FLOOTY</a>
  </div>

<div class="nav-right">

<?php if (isset($_SESSION["usuario"])): ?>

    <span style="color:#97B770; font-weight:bold;">
      Hola <?= htmlspecialchars($_SESSION["usuario"]) ?>
    </span>

    <a href="crearProducto.php">Crear producto</a>
    <a href="sesion/logout.php">Cerrar sesión</a>

<?php else: ?>

    <a href="sesion/login.php">Iniciar sesión</a>
    <a href="sesion/registro.php">Registrarse</a>

<?php endif; ?>

</div>

</nav>