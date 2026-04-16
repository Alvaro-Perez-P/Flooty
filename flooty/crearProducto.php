<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar producto</title>

    <style>
        *{
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, Helvetica, sans-serif;
        }

        body{
            min-height: 100vh;
            background-color: #f5f1e6;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .contenedor-principal{
            width: 100%;
            max-width: 500px;
        }

        .caja-formulario{
            width: 100%;
            background: rgba(245, 241, 230, 0.88);
            border: 1px solid rgba(0,0,0,0.10);
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
            backdrop-filter: blur(4px);
            padding: 35px;
        }

        .caja-formulario h2{
            text-align: center;
            margin-bottom: 25px;
            color: #97B770;
            font-size: 34px;
        }

        .grupo-input{
            margin-bottom: 18px;
        }

        .grupo-input label{
            display: block;
            margin-bottom: 8px;
            color: #97B770;
            font-weight: bold;
            font-size: 15px;
        }

        .grupo-input input,
        .grupo-input textarea,
        .grupo-input select{
            width: 100%;
            padding: 12px 14px;
            border: 1px solid rgba(0,0,0,0.15);
            border-radius: 8px;
            font-size: 15px;
            outline: none;
            background: rgba(255,255,255,0.7);
            color: #333;
        }

        .grupo-input textarea{
            resize: vertical;
            min-height: 100px;
        }

        .grupo-input input:focus,
        .grupo-input textarea:focus,
        .grupo-input select:focus{
            border: 1px solid #97B770;
        }

        .texto-ayuda{
            color: #666;
            font-size: 14px;
            margin-bottom: 8px;
        }

        .boton{
            width: 100%;
            padding: 14px;
            border: none;
            border-radius: 8px;
            font-size: 18px;
            font-weight: bold;
            cursor: pointer;
            transition: 0.3s;
            text-decoration: none;
            display: block;
            text-align: center;
        }

        .boton-guardar{
            background-color: #97B770;
            color: white;
            margin-top: 10px;
        }

        .boton-guardar:hover{
            background-color: #7fa45a;
        }

        .boton-volver{
            background-color: #8c8c8c;
            color: white;
            margin-top: 12px;
        }

        .boton-volver:hover{
            background-color: #737373;
        }

        .separador{
            border: none;
            border-top: 1px solid rgba(0,0,0,0.1);
            margin: 18px 0;
        }

        @media(max-width: 600px){
            .caja-formulario{
                padding: 25px 20px;
            }

            .caja-formulario h2{
                font-size: 28px;
            }
        }
    </style>
</head>
<body>

    <div class="contenedor-principal">
        <div class="caja-formulario">

            <form method="post" action="" enctype="multipart/form-data">

                <h2>Editar producto</h2>

                <div class="grupo-input">
                    <label for="nombre">Nombre</label>
                    <input type="text" name="nombre" id="nombre" placeholder="Nombre del producto">
                </div>

                <div class="grupo-input">
                    <label for="descripcion">Descripción</label>
                    <textarea name="descripcion" id="descripcion" placeholder="Descripción del producto"></textarea>
                </div>

                <div class="grupo-input">
                    <label for="precio">Precio</label>
                    <input type="number" name="precio" id="precio" placeholder="Precio">
                </div>

                <div class="grupo-input">
                    <label for="categoria">Categoría</label>
                    <select name="categoria" id="categoria">
                        <option selected disabled>--- ELIGE LA CATEGORÍA ---</option>
                        <option value="electronica">Electrónica</option>
                        <option value="ropa">Ropa</option>
                        <option value="hogar">Hogar</option>
                        <option value="deporte">Deporte</option>
                    </select>
                </div>

                <div class="grupo-input">
                    <label for="imagen">Imagen</label>
                    <p class="texto-ayuda">Selecciona una imagen nueva si quieres cambiar la actual</p>
                    <input type="file" name="imagen" id="imagen">
                </div>

                <button type="submit" name="enviar" class="boton boton-guardar">Guardar cambios</button>

                <hr class="separador">

                <a href="index.php" class="boton boton-volver">Volver al inicio</a>

            </form>

        </div>
    </div>

</body>
</html>