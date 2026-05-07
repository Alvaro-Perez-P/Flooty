<?php
session_start();

$id_producto = isset($_GET["producto"]) ? (int)$_GET["producto"] : 0;
$total = isset($_GET["total"]) ? (float)$_GET["total"] : 0;

if ($id_producto <= 0) {
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Pago simulado - Flooty</title>

    <style>
        body {
            font-family: Arial;
            background: #f5f1e6;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .card {
            background: white;
            padding: 30px;
            border-radius: 20px;
            width: 400px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, .15);
        }

        h1 {
            margin-bottom: 20px;
            color: #97B770;
        }

        input {
            width: 100%;
            padding: 12px;
            margin-bottom: 15px;
            border-radius: 10px;
            border: 1px solid #ccc;
        }

        button {
            width: 100%;
            padding: 14px;
            border: none;
            border-radius: 10px;
            background: #97B770;
            color: white;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
        }

        button:hover {
            background: #7fa45a;
        }

        .total {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 20px;
        }
    </style>
</head>

<body>

    <div class="card">

        <h1>💳 Pago seguro Flooty</h1>

        <div class="total">
            Total: <?= number_format($total, 2, ",", ".") ?> €
        </div>

        <form action="procesar_pago.php" method="POST">

            <input type="hidden" name="id_producto" value="<?= $id_producto ?>">
            <input type="hidden" name="total" value="<?= $total ?>">

            <input type="text" placeholder="Número de tarjeta" required>

            <input type="text" placeholder="MM/YY" required>

            <input type="text" placeholder="CVV" required>
            <input type="hidden" name="fecha_inicio" value="<?= htmlspecialchars($_GET["fecha_inicio"]) ?>">
            <input type="hidden" name="fecha_fin" value="<?= htmlspecialchars($_GET["fecha_fin"]) ?>">
            <input type="hidden" name="mensaje" value="<?= htmlspecialchars($_GET["mensaje"] ?? "") ?>">

            <button type="submit">
                Pagar ahora
            </button>

        </form>

    </div>

</body>

</html>