<?php
session_start();

// Vaciar todas las variables de sesión
$_SESSION = [];

// Destruir la sesión
session_destroy();

// Redirigir al index
header("Location: /flooty/sesion/login.php"); // en caso de que tengamos el boton de inicio de 
                               //  sesion en el index.php debe redireccionar ahi. 
exit();