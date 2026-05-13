<!--Datos de acceso
Usuario: admin
Email: admin@admin.com
Password: Admin123-->

<?php
session_start();

error_reporting(E_ALL);
ini_set("display_errors", 1);


?>
<!doctype html>
<html lang="es">
<head>
<meta charset="utf-8">
<title>Admin — Footy</title>
<link rel="stylesheet" href="css/admin.css">
</head>

<body>
  
<?php include __DIR__.'/nav_admin.php'; ?>
<main class="container">
  <h2>Panel administrador</h2>
  <div class="grid" style="grid-template-columns:1fr 1fr 1fr">
    <a class="card" href="panel_usuarios.php"><div class="p"><strong>Usuarios</strong></div></a>
    <a class="card" href="panel_anuncios.php"><div class="p"><strong>Anuncios</strong></div></a>
    <a class="card" href="panel_reservas.php"><div class="p"><strong>Reservas</strong></div></a>
    <a class="card" href="panel_categorias.php"><div class="p"><strong>Categorias</strong></div></a>
  </div>
  
</main>
</body>
</html>