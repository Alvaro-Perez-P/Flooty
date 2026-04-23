<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <style>
         body{
            background-color: #F5F5DC;
        }
    </style>
</head>
<body>


    <!--Formulario de edicion de producto-->
    <div class="container d-flex justify-content-center align-items-center vh-100">
        <div class="bg-success p-4 rounded shadow" style="max-width: 400px; width: 100%;">
            <form method="post" enctype="multipart/form-data">

                <h3 class="text-center mb-3">Editar producto</h3>

                <!-- ID oculto (IMPORTANTE para PHP) -->
                <input type="hidden" name="id" value="">
                <br>
                <input type="text" name="nombre" class="form-control mb-2" placeholder="Nombre">
                <br>
                <textarea name="descripcion" class="form-control mb-2" placeholder="Descripción"></textarea>
                <br>
                <input type="number" name="precio" class="form-control mb-2" placeholder="Precio">
                <br>
                <select name="categoria" class="form-control mb-2">
                    <option selected disabled>---ELIGE LA CATEGORIA---</option>
                    <option value="electronica">Electrónica</option>
                    <option value="ropa">Ropa</option>
                    <option value="hogar">Hogar</option>
                    <option value="deporte">Deporte</option>
                </select>
                <br>
                <p style="color: white;">Selecciona hasta 5 imagenes</p>
                <input type="file" name="imagen" class="form-control mb-3">

                <button type="submit" name="actualizar" class="btn w-100" style="background-color: #8B4513; color: white;">Actualizar</button>
                 <hr>
                 <!--Boton volver a inicio-->
                <a href="index.php" class="btn w-100" style="background-color: grey; color: white;">Volver al inicio</a>

        </form>
    </div>    
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
</body>
</html>