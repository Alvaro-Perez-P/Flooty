<?php


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

   .nav-left a{
    color: #97B770;
   }

  
  .nav-right a:hover {
    background: #97B770 ;
    color: white;
    border-radius: 6px ;
    border: solid 1px #97B770;
    transition: 0.23s;

  }

  .navbar-logo {
    font-weight: bold;
    font-size: 24px;
    color: #fff;
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
      <a href="sesion/login.php">Cerrar sesión</a>
  </div>

</nav>
